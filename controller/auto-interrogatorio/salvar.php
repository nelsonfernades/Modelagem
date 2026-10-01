<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../api/conexao.php'; // Certifique-se de que este ficheiro define a variável $conn

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos ou payload vazio.']);
    exit;
}

// Extração e saneamento dos campos
$id = isset($input['id']) ? filter_var($input['id'], FILTER_VALIDATE_INT) : null;
$processo_id = $input['processo_id'] ?? null;
$arguido_id = $input['arguido_id'] ?? null;
$utilizador_id = $input['utilizador_id'] ?? null;
$numero_processo = $input['numero_processo'] ?? '';
$data_diligencia = $input['data_diligencia'] ?? date('Y-m-d');
$cidade = $input['cidade'] ?? 'Luanda';
$magistrado_mp = $input['magistrado_mp'] ?? null;
$defensor_advogado = $input['defensor_advogado'] ?? null;
$oficial_escrivao = $input['oficial_escrivao'] ?? null;
$nome_completo = $input['nome_completo'] ?? '';
$estado_civil = $input['estado_civil'] ?? null;
$profissao = $input['profissao'] ?? null;
$idade = !empty($input['idade']) ? filter_var($input['idade'], FILTER_VALIDATE_INT) : null;
$data_nascimento = !empty($input['data_nascimento']) ? $input['data_nascimento'] : null;
$naturalidade = $input['naturalidade'] ?? null;
$nome_pai = $input['nome_pai'] ?? null;
$nome_mae = $input['nome_mae'] ?? null;
$residencia_habitual = $input['residencia_habitual'] ?? null;
$bi_numero = $input['bi_numero'] ?? null;
$bi_arquivo_emissao = $input['bi_arquivo_emissao'] ?? null;
$bi_data_emissao = !empty($input['bi_data_emissao']) ? $input['bi_data_emissao'] : null;
$antecedentes_criminais = $input['antecedentes_criminais'] ?? null;
$transcricao_declaracoes = $input['transcricao_declaracoes'] ?? '';
$estado_auto = $input['estado_auto'] ?? 'rascunho';

// Validação de campos obrigatórios
if (empty($processo_id) || empty($arguido_id) || empty($utilizador_id) || empty($numero_processo) || empty($nome_completo) || empty($transcricao_declaracoes)) {
    http_response_code(422);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Preencha todos os campos obrigatórios.',
        'dados_recebidos_no_servidor' => [
            'processo_id' => $processo_id,
            'arguido_id' => $arguido_id,
            'utilizador_id' => $utilizador_id,
            'numero_processo' => $numero_processo,
            'nome_completo' => $nome_completo,
            'transcricao_declaracoes' => !empty($transcricao_declaracoes) ? 'Preenchido (texto presente)' : 'VAZIO'
        ]
    ]);
    exit;
}

try {
    if ($id) {
        // Atualização (UPDATE) - Registo já existe e está a ser editado
        $sql = "UPDATE auto_interrogatorios SET 
                    processo_id = ?, arguido_id = ?, utilizador_id = ?, numero_processo = ?, 
                    data_diligencia = ?, cidade = ?, magistrado_mp = ?, defensor_advogado = ?, 
                    oficial_escrivao = ?, nome_completo = ?, estado_civil = ?, profissao = ?, 
                    idade = ?, data_nascimento = ?, naturalidade = ?, nome_pai = ?, 
                    nome_mae = ?, residencia_habitual = ?, bi_numero = ?, bi_arquivo_emissao = ?, 
                    bi_data_emissao = ?, antecedentes_criminais = ?, transcricao_declaracoes = ?, estado_auto = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iiisssssssssisssssssssssi", 
            $processo_id, $arguido_id, $utilizador_id, $numero_processo, 
            $data_diligencia, $cidade, $magistrado_mp, $defensor_advogado, 
            $oficial_escrivao, $nome_completo, $estado_civil, $profissao, 
            $idade, $data_nascimento, $naturalidade, $nome_pai, 
            $nome_mae, $residencia_habitual, $bi_numero, $bi_arquivo_emissao, 
            $bi_data_emissao, $antecedentes_criminais, $transcricao_declaracoes, $estado_auto, 
            $id
        );
        $stmt->execute();

        $mensagem = ($estado_auto === 'finalizado') ? 'Auto de interrogatório finalizado com sucesso!' : 'Rascunho atualizado com sucesso!';
    } else {
        // NOVO REGISTO: Verificar estritamente se já existe um auto para este processo_id
        $sqlCheck = "SELECT id FROM auto_interrogatorios WHERE processo_id = ? LIMIT 1";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $processo_id);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();

        if ($resCheck->num_rows > 0) {
            http_response_code(422);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Este processo já possui um Auto de Interrogatório associado. Não é permitido criar um novo registo para o mesmo processo. Por favor, edite o registo existente ou selecione outro processo.'
            ]);
            exit;
        }

        // Inserção de Novo Registo (INSERT)
        $sql = "INSERT INTO auto_interrogatorios (
                    processo_id, arguido_id, utilizador_id, numero_processo, data_diligencia, 
                    cidade, magistrado_mp, defensor_advogado, oficial_escrivao, nome_completo, 
                    estado_civil, profissao, idade, data_nascimento, naturalidade, nome_pai, 
                    nome_mae, residencia_habitual, bi_numero, bi_arquivo_emissao, bi_data_emissao, 
                    antecedentes_criminais, transcricao_declaracoes, estado_auto
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iiisssssssssisssssssssss", 
            $processo_id, $arguido_id, $utilizador_id, $numero_processo, $data_diligencia, 
            $cidade, $magistrado_mp, $defensor_advogado, $oficial_escrivao, $nome_completo, 
            $estado_civil, $profissao, $idade, $data_nascimento, $naturalidade, $nome_pai, 
            $nome_mae, $residencia_habitual, $bi_numero, $bi_arquivo_emissao, $bi_data_emissao, 
            $antecedentes_criminais, $transcricao_declaracoes, $estado_auto
        );
        $stmt->execute();

        $id = $conn->insert_id;
        $mensagem = ($estado_auto === 'finalizado') ? 'Auto de interrogatório registado com sucesso!' : 'Rascunho criado com sucesso!';
    }

    // Se o auto foi finalizado, atualiza o estado do processo para 'Finalizado' automaticamente
    if ($estado_auto === 'finalizado') {
        $sqlUpdateProc = "UPDATE processos SET estado_processo = 'Finalizado' WHERE id = ?";
        $stmtProc = $conn->prepare($sqlUpdateProc);
        $stmtProc->bind_param("i", $processo_id);
        $stmtProc->execute();
    }

    echo json_encode([
        'sucesso' => true,
        'mensagem' => $mensagem,
        'id' => $id
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao salvar no banco de dados: ' . $e->getMessage()
    ]);
}