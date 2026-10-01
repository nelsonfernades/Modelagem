<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../api/conexao.php'; // Certifique-se de que este ficheiro define a variável $conn (ou ajuste para a sua variável de conexão)

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID do auto inválido ou não fornecido.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM auto_interrogatorios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $dados = $resultado->fetch_assoc();

    if (!$dados) {
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Auto de interrogatório não encontrado.']);
        exit;
    }

    echo json_encode(['sucesso' => true, 'dados' => $dados]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}