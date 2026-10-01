<?php
// api/listar_processos.php
session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    require_once '../../api/conexao.php';

    // Verificar se o utilizador está autenticado e obter o seu perfil e ID
    $perfilId = isset($_SESSION['user_perfil']) ? intval($_SESSION['user_perfil']) : 0;
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

    if ($userId === 0) {
        throw new Exception("Sessão inválida ou expirada.");
    }

    // Se o perfil_id não estiver na sessão, buscamo-lo diretamente na base de dados pela tabela utilizadores
    if ($perfilId === 0 && $userId > 0) {
        $stmtU = $conn->prepare("SELECT perfil_id FROM utilizadores WHERE id = ? LIMIT 1");
        if ($stmtU) {
            $stmtU->bind_param("i", $userId);
            $stmtU->execute();
            $resU = $stmtU->get_result()->fetch_assoc();
            if ($resU) {
                $perfilId = intval($resU['perfil_id']);
                $_SESSION['user_perfil'] = $perfilId; // Atualiza a sessão
            }
            $stmtU->close();
        }
    }

    // Verificar se o utilizador logado é Administrador ou Gerente/Gestor
    $isAdminOrGerente = false;

    // 🔥 EXCEÇÃO PARA A EQUIPA TÉCNICA / SUPORTE (ID -999)
    if ($userId === -999) {
        $isAdminOrGerente = true;
    } elseif ($perfilId > 0) {
        $stmtPerfil = $conn->prepare("SELECT nome FROM perfis WHERE id = ? LIMIT 1");
        if ($stmtPerfil) {
            $stmtPerfil->bind_param("i", $perfilId);
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

    // Parâmetros de entrada (GET)
    $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $limite = 8; // Itens por página
    $offset = ($pagina - 1) * $limite;

    $tecnicoFiltro = isset($_GET['tecnico_id']) && $_GET['tecnico_id'] !== '' ? intval($_GET['tecnico_id']) : null;
    $busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
    $filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : 'todos'; // 🔥 Adicionado filtro de estado

    // Regra base de restrição de visualização
    $whereClauses = [];
    $params = [];
    $types = '';

    // Se NÃO for admin nem gerente, restringe estritamente aos processos do técnico logado
    if (!$isAdminOrGerente) {
        $whereClauses[] = "p.tecnico_responsavel_id = ?";
        $params[] = $userId;
        $types .= 'i';
    }

    // 1. Obter métrica do dia (entradas hoje) aplicando a mesma restrição de visibilidade
    $whereEntradasHoje = ["DATE(criado_em) = CURDATE()"];
    $paramsEntradas = [];
    $typesEntradas = "";

    if (!$isAdminOrGerente) {
        $whereEntradasHoje[] = "tecnico_responsavel_id = ?";
        $paramsEntradas[] = $userId;
        $typesEntradas .= "i";
    }

    $sqlEntradasHoje = "SELECT COUNT(*) as total FROM processos WHERE " . implode(" AND ", $whereEntradasHoje);
    $stmtEntradas = $conn->prepare($sqlEntradasHoje);
    if (!empty($paramsEntradas)) {
        $stmtEntradas->bind_param($typesEntradas, ...$paramsEntradas);
    }
    $stmtEntradas->execute();
    $resEntradas = $stmtEntradas->get_result()->fetch_assoc();
    $entradasHoje = $resEntradas ? $resEntradas['total'] : 0;
    $stmtEntradas->close();

    // 2. Obter lista de técnicos com a respetiva contagem de processos para os botões do topo
    $tecnicosFiltros = [];
    if ($isAdminOrGerente) {
        $sqlTecnicosContagem = "
            SELECT u.id, u.nome, 
                   (SELECT COUNT(*) FROM processos p WHERE p.tecnico_responsavel_id = u.id) as total_processos
            FROM utilizadores u
            WHERE u.estado = 'ativo'
            ORDER BY u.nome ASC
        ";
        $resTecnicos = $conn->query($sqlTecnicosContagem);
        if ($resTecnicos) {
            while ($t = $resTecnicos->fetch_assoc()) {
                $tecnicosFiltros[] = $t;
            }
        }
    } else {
        $stmtTecnicoUnico = $conn->prepare("
            SELECT u.id, u.nome, 
                   (SELECT COUNT(*) FROM processos p WHERE p.tecnico_responsavel_id = u.id) as total_processos
            FROM utilizadores u
            WHERE u.id = ? LIMIT 1
        ");
        if ($stmtTecnicoUnico) {
            $stmtTecnicoUnico->bind_param("i", $userId);
            $stmtTecnicoUnico->execute();
            $resT = $stmtTecnicoUnico->get_result()->fetch_assoc();
            if ($resT) {
                $tecnicosFiltros[] = $resT;
            }
            $stmtTecnicoUnico->close();
        }
    }

    // 3. Construção dinâmica da consulta principal de processos
    if ($tecnicoFiltro !== null && $isAdminOrGerente) {
        $whereClauses[] = "p.tecnico_responsavel_id = ?";
        $params[] = $tecnicoFiltro;
        $types .= 'i';
    }

    if (!empty($busca)) {
        $whereClauses[] = "(p.num_processo LIKE ? OR p.nome_arguido LIKE ? OR u.nome LIKE ?)";
        $searchTerm = "%{$busca}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sss';
    }

    // 🔥 Adicionar regra de filtragem por estado (finalizados ou pendentes)
    if ($filtro === 'finalizados') {
        $whereClauses[] = "EXISTS (SELECT 1 FROM auto_interrogatorios ai_f WHERE ai_f.processo_id = p.id AND ai_f.estado_auto = 'finalizado')";
    } elseif ($filtro === 'pendentes') {
        $whereClauses[] = "NOT EXISTS (SELECT 1 FROM auto_interrogatorios ai_p WHERE ai_p.processo_id = p.id AND ai_p.estado_auto = 'finalizado')";
    }

    $sqlWhere = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

    // Contagem total para paginação
    $sqlCount = "
        SELECT COUNT(p.id) as total 
        FROM processos p 
        LEFT JOIN utilizadores u ON p.tecnico_responsavel_id = u.id 
        {$sqlWhere}
    ";
    $stmtCount = $conn->prepare($sqlCount);
    if (!empty($params)) {
        $stmtCount->bind_param($types, ...$params);
    }
    $stmtCount->execute();
    $totalRegistos = $stmtCount->get_result()->fetch_assoc()['total'];
    $totalPaginas = max(1, ceil($totalRegistos / $limite));
    $stmtCount->close();

    // Buscar dados paginados
    $sqlProcessos = "
        SELECT p.id, p.num_processo, p.nome_arguido, p.estado_processo, p.criado_em,
               u.nome as nome_tecnico,
               uc.nome as nome_criador
        FROM processos p
        LEFT JOIN utilizadores u ON p.tecnico_responsavel_id = u.id
        LEFT JOIN utilizadores uc ON p.criado_por_id = uc.id
        {$sqlWhere}
        ORDER BY p.id DESC
        LIMIT ? OFFSET ?
    ";
    
    // Adicionar parâmetros de limite e offset
    $paramsList = $params;
    $paramsList[] = $limite;
    $paramsList[] = $offset;
    $typesList = $types . 'ii';

    $stmtList = $conn->prepare($sqlProcessos);
    if (!empty($paramsList)) {
        $stmtList->bind_param($typesList, ...$paramsList);
    }
    $stmtList->execute();
    $resultList = $stmtList->get_result();

    $processos = [];
    while ($row = $resultList->fetch_assoc()) {
        $processos[] = $row;
    }
    $stmtList->close();

    echo json_encode([
        'success' => true,
        'entradas_hoje' => intval($entradasHoje),
        'tecnicos_filtros' => $tecnicosFiltros,
        'processos' => $processos,
        'paginacao' => [
            'pagina_atual' => $pagina,
            'total_paginas' => $totalPaginas,
            'total_registos' => intval($totalRegistos)
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar dados: ' . $e->getMessage()
    ]);
}