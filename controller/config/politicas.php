<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_OFF);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$caminho_conexao = '../../api/conexao.php';
if (!file_exists($caminho_conexao)) {
    http_response_code(500);
    echo json_encode(["erro" => "Ficheiro conexao.php não encontrado."]);
    exit();
}

require_once $caminho_conexao;
$db = isset($conexao) ? $conexao : (isset($conn) ? $conn : null);

if (!$db || $db->connect_error) {
    http_response_code(500);
    echo json_encode(["erro" => "Falha na conexão com a base de dados."]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        obterPoliticas($db);
        break;

    case 'POST':
        guardarPoliticas($db);
        break;

    default:
        http_response_code(405);
        echo json_encode(["erro" => "Método não permitido."]);
        break;
}

/**
 * Retorna as políticas de instrução atuais (ID = 1)
 */
function obterPoliticas($db) {
    $res = $db->query("SELECT * FROM politicas_instrucao WHERE id = 1");
    if ($row = $res->fetch_assoc()) {
        http_response_code(200);
        echo json_encode($row);
    } else {
        // Retorna padrões se não encontrar registo
        echo json_encode([
            "prazo_max_instrucao" => 90,
            "antecedencia_alerta" => 15,
            "tamanho_max_anexo" => 2000,
            "algoritmo_hash" => "SHA-256",
            "exigir_2fa" => 1,
            "restringir_vpn" => 1
        ]);
    }
}

/**
 * Atualiza ou insere as políticas no registo único (ID = 1)
 */
function guardarPoliticas($db) {
    try {
        $input = file_get_contents("php://input");
        $dados = json_decode($input, true);

        if (!$dados) {
            http_response_code(400);
            echo json_encode(["erro" => "Dados inválidos recebidos."]);
            return;
        }

        $prazo_max = intval($dados['prazo_max_instrucao'] ?? 90);
        $antecedencia = intval($dados['antecedencia_alerta'] ?? 15);
        $tamanho_anexo = intval($dados['tamanho_max_anexo'] ?? 2000);
        $exigir_2fa = !empty($dados['exigir_2fa']) ? 1 : 0;
        $restringir_vpn = !empty($dados['restringir_vpn']) ? 1 : 0;

        $sql = "INSERT INTO politicas_instrucao (id, prazo_max_instrucao, antecedencia_alerta, tamanho_max_anexo, algoritmo_hash, exigir_2fa, restringir_vpn)
                VALUES (1, ?, ?, ?, 'SHA-256', ?, ?)
                ON DUPLICATE KEY UPDATE 
                    prazo_max_instrucao = VALUES(prazo_max_instrucao),
                    antecedencia_alerta = VALUES(antecedencia_alerta),
                    tamanho_max_anexo = VALUES(tamanho_max_anexo),
                    exigir_2fa = VALUES(exigir_2fa),
                    restringir_vpn = VALUES(restringir_vpn)";

        $stmt = $db->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(["erro" => "Erro SQL: " . $db->error]);
            return;
        }

        $stmt->bind_param("iiiii", $prazo_max, $antecedencia, $tamanho_anexo, $exigir_2fa, $restringir_vpn);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode([
                "sucesso" => true,
                "mensagem" => "Políticas de instrução guardadas com sucesso!"
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao guardar no banco: " . $stmt->error]);
        }
        $stmt->close();

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Exceção no servidor: " . $e->getMessage()]);
    }
}