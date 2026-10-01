<?php
// alterar_status_acesso.php
header('Content-Type: application/json; charset=utf-8');
require_once '../../api/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    exit;
}

$id   = isset($_POST['id']) ? intval($_POST['id']) : null;
$acao = isset($_POST['acao']) ? trim($_POST['acao']) : '';

if (!$id || !in_array($acao, ['ativar', 'bloquear'])) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
    exit;
}

// Define o novo estado com base na ação
$novoEstado = ($acao === 'ativar') ? 'ativo' : 'bloqueado';
$mensagem   = ($acao === 'ativar') 
    ? 'Conta reativada com sucesso! O utilizador já pode aceder ao sistema.' 
    : 'Acesso revogado! A conta foi suspensa com sucesso.';

try {
    $stmt = $conn->prepare("UPDATE utilizadores SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $novoEstado, $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => $mensagem
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Falha ao atualizar o estado no banco de dados.'
        ]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao processar alteração: ' . $e->getMessage()
    ]);
}