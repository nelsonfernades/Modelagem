<?php
// 🔥 Impede que avisos ou erros do PHP corrompam o formato JSON da resposta
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once '../../api/conexao.php'; // Ajuste o caminho se necessário
    require_once '../../api/session.php'; // Essencial para capturar a sessão

    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Falha na ligação com a base de dados.");
    }

    $userId = $_SESSION['user_id'] ?? null;
    $perfilId = $_SESSION['user_perfil'] ?? null;

    $userIdInt = intval($userId);
    $perfilIdInt = intval($perfilId);

    // Verifica se o utilizador logado é Administrador, Gerente ou Equipa Técnica (-999)
    $isAdminOrGerente = false;

    if ($userIdInt === -999) {
        $isAdminOrGerente = true;
    } elseif ($perfilIdInt > 0) {
        $stmtPerfil = $conn->prepare("SELECT nome FROM perfis WHERE id = ? LIMIT 1");
        if ($stmtPerfil) {
            $stmtPerfil->bind_param("i", $perfilIdInt);
            $stmtPerfil->execute();
            $resPerfil = $stmtPerfil->get_result()->fetch_assoc();
            if ($resPerfil) {
                $nomePerfil = strtolower(trim($resPerfil['nome']));
                if (strpos($nomePerfil, 'admin') !== false || strpos($nomePerfil, 'gerente') !== false || strpos($nomePerfil, 'gestor') !== false) {
                    $isAdminOrGerente = true;
                }
            }
            $stmtPerfil->close();
        }
    }

    // Fallback opcional caso o cargo venha direto na tabela de utilizadores
    if (!$isAdminOrGerente && $userIdInt > 0) {
        $stmtUser = $conn->prepare("SELECT cargo, tipo FROM utilizadores WHERE id = ? LIMIT 1");
        if ($stmtUser) {
            $stmtUser->bind_param("i", $userIdInt);
            $stmtUser->execute();
            $resUser = $stmtUser->get_result()->fetch_assoc();
            if ($resUser) {
                $cargoTexto = strtolower(trim($resUser['cargo'] ?? $resUser['tipo'] ?? ''));
                if (strpos($cargoTexto, 'admin') !== false || strpos($cargoTexto, 'gerente') !== false) {
                    $isAdminOrGerente = true;
                }
            }
            $stmtUser->close();
        }
    }

    $filtro = $_GET['filtro'] ?? 'total';
    $busca = trim($_GET['busca'] ?? '');
    $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
    $limite = 8; // Quantos itens por página
    $offset = ($pagina - 1) * $limite;

    $whereClauses = [];
    $params = [];
    $types = '';

    // Restrição de segurança
    if (!$isAdminOrGerente && $userIdInt > 0) {
        $whereClauses[] = "p.tecnico_responsavel_id = ?";
        $params[] = $userIdInt;
        $types .= 'i';
    }

    if ($busca !== '') {
        $whereClauses[] = "(p.num_processo LIKE ? OR a.nome LIKE ? OR a.tipo_crime LIKE ?)";
        $searchTerm = "%$busca%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sss';
    }

    if ($filtro === 'finalizados') {
        $whereClauses[] = "ai.estado_auto = 'finalizado'";
    } elseif ($filtro === 'pendentes') {
        $whereClauses[] = "ai.estado_auto = 'rascunho'";
    }

    $whereSql = count($whereClauses) > 0 ? "WHERE " . implode(' AND ', $whereClauses) : "";

    // 1. Contagem total para paginação
    $countSql = "SELECT COUNT(DISTINCT p.id) as total 
                 FROM processos p 
                 INNER JOIN arguidos a ON a.processo_id = p.id 
                 INNER JOIN auto_interrogatorios ai ON ai.processo_id = p.id 
                 $whereSql";

    $stmtCount = $conn->prepare($countSql);
    if (!$stmtCount) {
        throw new Exception("Erro ao preparar consulta de contagem: " . $conn->error);
    }
    if (!empty($params)) {
        $stmtCount->bind_param($types, ...$params);
    }
    $stmtCount->execute();
    $totalRegistos = $stmtCount->get_result()->fetch_assoc()['total'];
    $totalPaginas = max(1, ceil($totalRegistos / $limite));
    $stmtCount->close();

    // 2. Buscar dados mapeando as colunas reais
    $sql = "SELECT p.id as processo_id_real, p.num_processo as numero_processo, p.estado_processo,
                   a.id as arguido_id_real, a.nome as arguido_nome, a.bi as nif, a.contacto as nip, 
                   a.tipo_crime as crime, u.nome as tecnico_nome, 
                   ai.estado_auto as estado, ai.criado_em 
            FROM processos p 
            INNER JOIN arguidos a ON a.processo_id = p.id 
            INNER JOIN auto_interrogatorios ai ON ai.processo_id = p.id 
            LEFT JOIN utilizadores u ON p.tecnico_responsavel_id = u.id 
            $whereSql 
            ORDER BY p.criado_em DESC 
            LIMIT ? OFFSET ?";

    $paramsList = $params;
    $paramsList[] = $limite;
    $paramsList[] = $offset;
    $typesList = $types . 'ii';

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro ao preparar consulta de listagem: " . $conn->error);
    }
    if (!empty($paramsList)) {
        $stmt->bind_param($typesList, ...$paramsList);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();

    $processos = [];
    while ($row = $resultado->fetch_assoc()) {
        $processos[] = $row;
    }
    $stmt->close();

    // Contadores rápidos do painel
    $userRestrictionSql = (!$isAdminOrGerente && $userIdInt > 0) ? " AND p.tecnico_responsavel_id = " . $userIdInt : "";

    $sqlTotal = "SELECT COUNT(DISTINCT p.id) as c FROM processos p INNER JOIN arguidos a ON a.processo_id = p.id INNER JOIN auto_interrogatorios ai ON ai.processo_id = p.id WHERE 1=1" . $userRestrictionSql;
    $sqlFin = "SELECT COUNT(DISTINCT p.id) as c FROM processos p INNER JOIN arguidos a ON a.processo_id = p.id INNER JOIN auto_interrogatorios ai ON ai.processo_id = p.id WHERE ai.estado_auto = 'finalizado'" . $userRestrictionSql;
    $sqlPend = "SELECT COUNT(DISTINCT p.id) as c FROM processos p INNER JOIN arguidos a ON a.processo_id = p.id INNER JOIN auto_interrogatorios ai ON ai.processo_id = p.id WHERE ai.estado_auto = 'rascunho'" . $userRestrictionSql;

    echo json_encode([
        'sucesso' => true,
        'processos' => $processos,
        'pagina_atual' => $pagina,
        'total_paginas' => intval($totalPaginas),
        'total_geral' => intval($conn->query($sqlTotal)->fetch_assoc()['c'] ?? 0),
        'total_finalizados' => intval($conn->query($sqlFin)->fetch_assoc()['c'] ?? 0),
        'total_pendentes' => intval($conn->query($sqlPend)->fetch_assoc()['c'] ?? 0)
    ]);

} catch (Exception $e) {
    // Retorna o erro formatado em JSON limpo para o Javascript capturar sem crashar
    echo json_encode([
        'sucesso' => false,
        'message' => $e->getMessage()
    ]);
}
?>