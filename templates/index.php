<?php
// 1. Inicia a sessão para poder aceder aos dados ativos
session_start();

// 2. Limpa todas as variáveis de sessão associadas
$_SESSION = array();

// 3. Destrói o cookie da sessão no navegador (boa prática de segurança)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destrói completamente a sessão no servidor
session_destroy();

// 5. Redireciona o utilizador de volta para a tela de login
// (Ajuste o caminho relativo '../index.php' ou 'login.php' conforme a localização real deste ficheiro)
header("Location: ../index.php");
exit;