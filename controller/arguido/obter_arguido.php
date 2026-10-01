<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

require_once '../../api/conexao.php'; // Ajuste o caminho da conexão conforme a localização do ficheiro

if (!isset($conn) || !($conn instanceof mysqli)) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro de conexão com a base de dados.'
    ]);
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'ID não fornecido.'
    ]);
    exit;
}

$id = intval($_GET['id']);

try {
    // Procura o arguido procurando primeiro pelo ID próprio da tabela arguidos, 
    // ou pelo processo_id (caso o frontend envie o ID do processo)
    $sqlArguido = "SELECT a.*, p.num_processo, a.id as arguido_id
                   FROM arguidos a 
                   INNER JOIN processos p ON a.processo_id = p.id 
                   WHERE a.id = ? OR a.processo_id = ? 
                   LIMIT 1";
                   
    $stmt = $conn->prepare($sqlArguido);
    $stmt->bind_param("ii", $id, $id);
    $stmt->execute();
    $resultArguido = $stmt->get_result();
    
    if ($resultArguido->num_rows === 0) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Este arguido não existe na base de dados.'
        ]);
        exit;
    }

    $arguido = $resultArguido->fetch_assoc();
    $stmt->close();

    $arguidoIdReal = $arguido['arguido_id'];

    // Busca os bens associados a este arguido na tabela normalizada 'bens'
    $sqlBens = "SELECT descricao, categoria, observacoes FROM bens WHERE arguido_id = ?";
    $stmtBens = $conn->prepare($sqlBens);
    $stmtBens->bind_param("i", $arguidoIdReal);
    $stmtBens->execute();
    $resultBens = $stmtBens->get_result();
    
    $bens = [];
    while ($bem = $resultBens->fetch_assoc()) {
        $bens[] = $bem;
    }
    $stmtBens->close();

    // Organiza a resposta final
    $arguido['id'] = $arguidoIdReal; // Garante que o ID retornado é o ID do arguido para futuras edições
    $arguido['bens'] = $bens;
    $arguido['sucesso'] = true;

    echo json_encode($arguido);

} catch (Exception $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro interno: ' . $e->getMessage()
    ]);
}