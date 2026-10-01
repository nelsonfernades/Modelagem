<?php
require_once '../../api/conexao.php'; 
require_once '../../api/session.php'; 

header('Content-Type: application/json; charset=utf-8');

try {
    global $conn;

    // 1. Total de Processos
    $resProc = $conn->query("SELECT COUNT(*) AS total FROM processos");
    $totalProcessos = $resProc->fetch_assoc()['total'] ?? 0;

    // 2. Aguardando despachos (Autos finalizados ou bens que ainda não possuem despacho registado)
    $resAutosPend = $conn->query("SELECT COUNT(DISTINCT ai.processo_id) AS total FROM auto_interrogatorios ai WHERE ai.estado_auto = 'finalizado' AND NOT EXISTS (SELECT 1 FROM despachos d WHERE d.processo_id = ai.processo_id AND d.tipo_tarefa = 'auto')");
    $autosPendentes = $resAutosPend->fetch_assoc()['total'] ?? 0;

    $resBensPend = $conn->query("SELECT COUNT(DISTINCT p.id) AS total FROM bens b INNER JOIN arguidos a ON b.arguido_id = a.id INNER JOIN processos p ON a.processo_id = p.id WHERE EXISTS (SELECT 1 FROM auto_interrogatorios ai WHERE ai.processo_id = p.id AND ai.estado_auto = 'finalizado') AND NOT EXISTS (SELECT 1 FROM despachos d WHERE d.processo_id = p.id AND d.tipo_tarefa = 'bens')");
    $bensPendentes = $resBensPend->fetch_assoc()['total'] ?? 0;

    $aguardandoDespachos = $autosPendentes + $bensPendentes;

    // 3. Entregas Concluídas (Total de despachos de autos emitidos)
    $resConcluidas = $conn->query("SELECT COUNT(*) AS total FROM despachos WHERE tipo_tarefa = 'auto'");
    $entregasConcluidas = $resConcluidas->fetch_assoc()['total'] ?? 0;

    // 4. Restituídos ao Estado (Total de despachos de bens com destino ao estado)
    $resEstado = $conn->query("SELECT COUNT(*) AS total FROM despachos WHERE tipo_tarefa = 'bens' AND destino_bem = 'estado'");
    $restituidosEstado = $resEstado->fetch_assoc()['total'] ?? 0;

    echo json_encode([
        'success' => true,
        'total_processos' => (int)$totalProcessos,
        'aguardando_despachos' => (int)$aguardandoDespachos,
        'entregas_concluidas' => (int)$entregasConcluidas,
        'restituidos_estado' => (int)$restituidosEstado
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar métricas: ' . $e->getMessage()
    ]);
}