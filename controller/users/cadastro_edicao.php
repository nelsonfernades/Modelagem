<?php
// salvar_tecnico.php
header('Content-Type: application/json; charset=utf-8');

// Requer a sua conexão com a base de dados
require_once '../../api/conexao.php'; // Altere para o seu ficheiro de conexão ($conn / $mysqli)

// Resposta padrão
$response = ['success' => false, 'message' => 'Ocorreu um erro inesperado.'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Método de requisição inválido.';
    echo json_encode($response);
    exit;
}

try {
    // Captura dos dados enviados via FormData
    $id        = !empty($_POST['id']) ? intval($_POST['id']) : null;
    $nome      = trim($_POST['nome'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $nip       = trim($_POST['nip'] ?? '');
    $telefone  = trim($_POST['telefone'] ?? '');
    $perfil_id = !empty($_POST['perfil_id']) ? intval($_POST['perfil_id']) : null;
    $estado    = trim($_POST['status'] ?? 'ativo');
    $qr_code   = !empty($_POST['qr_code']) ? trim($_POST['qr_code']) : null;
    $senha     = $_POST['senha'] ?? '';

    // Validações básicas de campos obrigatórios
    if (empty($nome) || empty($email) || empty($nip) || empty($telefone) || empty($perfil_id)) {
        throw new Exception('Por favor, preencha todos os campos obrigatórios.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('O e-mail fornecido não é válido.');
    }

    // Validação de Senha para Novo Cadastro
    if (!$id && empty($senha)) {
        throw new Exception('A senha é obrigatória para o cadastro de novos técnicos.');
    }

    // ------------------------------------------------------------------
    // VERIFICAÇÃO DE UNICIDADE (Email, NIP e QR Code)
    // ------------------------------------------------------------------
    $sqlCheck = "SELECT id, email, nip, qr_code_passe FROM utilizadores WHERE (email = ? OR nip = ?" . ($qr_code ? " OR qr_code_passe = ?" : "") . ")";
    if ($id) {
        $sqlCheck .= " AND id != ?";
    }

    $stmtCheck = $conn->prepare($sqlCheck);
    if ($qr_code) {
        if ($id) $stmtCheck->bind_param("sssi", $email, $nip, $qr_code, $id);
        else $stmtCheck->bind_param("sss", $email, $nip, $qr_code);
    } else {
        if ($id) $stmtCheck->bind_param("ssi", $email, $nip, $id);
        else $stmtCheck->bind_param("ss", $email, $nip);
    }

    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    while ($row = $resultCheck->fetch_assoc()) {
        if ($row['email'] === $email) throw new Exception('Este e-mail já se encontra registado.');
        if ($row['nip'] === $nip) throw new Exception('Este NIP já se encontra registado.');
        if ($qr_code && $row['qr_code_passe'] === $qr_code) throw new Exception('Este QR Code de Passe já se encontra associado a outro utilizador.');
    }
    $stmtCheck->close();

    // ------------------------------------------------------------------
    // TRATAMENTO DO UPLOAD DA FOTO DE PERFIL
    // ------------------------------------------------------------------
    $foto_url = null;
    
    // Se estiver a editar, procura a foto atual no banco para manter caso não seja alterada
    if ($id) {
        $stmtFoto = $conn->prepare("SELECT foto_url FROM utilizadores WHERE id = ?");
        $stmtFoto->bind_param("i", $id);
        $stmtFoto->execute();
        $foto_url = $stmtFoto->get_result()->fetch_assoc()['foto_url'] ?? null;
        $stmtFoto->close();
    }

    // Verifica se a opção de remover foto foi marcada no frontend
    if (isset($_POST['remover_foto']) && $_POST['remover_foto'] === 'true') {
        if ($foto_url && file_exists(__DIR__ . '/' . $foto_url)) {
            @unlink(__DIR__ . '/' . $foto_url); // Apaga a imagem física do servidor
        }
        $foto_url = null;
    }

    // Upload de nova foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if ($file['size'] > $maxSize) {
            throw new Exception('A fotografia excede o tamanho máximo permitido de 2MB.');
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes)) {
            throw new Exception('Formato de imagem inválido. Apenas JPG e PNG são permitidos.');
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $novoNome = 'avatar_' . uniqid() . '_' . time() . '.' . strtolower($ext);
        $diretorioUpload = 'uploads/avatars/';

        if (!is_dir($diretorioUpload)) {
            mkdir($diretorioUpload, 0755, true);
        }

        $caminhoCompleto = $diretorioUpload . $novoNome;

        if (move_uploaded_file($file['tmp_name'], $caminhoCompleto)) {
            // Apaga a foto antiga se existir
            if ($foto_url && file_exists(__DIR__ . '/' . $foto_url)) {
                @unlink(__DIR__ . '/' . $foto_url);
            }
            $foto_url = $caminhoCompleto;
        } else {
            throw new Exception('Erro ao guardar a fotografia no servidor.');
        }
    }

    // ------------------------------------------------------------------
    // PERSISTÊNCIA NA BASE DE DADOS (INSERT / UPDATE)
    // ------------------------------------------------------------------
    if ($id) {
        // EDIÇÃO (UPDATE)
        if (!empty($senha)) {
            // Atualiza com nova senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sqlUpdate = "UPDATE utilizadores SET perfil_id = ?, nome = ?, email = ?, nip = ?, telefone = ?, estado = ?, qr_code_passe = ?, senha_hash = ?, foto_url = ? WHERE id = ?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("issssssssi", $perfil_id, $nome, $email, $nip, $telefone, $estado, $qr_code, $senha_hash, $foto_url, $id);
        } else {
            // Mantém a senha antiga
            $sqlUpdate = "UPDATE utilizadores SET perfil_id = ?, nome = ?, email = ?, nip = ?, telefone = ?, estado = ?, qr_code_passe = ?, foto_url = ? WHERE id = ?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("isssssssi", $perfil_id, $nome, $email, $nip, $telefone, $estado, $qr_code, $foto_url, $id);
        }
        
        $stmt->execute();
        $response['message'] = 'Técnico atualizado com sucesso!';
    } else {
        // CADASTRO (INSERT)
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sqlInsert = "INSERT INTO utilizadores (perfil_id, nome, email, nip, telefone, estado, qr_code_passe, senha_hash, foto_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlInsert);
        $stmt->bind_param("issssssss", $perfil_id, $nome, $email, $nip, $telefone, $estado, $qr_code, $senha_hash, $foto_url);
        $stmt->execute();
        $response['message'] = 'Técnico cadastrado com sucesso!';
    }

    $stmt->close();
    $response['success'] = true;

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);