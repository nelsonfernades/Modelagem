<?php
// api/apagar_processo.php
session_start();

if (ob_get_length()) {
    ob_clean();
}

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Sessão expirada. Por favor, faça login novamente.'
        ]);
        exit;
    }

    require_once '../../api/conexao.php';

    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? intval($input['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID do processo inválido.'
        ]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM processos WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Processo eliminado com sucesso!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Processo não encontrado ou já eliminado.'
            ]);
        }
    } else {
        throw new Exception("Erro ao executar eliminação: " . $stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno no servidor: ' . $e->getMessage()
    ]);
}