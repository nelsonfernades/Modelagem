<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    require_once '../../api/conexao.php';

    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception("Erro de conexão com a base de dados.");
    }

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        throw new Exception("ID do processo inválido.");
    }

    // Soft Delete: Em vez de apagar, marcamos como inativo/suspenso (ativo = 0)
    $stmt = $conn->prepare("UPDATE processos SET ativo = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Erro ao suspender o processo: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("Processo não encontrado ou já se encontra suspenso.");
    }

    $stmt->close();
    $conn->close();

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Processo suspenso com sucesso e guardado para auditoria.'
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}