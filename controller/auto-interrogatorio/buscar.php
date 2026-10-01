<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../../api/conexao.php';
require_once '../../api/session.php';

// Valida se o número do processo foi enviado via parâmetro GET
if (!isset($_GET['numero']) || empty(trim($_GET['numero']))) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Número do processo não fornecido.']);
    exit;
}

$numeroProcesso = trim($_GET['numero']);

try {
    // 1. Procura o processo principal na tabela processos
    $sqlProc = "SELECT id, num_processo, tecnico_responsavel_id FROM processos WHERE num_processo = ? LIMIT 1";
    $stmtProc = $conn->prepare($sqlProc);
    $stmtProc->bind_param("s", $numeroProcesso);
    $stmtProc->execute();
    $resProc = $stmtProc->get_result();
    $processo = $resProc->fetch_assoc();

    if (!$processo) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Processo não encontrado na base de dados.']);
        exit;
    }

    $processoId = $processo['id'];

    // 2. Verifica se já existe um Auto de Interrogatório associado
    $sqlAuto = "SELECT * FROM auto_interrogatorios WHERE processo_id = ? LIMIT 1";
    $stmtAuto = $conn->prepare($sqlAuto);
    $stmtAuto->bind_param("i", $processoId);
    $stmtAuto->execute();
    $resAuto = $stmtAuto->get_result();
    $autoExistente = $resAuto->fetch_assoc();

    if ($autoExistente) {

        // Se for rascunho, calcula a idade e retorna os dados guardados
        $idade = '';
        if (!empty($autoExistente['data_nascimento'])) {
            $nascimento = new DateTime($autoExistente['data_nascimento']);
            $hoje = new DateTime('today');
            $idade = $nascimento->diff($hoje)->y;
        }

        echo json_encode([
            'sucesso' => true,
            'origem' => 'rascunho_existente',
            'dados' => [
                'id' => $autoExistente['id'],
                'processo_id' => $autoExistente['processo_id'],
                'arguido_id' => $autoExistente['arguido_id'],
                'utilizador_id' => $autoExistente['utilizador_id'],
                'numero_processo' => $autoExistente['numero_processo'],
                'data_diligencia' => $autoExistente['data_diligencia'],
                'cidade' => $autoExistente['cidade'],
                'magistrado_mp' => $autoExistente['magistrado_mp'],
                'defensor_advogado' => $autoExistente['defensor_advogado'],
                'oficial_escrivao' => $autoExistente['oficial_escrivao'],
                'nome_completo' => $autoExistente['nome_completo'],
                'estado_civil' => $autoExistente['estado_civil'],
                'profissao' => $autoExistente['profissao'],
                'idade' => $idade,
                'data_nascimento' => $autoExistente['data_nascimento'],
                'naturalidade' => $autoExistente['naturalidade'],
                'nome_pai' => $autoExistente['nome_pai'],
                'nome_mae' => $autoExistente['nome_mae'],
                'residencia_habitual' => $autoExistente['residencia_habitual'],
                'bi_numero' => $autoExistente['bi_numero'],
                'bi_arquivo_emissao' => $autoExistente['bi_arquivo_emissao'],
                'bi_data_emissao' => $autoExistente['bi_data_emissao'],
                'antecedentes_criminais' => $autoExistente['antecedentes_criminais'],
                'transcricao_declaracoes' => $autoExistente['transcricao_declaracoes'],
                'estado_auto' => $autoExistente['estado_auto']
            ]
        ]);
        exit;
    }

    // 3. Se não existe auto, puxa os dados base do arguido cadastrado no processo
    $sqlArguido = "SELECT * FROM arguidos WHERE processo_id = ? LIMIT 1";
    $stmtArguido = $conn->prepare($sqlArguido);
    $stmtArguido->bind_param("i", $processoId);
    $stmtArguido->execute();
    $resArguido = $stmtArguido->get_result();
    $arguido = $resArguido->fetch_assoc();

    if (!$arguido) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Este processo existe, mas ainda não possui nenhum arguido registado na base de dados.'
        ]);
        exit;
    }

    // Tratamento e separação da filiação unificada ("Pai: X | Mãe: Y")
    $nomePai = '';
    $nomeMae = '';

    if (!empty($arguido['filiacao'])) {
        $filiacaoString = $arguido['filiacao'];

        if (stripos($filiacaoString, 'Pai:') !== false || stripos($filiacaoString, 'Mãe:') !== false || stripos($filiacaoString, 'Mae:') !== false) {
            $partes = explode('|', $filiacaoString);
            foreach ($partes as $parte) {
                if (stripos($parte, 'Pai:') !== false) {
                    $nomePai = trim(str_ireplace('Pai:', '', $parte));
                } elseif (stripos($parte, 'Mãe:') !== false || stripos($parte, 'Mae:') !== false) {
                    $nomeMae = trim(str_ireplace(['Mãe:', 'Mae:'], '', $parte));
                }
            }
        } else {
            $pais = explode(' e ', $filiacaoString);
            $nomePai = trim($pais[0] ?? '');
            $nomeMae = trim($pais[1] ?? '');
        }
    }

    // Cálculo da idade a partir da data de nascimento
    $idade = '';
    if (!empty($arguido['data_nascimento'])) {
        $nascimento = new DateTime($arguido['data_nascimento']);
        $hoje = new DateTime('today');
        $idade = $nascimento->diff($hoje)->y;
    }

    echo json_encode([
        'sucesso' => true,
        'origem' => 'arguido_base',
        'dados' => [
            'id' => null, 
            'processo_id' => $processoId,
            'arguido_id' => $arguido['id'],
            'utilizador_id' => $_SESSION['user_id'] ?? $processo['tecnico_responsavel_id'],
            'numero_processo' => $processo['num_processo'],
            'nome_completo' => $arguido['nome'] ?? '',
            'estado_civil' => $arguido['estado_civil'] ?? '',
            'profissao' => $arguido['profissao'] ?? '',
            'idade' => $idade,
            'data_nascimento' => $arguido['data_nascimento'] ?? '',
            'naturalidade' => $arguido['naturalidade'] ?? '',
            'nome_pai' => $nomePai,
            'nome_mae' => $nomeMae,
            'residencia_habitual' => $arguido['morada'] ?? '',
            'bi_numero' => $arguido['bi'] ?? '',
            'bi_arquivo_emissao' => '',
            'bi_data_emissao' => '',
            'antecedentes_criminais' => '',
            'transcricao_declaracoes' => '',
            'estado_auto' => 'rascunho'
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno no servidor: ' . $e->getMessage()]);
}