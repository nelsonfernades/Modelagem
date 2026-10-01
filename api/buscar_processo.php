<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    require_once __DIR__ . '/conexao.php'; 

    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception("A conexão MySQLi (\$conn) não está disponível.");
    }

    $numero = trim($_GET['numero'] ?? '');

    if (empty($numero)) {
        echo json_encode(['success' => false, 'mensagem' => 'Número do processo não informado.']);
        exit;
    }

    $usuarioLogadoId = $_SESSION['user_id'] ?? $_SESSION['id'] ?? $_SESSION['utilizador_id'] ?? null;

    if (!$usuarioLogadoId) {
        echo json_encode(['success' => false, 'mensagem' => 'Sessão expirada ou utilizador não autenticado.']);
        exit;
    }

    // 1. Consulta o processo e o técnico responsável
    $sql = "SELECT p.id, p.num_processo, p.nome_arguido, p.tecnico_responsavel_id, u.nome AS tecnico_nome 
            FROM processos p
            JOIN utilizadores u ON p.tecnico_responsavel_id = u.id
            WHERE p.num_processo = ? LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $numero);
    $stmt->execute();
    $result = $stmt->get_result();
    $processo = $result->fetch_assoc();
    $stmt->close();

    if (!$processo) {
        echo json_encode(['success' => false, 'mensagem' => 'Processo não encontrado.']);
        exit;
    }

    // 2. VERIFICAÇÃO DE DUPLICIDADE: Se o ID do processo já existe na tabela arguidos
    $stmtArguido = $conn->prepare("SELECT id FROM arguidos WHERE processo_id = ? LIMIT 1");
    $stmtArguido->bind_param("i", $processo['id']);
    $stmtArguido->execute();
    $stmtArguido->store_result();
    $jaRegistrado = ($stmtArguido->num_rows > 0);
    $stmtArguido->close();

    // Validação de autorização do técnico responsável
    $autorizado = ((int) $usuarioLogadoId === (int) $processo['tecnico_responsavel_id']);

    // Se já estiver registado, bloqueia e notifica independentemente do utilizador
    if ($jaRegistrado) {
        echo json_encode([
            'success' => true,
            'processo' => [
                'id' => $processo['id'],
                'numero' => $processo['num_processo'],
                'arguido' => $processo['nome_arguido'],
                'tecnico_id' => $processo['tecnico_responsavel_id'],
                'tecnicoNome' => $processo['tecnico_nome']
            ],
            'usuarioLogadoId' => $usuarioLogadoId,
            'autorizado' => false,
            'ja_registrado' => true,
            'mensagem' => 'Este processo já possui um arguido registado na base de dados.'
        ]);
        exit;
    }

    // Resposta normal se estiver livre para registo
    echo json_encode([
        'success' => true,
        'processo' => [
            'id' => $processo['id'],
            'numero' => $processo['num_processo'],
            'arguido' => $processo['nome_arguido'],
            'tecnico_id' => $processo['tecnico_responsavel_id'],
            'tecnicoNome' => $processo['tecnico_nome']
        ],
        'usuarioLogadoId' => $usuarioLogadoId,
        'autorizado' => $autorizado,
        'ja_registrado' => false
    ]);

    $conn->close();

} catch (Throwable $e) {
    echo json_encode([
        'success' => false, 
        'mensagem' => 'Erro no Servidor: ' . $e->getMessage()
    ]);
}