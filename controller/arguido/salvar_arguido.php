<?php
error_reporting(E_ALL);
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');
ini_set('max_execution_time', '300');
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
session_start();

/**
 * Função auxiliar para converter Base64 e guardar como ficheiro físico no servidor
 */
function guardarImagemServidor($base64Data, $diretorioDestino, $prefixo = 'biometria_') {
    if (empty($base64Data)) {
        return null;
    }

    if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
        $data = substr($base64Data, strpos($base64Data, ',') + 1);
        $extensao = strtolower($type[1]);

        if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'webp'])) {
            return null;
        }

        $dataDecodificada = base64_decode($data);
        if ($dataDecodificada === false) {
            return null;
        }

        if (!file_exists($diretorioDestino)) {
            mkdir($diretorioDestino, 0755, true);
        }

        $nomeFicheiro = $prefixo . uniqid() . '_' . time() . '.' . $extensao;
        $caminhoCompleto = $diretorioDestino . $nomeFicheiro;

        if (file_put_contents($caminhoCompleto, $dataDecodificada)) {
            return 'uploads/' . $nomeFicheiro;
        }
    }
    return null;
}

try {
    require_once '../../api/conexao.php';

    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception("Erro de conexão com a base de dados.");
    }

    $utilizador_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? $_SESSION['utilizador_id'] ?? null;
    if (!$utilizador_id) {
        throw new Exception("Sessão expirada. Faça login novamente.");
    }

    // Identifica se estamos em modo de Edição recolhendo o arguido_id
    $arguido_id = !empty($_POST['arguido_id']) ? intval($_POST['arguido_id']) : null;
    $isEdicao = ($arguido_id !== null && $arguido_id > 0);

    $processo_id = $_POST['processo_id'] ?? null;
    if (empty($processo_id) || !is_numeric($processo_id)) {
        throw new Exception("ID do processo inválido ou não fornecido.");
    }
    $processo_id = intval($processo_id);

    // Recolha de inputs - Dados Pessoais
    $nome = $_POST['nome'] ?? '';
    $alcunha = $_POST['alcunha'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? '';
    $idade = $_POST['idade'] ?? '';
    $bi = $_POST['bi'] ?? '';
    $nacionalidade = $_POST['nacionalidade'] ?? '';
    $naturalidade = $_POST['naturalidade'] ?? '';
    $estado_civil = $_POST['estado_civil'] ?? '';
    
    $nome_pai = $_POST['nome_pai'] ?? '';
    $nome_mae = $_POST['nome_mae'] ?? '';
    $filiacao = trim("Pai: " . $nome_pai . " | Mãe: " . $nome_mae);
    
    $contacto = $_POST['telefone'] ?? '';
    $morada = $_POST['residencia'] ?? '';
    $profissao = $_POST['profissao'] ?? '';
    
    // Antecedentes e Medidas
    $tipo_crime = $_POST['tipo_crime'] ?? '';
    $reincidente = $_POST['reincidente'] ?? 'Não';
    $estabelecimento_prisional = $_POST['estabelecimento_prisional'] ?? '';
    $tempo_cumprido = $_POST['tempo_cumprido'] ?? '';
    
    $tipo_medida = $_POST['tipo_medida'] ?? '';
    $prazo_unidade = $_POST['prazo_unidade'] ?? '';
    $prazo_quantidade = !empty($_POST['prazo_quantidade']) ? intval($_POST['prazo_quantidade']) : null;
    $data_inicio = !empty($_POST['data_inicio']) ? $_POST['data_inicio'] : null;
    $observacoes_detencao = $_POST['observacoes_detencao'] ?? '';

    // --- PROCESSAMENTO E GUARDA DE IMAGENS NO SERVIDOR ---
    $pastaUploadsFisica = __DIR__ . 'uploads/';

    $biometria_face_base64 = $_POST['biometria_face'] ?? null;
    $biometria_digital_base64 = $_POST['biometria_digital'] ?? null;

    $biometria_face_path = guardarImagemServidor($biometria_face_base64, $pastaUploadsFisica, 'face_');
    $biometria_digital_path = guardarImagemServidor($biometria_digital_base64, $pastaUploadsFisica, 'digital_');

    // Bens Apreendidos
    $bens_descricoes = $_POST['bem_descricao'] ?? [];
    $bens_categorias = $_POST['bem_categoria'] ?? [];
    $bens_observacoes = $_POST['bem_observacoes'] ?? [];

    if (!is_array($bens_descricoes)) {
        $bens_descricoes = [$bens_descricoes];
        $bens_categorias = [$bens_categorias];
        $bens_observacoes = [$bens_observacoes];
    }

    if (empty($bi) || empty($nome)) {
        throw new Exception("Dados obrigatórios em falta (Nome ou BI do arguido).");
    }

    // --- VALIDAÇÃO DE BI DUPLICADO ---
    $sqlCheck = "SELECT id FROM arguidos WHERE bi = ?";
    if ($isEdicao) {
        $sqlCheck .= " AND id != ?";
    }

    $stmtCheck = $conn->prepare($sqlCheck);
    if ($isEdicao) {
        $stmtCheck->bind_param("si", $bi, $arguido_id);
    } else {
        $stmtCheck->bind_param("s", $bi);
    }
    
    $stmtCheck->execute();
    $stmtCheck->store_result();
    
    if ($stmtCheck->num_rows > 0) {
        $stmtCheck->close();
        throw new Exception("Já existe um arguido registado com este número de BI no sistema.");
    }
    $stmtCheck->close();
    // ----------------------------------

    // Validação da existência do processo
    $stmtProc = $conn->prepare("SELECT id FROM processos WHERE id = ?");
    $stmtProc->bind_param("i", $processo_id);
    $stmtProc->execute();
    $stmtProc->store_result();
    if ($stmtProc->num_rows === 0) {
        $stmtProc->close();
        throw new Exception("O processo associado não foi encontrado na base de dados.");
    }
    $stmtProc->close();

    $conn->begin_transaction();

    if ($isEdicao) {
        // --- MODO DE EDIÇÃO (UPDATE) ---
        if (!$biometria_face_path) {
            $stmtFoto = $conn->prepare("SELECT biometria_face FROM arguidos WHERE id = ?");
            $stmtFoto->bind_param("i", $arguido_id);
            $stmtFoto->execute();
            $stmtFoto->bind_result($biometria_face_path);
            $stmtFoto->fetch();
            $stmtFoto->close();
        }

        if (!$biometria_digital_path) {
            $stmtDig = $conn->prepare("SELECT biometria_digital FROM arguidos WHERE id = ?");
            $stmtDig->bind_param("i", $arguido_id);
            $stmtDig->execute();
            $stmtDig->bind_result($biometria_digital_path);
            $stmtDig->fetch();
            $stmtDig->close();
        }

        $sqlArguido = "UPDATE arguidos SET 
            processo_id = ?, nome = ?, alcunha = ?, data_nascimento = ?, idade = ?, bi = ?, 
            nacionalidade = ?, naturalidade = ?, estado_civil = ?, filiacao = ?, 
            profissao = ?, contacto = ?, morada = ?, tipo_crime = ?, reincidente = ?, 
            estabelecimento_prisional = ?, tempo_cumprido = ?, tipo_medida = ?, 
            prazo_unidade = ?, prazo_quantidade = ?, data_inicio = ?, 
            observacoes_detencao = ?, biometria_face = ?, biometria_digital = ? 
            WHERE id = ?";

        $stmt = $conn->prepare($sqlArguido);
        if (!$stmt) {
            throw new Exception("Erro ao preparar query de atualização: " . $conn->error);
        }

        // Correção exata dos 23 tipos correspondentes (16 strings, 3 inteiros, etc.)
        $stmt->bind_param(
            "issssssssssssssssssissssi",
            $processo_id, $nome, $alcunha, $data_nascimento, $idade, $bi,
            $nacionalidade, $naturalidade, $estado_civil, $filiacao, $profissao,
            $contacto, $morada, $tipo_crime, $reincidente, 
            $estabelecimento_prisional, $tempo_cumprido, $tipo_medida, 
            $prazo_unidade, $prazo_quantidade, $data_inicio, 
            $observacoes_detencao, $biometria_face_path, $biometria_digital_path, 
            $arguido_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Erro ao atualizar arguido: " . $stmt->error);
        }
        $stmt->close();

        // Limpa os bens antigos para inserir os novos atualizados
        $stmtDelBens = $conn->prepare("DELETE FROM bens WHERE arguido_id = ?");
        $stmtDelBens->bind_param("i", $arguido_id);
        $stmtDelBens->execute();
        $stmtDelBens->close();

    } else {
        // --- MODO DE NOVO REGISTO (INSERT) ---
        $sqlArguido = "INSERT INTO arguidos (
            processo_id, utilizador_id, nome, alcunha, data_nascimento, idade, bi, 
            nacionalidade, naturalidade, estado_civil, filiacao, profissao, contacto, morada, 
            tipo_crime, reincidente, estabelecimento_prisional, tempo_cumprido, 
            tipo_medida, prazo_unidade, prazo_quantidade, data_inicio, 
            observacoes_detencao, biometria_face, biometria_digital
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sqlArguido);
        if (!$stmt) {
            throw new Exception("Erro ao preparar query de inserção: " . $conn->error);
        }

        $stmt->bind_param(
            "iisssssssssssssssssssssss",
            $processo_id, $utilizador_id, $nome, $alcunha, $data_nascimento, $idade, $bi,
            $nacionalidade, $naturalidade, $estado_civil, $filiacao, $profissao, $contacto, $morada,
            $tipo_crime, $reincidente, $estabelecimento_prisional, $tempo_cumprido,
            $tipo_medida, $prazo_unidade, $prazo_quantidade, $data_inicio,
            $observacoes_detencao, $biometria_face_path, $biometria_digital_path
        );

        if (!$stmt->execute()) {
            throw new Exception("Erro ao inserir arguido: " . $stmt->error);
        }

        $arguido_id = $stmt->insert_id;
        $stmt->close();
    }

    // Inserção de Bens (Comum para novos registos e atualizações)
    if (!empty($bens_descricoes)) {
        $sqlBem = "INSERT INTO bens (arguido_id, descricao, categoria, observacoes) VALUES (?, ?, ?, ?)";
        $stmtBem = $conn->prepare($sqlBem);
        
        for ($i = 0; $i < count($bens_descricoes); $i++) {
            $descricao = trim($bens_descricoes[$i] ?? '');
            $categoria = trim($bens_categorias[$i] ?? '');
            $observacoes = trim($bens_observacoes[$i] ?? '');

            if (!empty($descricao) && !empty($categoria)) {
                $stmtBem->bind_param("isss", $arguido_id, $descricao, $categoria, $observacoes);
                $stmtBem->execute();
            }
        }
        $stmtBem->close();
    }

    $conn->commit();
    
    $mensagemRetorno = $isEdicao ? 'Registo atualizado com sucesso!' : 'Registo guardado com sucesso!';
    echo json_encode([
        'sucesso' => true, 
        'mensagem' => $mensagemRetorno, 
        'id' => $arguido_id
    ]);

} catch (Throwable $e) {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->rollback();
    }
    echo json_encode([
        'sucesso' => false, 
        'mensagem' => $e->getMessage()
    ]);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}