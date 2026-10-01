<?php
// api/auth.php
session_start();

if (ob_get_length()) {
    ob_clean();
}

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    $caminhoConexao = 'conexao.php';
    if (!file_exists($caminhoConexao)) {
        throw new Exception("O ficheiro conexao.php não foi encontrado.");
    }
    require_once $caminhoConexao;

    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception("A conexão \$conn não é uma instância válida do mysqli.");
    }

    $input = json_decode(file_get_contents('php://input'), true);

    $usuario = isset($input['usuario']) ? trim($input['usuario']) : (isset($_POST['usuario']) ? trim($_POST['usuario']) : '');
    $senha = isset($input['senha']) ? trim($input['senha']) : (isset($_POST['senha']) ? trim($_POST['senha']) : '');

    if (empty($usuario) || empty($senha)) {
        echo json_encode([
            'success' => false,
            'message' => 'Por favor, preencha o utilizador e a senha.'
        ]);
        exit;
    }

    // ======================================================================
    // UTILIZADOR MASTER / EQUIPA TÉCNICA (MONITORIZAÇÃO SEM BASE DE DADOS)
    // ======================================================================
    $master_user = 'suporte.pgr';
    $master_pass = 'pgr@2026!';

    if ($usuario === $master_user && $senha === $master_pass) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = -999;
        $_SESSION['user_nome'] = 'Equipa Técnica (Monitorização)';
        $_SESSION['user_email'] = 'suporte.pgr@suporte.ao';
        $_SESSION['user_nip'] = 'SUPORTE-00';
        $_SESSION['user_perfil'] = 99; // Perfil especial para monitorização técnica
        $_SESSION['user_nivel_acesso'] = 3; // Nível máximo de acesso (0 a 3) para visualização global
        $_SESSION['user_foto'] = '';
        $_SESSION['user_notif_email'] = 1;
        $_SESSION['user_notif_novos'] = 1;
        $_SESSION['user_notif_pendentes'] = 1;

        echo json_encode([
            'success' => true,
            'message' => 'Sessão de monitorização técnica iniciada com sucesso!',
            'redirect' => 'templates/validacao_reicidencia.php'
        ]);
        exit;
    }
    // ======================================================================

    // Consulta expandida para permitir login por Email, NIP ou Nome (Fluxo normal da BD)
    $sql = "SELECT id, nome, email, nip, senha_hash, perfil_id, estado FROM utilizadores WHERE email = ? OR nip = ? OR nome = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . $conn->error);
    }

    $stmt->bind_param("sss", $usuario, $usuario, $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($db_id, $db_nome, $db_email, $db_nip, $db_senha_hash, $db_perfil_id, $db_estado);
        $stmt->fetch();

        // Verifica o estado do utilizador
        if ($db_estado !== 'ativo') {
            echo json_encode([
                'success' => false,
                'message' => 'Esta conta encontra-se inativa ou bloqueada. Contacte o administrador.'
            ]);
            exit;
        }

        // Validação da senha (suporte a password_verify ou texto plano)
        $senhaValida = false;
        if (password_verify($senha, $db_senha_hash) || $senha === $db_senha_hash) {
            $senhaValida = true;
        }

        if ($senhaValida) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $db_id;
            $_SESSION['user_nome'] = $db_nome;
            $_SESSION['user_email'] = $db_email;
            $_SESSION['user_nip'] = $db_nip;
            $_SESSION['user_perfil'] = $db_perfil_id;
            $_SESSION['user_nivel_acesso'] = 3; // Nível padrão elevado para utilizadores internos autenticados
            $_SESSION['user_foto'] = '';
            $_SESSION['user_notif_email'] = 1;
            $_SESSION['user_notif_novos'] = 1;
            $_SESSION['user_notif_pendentes'] = 1;

            echo json_encode([
                'success' => true,
                'message' => 'Login efetuado com sucesso!',
                'redirect' => 'templates/validacao_reicidencia.php'
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Senha incorreta.'
            ]);
            exit;
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Utilizador não encontrado com este Email, NIP ou Nome.'
        ]);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno no servidor: ' . $e->getMessage()
    ]);
}