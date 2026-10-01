<?php
// processar_despacho.php
header('Content-Type: application/json');
require_once '../../api/conexao.php'; 
require_once '../../api/session.php'; 

try {
    // Iniciar transação via MySQLi
    $conn->begin_transaction();

    $processo_id = $_POST['processo_id'] ?? null;
    $tipo_tarefa = $_POST['tipo_tarefa'] ?? null;
    $utilizador_id = $_SESSION['user_id'] ?? 0; 

    if (!$processo_id || !$tipo_tarefa) {
        throw new Exception("Dados incompletos enviados.");
    }

    if ($utilizador_id == 0) {
        throw new Exception("Sessão inválida. Por favor, faça login novamente.");
    }

    // 1. Inserir na tabela de Despachos
    $sql_despacho = "INSERT INTO despachos (processo_id, utilizador_id, tipo_tarefa, 
                     prazo_submissao, destino_submissao, responsavel_envio, 
                     destino_bem, fundamentacao) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql_despacho);
    if (!$stmt) {
        throw new Exception("Erro na preparação da query: " . $conn->error);
    }

    $prazo = $_POST['prazo_submissao'] ?? null;
    $destino_sub = $_POST['destino_submissao'] ?? null;
    $resp_envio = $_POST['responsavel_envio'] ?? null;
    $destino_b = $_POST['destino_bem'] ?? null;
    $desc_dec = $_POST['descricao_decisao'] ?? null;

    $stmt->bind_param("iissssss", $processo_id, $utilizador_id, $tipo_tarefa, $prazo, $destino_sub, $resp_envio, $destino_b, $desc_dec);
    $stmt->execute();
    $stmt->close();

    // 2. Atualizar o estado do processo conforme a tarefa
    $novo_estado = ($tipo_tarefa === 'auto') ? 'Encaminhado ao Magistrado' : 'Decisão sobre Bens emitida';
    $stmt_update = $conn->prepare("UPDATE processos SET estado_processo = ? WHERE id = ?");
    if (!$stmt_update) {
        throw new Exception("Erro na preparação do update: " . $conn->error);
    }
    
    $stmt_update->bind_param("si", $novo_estado, $processo_id);
    $stmt_update->execute();
    $stmt_update->close();

    // 3. Lógica específica para Bens (Atualizado com JOIN na tabela arguidos)
    if ($tipo_tarefa === 'bens') {
        $stmt_bens = $conn->prepare("
            UPDATE bens b 
            JOIN arguidos a ON b.arguido_id = a.id 
            SET b.estado_processamento = 'despachado' 
            WHERE a.processo_id = ?
        ");
        if (!$stmt_bens) {
            throw new Exception("Erro na preparação dos bens: " . $conn->error);
        }
        $stmt_bens->bind_param("i", $processo_id);
        $stmt_bens->execute();
        $stmt_bens->close();
    }

    // Confirmar transação
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Despacho emitido com sucesso!']);

} catch (Exception $e) {
    // Reverter transação em caso de erro
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Erro ao processar: ' . $e->getMessage()]);
}