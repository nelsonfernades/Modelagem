<?php
// Caminho da sessão e conexão mantido conforme solicitado
require_once '../../api/conexao.php'; 
require_once '../../api/session.php'; 

header('Content-Type: application/json; charset=utf-8');

$busca = $_GET['busca'] ?? '';
$filtro = $_GET['filtro'] ?? 'todos'; // 'todos', 'interrogatorio', 'restituicao'
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$limitesPorPagina = 8;
$offset = ($pagina - 1) * $limitesPorPagina;

try {
    global $conn;
    $tarefas = [];

    $buscaSQL = "";
    if (!empty($busca)) {
        $buscaClean = $conn->real_escape_string($busca);
        $buscaSQL = " AND (p.num_processo LIKE '%{$buscaClean}%' OR a.nome LIKE '%{$buscaClean}%')";
    }

    // ----------------------------------------------------------------------
    // 1. BUSCA AUTOS DE INTERROGATÓRIO
    // ----------------------------------------------------------------------
    if ($filtro === 'todos' || $filtro === 'interrogatorio') {
        $sqlAutos = "SELECT 
                        p.id AS processo_id,
                        p.num_processo,
                        a.nome AS nome_arguido,
                        a.tipo_crime,
                        u.nome AS tecnico_nome,
                        'auto' AS tipo_tarefa,
                        ai.id AS auto_id,
                        ai.data_diligencia,
                        ai.magistrado_mp,
                        ai.atualizado_em AS data_ordenacao,
                        (SELECT COUNT(*) FROM despachos d WHERE d.processo_id = p.id AND d.tipo_tarefa = 'auto') AS deliberado,
                        (SELECT d.criado_em FROM despachos d WHERE d.processo_id = p.id AND d.tipo_tarefa = 'auto' ORDER BY d.id DESC LIMIT 1) AS data_deliberacao,
                        (SELECT d.prazo_submissao FROM despachos d WHERE d.processo_id = p.id AND d.tipo_tarefa = 'auto' ORDER BY d.id DESC LIMIT 1) AS prazo_submissao
                    FROM auto_interrogatorios ai
                    INNER JOIN processos p ON ai.processo_id = p.id
                    INNER JOIN arguidos a ON ai.arguido_id = a.id
                    LEFT JOIN utilizadores u ON p.tecnico_responsavel_id = u.id
                    WHERE ai.estado_auto = 'finalizado' {$buscaSQL}";

        $resAutos = $conn->query($sqlAutos);
        while ($row = $resAutos->fetch_assoc()) {
            $row['deliberado'] = (int)$row['deliberado'] > 0;
            $tarefas[] = $row;
        }
    }

    // ----------------------------------------------------------------------
    // 2. BUSCA BENS DE PROCESSOS (Corrigido com EXISTS para evitar duplicações)
    // ----------------------------------------------------------------------
    if ($filtro === 'todos' || $filtro === 'restituicao') {
        $sqlBens = "SELECT 
                        p.id AS processo_id,
                        p.num_processo,
                        a.nome AS nome_arguido,
                        a.tipo_crime,
                        u.nome AS tecnico_nome,
                        'bens' AS tipo_tarefa,
                        COUNT(b.id) AS total_bens,
                        GROUP_CONCAT(CONCAT(b.descricao, ' [', b.categoria, ']') SEPARATOR '||') AS lista_bens_raw,
                        p.atualizado_em AS data_ordenacao,
                        (SELECT COUNT(*) FROM despachos d WHERE d.processo_id = p.id AND d.tipo_tarefa = 'bens') AS deliberado,
                        (SELECT d.criado_em FROM despachos d WHERE d.processo_id = p.id AND d.tipo_tarefa = 'bens' ORDER BY d.id DESC LIMIT 1) AS data_deliberacao,
                        (SELECT d.destino_bem FROM despachos d WHERE d.processo_id = p.id AND d.tipo_tarefa = 'bens' ORDER BY d.id DESC LIMIT 1) AS destino_bem_efetuado
                    FROM bens b
                    INNER JOIN arguidos a ON b.arguido_id = a.id
                    INNER JOIN processos p ON a.processo_id = p.id
                    LEFT JOIN utilizadores u ON p.tecnico_responsavel_id = u.id
                    WHERE EXISTS (
                        SELECT 1 FROM auto_interrogatorios ai 
                        WHERE ai.processo_id = p.id AND ai.estado_auto = 'finalizado'
                    ) {$buscaSQL}
                    GROUP BY p.id, a.id";

        $resBens = $conn->query($sqlBens);
        while ($row = $resBens->fetch_assoc()) {
            $row['deliberado'] = (int)$row['deliberado'] > 0;
            $bensArray = [];
            if (!empty($row['lista_bens_raw'])) {
                $itens = explode('||', $row['lista_bens_raw']);
                foreach ($itens as $item) {
                    $bensArray[] = $item;
                }
            }
            $row['bens'] = $bensArray;
            unset($row['lista_bens_raw']);
            $tarefas[] = $row;
        }
    }

    // Ordenação unificada cronológica por data de atualização (mais recente primeiro)
    usort($tarefas, function($a, $b) {
        return strtotime($b['data_ordenacao']) - strtotime($a['data_ordenacao']);
    });

    // Paginação manual segura do array unificado e ordenado
    $totalTarefas = count($tarefas);
    $totalPaginas = max(1, ceil($totalTarefas / $limitesPorPagina));
    $tarefasPaginadas = array_slice($tarefas, $offset, $limitesPorPagina);

    echo json_encode([
        'success' => true,
        'tarefas' => $tarefasPaginadas,
        'pagina_atual' => $pagina,
        'total_paginas' => $totalPaginas,
        'total_registos' => $totalTarefas
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor: ' . $e->getMessage()
    ]);
}