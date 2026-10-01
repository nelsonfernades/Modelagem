<?php
// listar_utilizadores.php
header('Content-Type: application/json; charset=utf-8');
require_once '../../api/conexao.php'; // Ficheiro de conexão MySQL ($conn)

try {
    // Captura dos filtros via GET
    $busca   = isset($_GET['busca']) ? trim($_GET['busca']) : '';
    $perfil  = isset($_GET['perfil']) && $_GET['perfil'] !== '' ? intval($_GET['perfil']) : null;
    $estado  = isset($_GET['estado']) && $_GET['estado'] !== '' ? trim($_GET['estado']) : null;
    $pagina  = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $limite  = 6; // Quantidade de cards por página
    $offset  = ($pagina - 1) * $limite;

    // Construção das condições SQL dinâmicas
    $where = ["1=1"];
    $params = [];
    $types  = "";

    if (!empty($busca)) {
        $where[] = "(u.nome LIKE ? OR u.email LIKE ? OR u.nip LIKE ? OR p.nome LIKE ?)";
        $searchTerm = "%{$busca}%";
        array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $types .= "ssss";
    }

    if ($perfil !== null) {
        $where[] = "u.perfil_id = ?";
        $params[] = $perfil;
        $types .= "i";
    }

    if ($estado !== null) {
        $where[] = "u.estado = ?";
        $params[] = $estado;
        $types .= "s";
    }

    $sqlWhere = implode(" AND ", $where);

    // 1. Contagem total de registos (para a paginação)
    $sqlCount = "SELECT COUNT(*) as total FROM utilizadores u INNER JOIN perfis p ON u.perfil_id = p.id WHERE {$sqlWhere}";
    $stmtCount = $conn->prepare($sqlCount);
    if (!empty($types)) {
        $stmtCount->bind_param($types, ...$params);
    }
    $stmtCount->execute();
    $totalRegistos = $stmtCount->get_result()->fetch_assoc()['total'];
    $stmtCount->close();

    $totalPaginas = ceil($totalRegistos / $limite);

    // 2. Consulta dos dados paginados
    $sqlData = "SELECT 
                    u.id, u.nome, u.email, u.nip, u.telefone, u.estado, 
                    u.qr_code_passe, u.foto_url, u.perfil_id,
                    p.nome AS perfil_nome
                FROM utilizadores u 
                INNER JOIN perfis p ON u.perfil_id = p.id 
                WHERE {$sqlWhere} 
                ORDER BY u.id DESC 
                LIMIT ? OFFSET ?";

    $stmtData = $conn->prepare($sqlData);
    
    // Adiciona limites aos parâmetros
    $paramsWithLimit = $params;
    $paramsWithLimit[] = $limite;
    $paramsWithLimit[] = $offset;
    $typesWithLimit = $types . "ii";

    $stmtData->bind_param($typesWithLimit, ...$paramsWithLimit);
    $stmtData->execute();
    $result = $stmtData->get_result();

    $utilizadores = [];
    while ($row = $result->fetch_assoc()) {
        $utilizadores[] = $row;
    }
    $stmtData->close();

    // Resposta final JSON
    echo json_encode([
        'success' => true,
        'dados' => $utilizadores,
        'paginacao' => [
            'pagina_atual'  => $pagina,
            'total_paginas' => $totalPaginas,
            'total_registos' => $totalRegistos,
            'limite'        => $limite
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar os dados: ' . $e->getMessage()
    ]);
}