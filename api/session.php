<?php
// api/session.php

// Garante que a sessão é iniciada de forma segura apenas uma vez
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

/**
 * Função para verificar se o utilizador está autenticado.
 * Redireciona para o index.php caso não exista uma sessão ativa.
 */
function verificarAutenticacao() {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: ../index.php?erro=nao_autorizado");
        exit;
    }
}

/**
 * Retorna todos os dados mapeados do utilizador logado
 * com base na estrutura da tabela 'utilizadores'.
 */
function getUtilizadorLogado() {
    return [
        'id'               => $_SESSION['user_id']               ?? null,
        'nome'             => $_SESSION['user_nome']             ?? 'Utilizador',
        'email'            => $_SESSION['user_email']            ?? '',
        'nip'              => $_SESSION['user_nip']              ?? '',
        'perfil_id'        => $_SESSION['user_perfil']           ?? null,
        'foto_url'         => $_SESSION['user_foto']             ?? '',
        'notif_email'      => $_SESSION['user_notif_email']      ?? 1,
        'notif_novos'      => $_SESSION['user_notif_novos']      ?? 1,
        'notif_pendentes'  => $_SESSION['user_notif_pendentes']  ?? 1
    ];
}

// Executa a proteção da rota automaticamente ao incluir este ficheiro
verificarAutenticacao();