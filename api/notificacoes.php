<?php
// Inicia a sessão para identificar o usuário logado
session_start();

// Define explicitamente que a resposta será em formato JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // 1. Inclua o arquivo de conexão com o seu banco de dados
    // Ajuste o caminho relativo caso o arquivo de conexão esteja em outra pasta (ex: '../conexao.php')
    require_once '../api/conexao.php'; 

    // 2. Identifica o ID do usuário logado na sessão (ajuste o nome da variável de sessão se necessário)
    $usuario_id = $_SESSION['usuario_id'] ?? null;

    if (!$usuario_id) {
        echo json_encode([
            'success' => false,
            'message' => 'Usuário não autenticado.'
        ]);
        exit;
    }

    // 3. Consulta ao banco de dados para buscar as notificações do usuário
    // Substitua 'notificacoes', 'usuario_id', 'titulo', 'mensagem', 'data' pelos nomes reais das suas colunas/tabela
    $stmt = $pdo->prepare("
        SELECT id, titulo, mensagem, data, lida 
        FROM notificacoes 
        WHERE usuario_id = ? 
        ORDER BY data DESC 
        LIMIT 10
    ");
    
    $stmt->execute([$usuario_id]);
    $notificacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Retorna os dados em formato JSON com sucesso
    echo json_encode([
        'success' => true,
        'notificacoes' => $notificacoes
    ]);

} catch (PDOException $e) {
    // Tratamento de erro caso ocorra falha na conexão ou na query SQL
    echo json_encode([
        'success' => false,
        'message' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Tratamento de erros gerais
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno: ' . $e->getMessage()
    ]);
}
?>