<?php
// api/get_ficha_processo.php
session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Sessão expirada.'
        ]);
        exit;
    }

    require_once '../../api/conexao.php';

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID inválido.'
        ]);
        exit;
    }

    $sql = "
        SELECT p.*, 
               u.nome as nome_tecnico, u.nip as nip_tecnico, u.email as email_tecnico,
               uc.nome as nome_criador
        FROM processos p
        LEFT JOIN utilizadores u ON p.tecnico_responsavel_id = u.id
        LEFT JOIN utilizadores uc ON p.criado_por_id = uc.id
        WHERE p.id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Processo não encontrado.'
        ]);
        exit;
    }

    $processo = $res->fetch_assoc();
    $stmt->close();

    echo json_encode([
        'success' => true,
        'processo' => $processo
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno: ' . $e->getMessage()
    ]);
}