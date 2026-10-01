<?php
// api/get_sessao_usuario.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'session.php'; // Garante autenticação

$user = getUtilizadorLogado();

echo json_encode([
    'success' => true,
    'utilizador' => $user
]);