<?php
// controller/processos/salvar_processo.php
session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    // Garante que a requisição seja exclusivamente POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método de requisição inválido.");
    }

    // Ajuste o caminho para a sua conexão conforme a estrutura da sua pasta
    require_once '../../api/conexao.php';

    // Verificar se o utilizador está autenticado
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    if ($userId === 0) {
        throw new Exception("Sessão inválida ou expirada. Faça login novamente.");
    }

    // Capturar os dados enviados em JSON pelo JavaScript
    $input = json_decode(file_get_contents('php://input'), true);

    $numeroProcesso = isset($input['numero_processo']) ? trim($input['numero_processo']) : '';
    $nomeArguido    = isset($input['nome_arguido']) ? trim($input['nome_arguido']) : '';
    $tecnicoId      = isset($input['tecnico_id']) && $input['tecnico_id'] !== '' ? intval($input['tecnico_id']) : $userId;

    // Validações dos campos obrigatórios
    if (empty($numeroProcesso)) {
        throw new Exception("O número do processo é obrigatório.");
    }
    if (empty($nomeArguido)) {
        throw new Exception("O nome do arguido é obrigatório.");
    }

    // Verificar se o número de processo já existe para evitar duplicados
    $stmtCheck = $conn->prepare("SELECT id FROM processos WHERE num_processo = ? LIMIT 1");
    if ($stmtCheck) {
        $stmtCheck->bind_param("s", $numeroProcesso);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            throw new Exception("Já existe um processo registado com este número.");
        }
        $stmtCheck->close();
    }

    // Inserir na base de dados
    $sqlInsert = "
        INSERT INTO processos (num_processo, nome_arguido, tecnico_responsavel_id, criado_por_id, estado_processo, criado_em) 
        VALUES (?, ?, ?, ?, 'Em Andamento', NOW())
    ";

    $stmtInsert = $conn->prepare($sqlInsert);
    if (!$stmtInsert) {
        throw new Exception("Erro na preparação da query de inserção: " . $conn->error);
    }

    // Tipos: s (string num), s (string nome), i (int técnico), i (int criador)
    $stmtInsert->bind_param("ssii", $numeroProcesso, $nomeArguido, $tecnicoId, $userId);
    
    if (!$stmtInsert->execute()) {
        throw new Exception("Erro ao salvar o processo na base de dados: " . $stmtInsert->error);
    }

    $novoProcessoId = $stmtInsert->insert_id;
    $stmtInsert->close();

    // Resposta bem-sucedida em formato JSON exato que o seu JS espera
    echo json_encode([
        'success' => true,
        'message' => 'Processo criado e atribuído com sucesso!',
        'processo_id' => $novoProcessoId
    ]);

} catch (Exception $e) {
    // Retorna o erro capturado de forma limpa em JSON (adeus erro de PDF!)
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}