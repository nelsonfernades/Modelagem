<?php
// Configurações de acesso à base de dados
$host = "localhost";
$usuario = "root";
$senha = ""; // Insira a sua senha do MySQL, se houver
$banco = "pgr_instrucao_db";

// Criar a conexão via MySQLi
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar se ocorreu algum erro de conexão
if ($conn->connect_error) {
    die("Falha na conexão com a base de dados: " . $conn->connect_error);
}

// Definir o charset para UTF-8 (suporte a acentos e caracteres especiais)
$conn->set_charset("utf8mb4");