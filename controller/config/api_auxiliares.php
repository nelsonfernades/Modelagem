<?php
// Desativa exibição de erros/warnings no HTML para evitar corromper a resposta JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Inclui o ficheiro de conexão existente
require_once '../../api/conexao.php'; 

// Valida se a conexão MySQLi ($conn) existe e está ativa
if (!isset($conn) || !($conn instanceof mysqli) || $conn->connect_error) {
    echo json_encode([
        "sucesso" => false, 
        "erro" => "Erro de Conexão: A variável \$conn do MySQLi não foi encontrada ou falhou."
    ]);
    exit;
}

// Configura o charset para suportar acentuação e caracteres especiais
$conn->set_charset("utf8mb4");

// Lista de tabelas permitidas (Camada de segurança para validar a entrada da requisição)
$tabelasPermitidas = ['profissoes', 'nacionalidades', 'tipos_crime', 'cadeias'];

$acao = $_GET['acao'] ?? '';

try {
    // ------------------------------------------
    // 1. LISTAR REGISTOS
    // ------------------------------------------
    if ($acao === 'listar') {
        $tabela = $_GET['tabela'] ?? '';
        if (!in_array($tabela, $tabelasPermitidas)) {
            throw new Exception("Tabela não permitida.");
        }

        $sql = "SELECT id, nome FROM `{$tabela}` ORDER BY nome ASC";
        $resultado = $conn->query($sql);

        if (!$resultado) {
            throw new Exception("Erro na consulta: " . $conn->error);
        }

        // Obtém todos os registos em formato de array associativa
        $registos = $resultado->fetch_all(MYSQLI_ASSOC);

        echo json_encode(["sucesso" => true, "registos" => $registos]);
        exit;
    }

    // ------------------------------------------
    // 2. SALVAR (INSERIR OU EDITAR)
    // ------------------------------------------
    if ($acao === 'salvar') {
        $tabela = $_POST['tabela'] ?? '';
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $nome = trim($_POST['nome'] ?? '');

        if (!in_array($tabela, $tabelasPermitidas) || empty($nome)) {
            throw new Exception("Dados inválidos fornecidos.");
        }

        if ($id) {
            // Atualizar registo existente
            $stmt = $conn->prepare("UPDATE `{$tabela}` SET nome = ? WHERE id = ?");
            $stmt->bind_param("si", $nome, $id);
        } else {
            // Inserir novo registo
            $stmt = $conn->prepare("INSERT INTO `{$tabela}` (nome) VALUES (?)");
            $stmt->bind_param("s", $nome);
        }

        if (!$stmt->execute()) {
            throw new Exception("Erro ao guardar: " . $stmt->error);
        }

        echo json_encode(["sucesso" => true]);
        exit;
    }

    // ------------------------------------------
    // 3. ELIMINAR REGISTO
    // ------------------------------------------
    if ($acao === 'eliminar') {
        $tabela = $_POST['tabela'] ?? '';
        $id = (int)($_POST['id'] ?? 0);

        if (!in_array($tabela, $tabelasPermitidas) || !$id) {
            throw new Exception("Parâmetros inválidos para eliminação.");
        }

        $stmt = $conn->prepare("DELETE FROM `{$tabela}` WHERE id = ?");
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao eliminar: " . $stmt->error);
        }

        echo json_encode(["sucesso" => true]);
        exit;
    }

} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(["sucesso" => false, "erro" => $e->getMessage()]);
}