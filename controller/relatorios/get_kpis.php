<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../api/conexao.php';
require_once '../../api/session.php';

try {
    // Contagem de Processos
    $resProc = $conn->query("SELECT COUNT(*) as total FROM processos");
    $totalProcessos = $resProc ? $resProc->fetch_assoc()['total'] : 0;

    // Contagem de Autos de Interrogatório
    $resAutos = $conn->query("SELECT COUNT(*) as total FROM auto_interrogatorios");
    $totalAutos = $resAutos ? $resAutos->fetch_assoc()['total'] : 0;

    // Contagem de Arguidos
    $resArg = $conn->query("SELECT COUNT(*) as total FROM arguidos");
    $totalArguidos = $resArg ? $resArg->fetch_assoc()['total'] : 0;

    // Contagem de Bens Apreendidos
    $resBens = $conn->query("SELECT COUNT(*) as total FROM bens");
    $totalBens = $resBens ? $resBens->fetch_assoc()['total'] : 0;

    echo json_encode([
        'success' => true,
        'processos' => (int)$totalProcessos,
        'autos' => (int)$totalAutos,
        'arguidos' => (int)$totalArguidos,
        'bens' => (int)$totalBens
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}