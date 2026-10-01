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
$processo_id = !empty($input['processo_id']) ? filter_var($input['processo_id'], FILTER_VALIDATE_INT) : null;
$arguido_id = !empty($input['arguido_id']) ? filter_var($input['arguido_id'], FILTER_VALIDATE_INT) : null;
$utilizador_id = !empty($input['utilizador_id']) ? filter_var($input['utilizador_id'], FILTER_VALIDATE_INT) : null;
$numero_processo = $input['numero_processo'] ?? null;
$data_diligencia = !empty($input['data_diligencia']) ? $input['data_diligencia'] : null;
$cidade = $input['cidade'] ?? 'Luanda';
$magistrado_mp = $input['magistrado_mp'] ?? null;
$defensor_advogado = $input['defensor_advogado'] ?? null;
$oficial_escrivao = $input['oficial_escrivao'] ?? null;
$nome_completo = $input['nome_completo'] ?? null;
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
$transcricao_declaracoes = $input['transcricao_declaracoes'] ?? null;
$estado_auto = $input['estado_auto'] ?? 'rascunho';

try {
    if ($id) {
        // Atualização de Rascunho existente (UPDATE) - 25 parâmetros exatos
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
        // Validação: Verificar se já existe um auto para este processo_id antes de inserir
        if ($processo_id) {
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
        }

        // Criação de Novo Rascunho (INSERT) - 24 parâmetros exatos
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

    echo json_encode([
        'sucesso' => true,
        'mensagem' => $mensagem,
        'id' => $id
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao salvar rascunho no banco de dados: ' . $e->getMessage()
    ]);
}