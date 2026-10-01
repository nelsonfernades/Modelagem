<?php
// Previne exibição de erros HTML para não corromper o JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Desativa exceções fatais automáticas do MySQLi
mysqli_report(MYSQLI_REPORT_OFF);

// Cabeçalhos HTTP para API REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Trata requisição Preflight (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 1. Conexão com o Banco de Dados
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

// 2. Captura o ID via Query String (?id=X)
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$method = $_SERVER['REQUEST_METHOD'];

// 3. Roteamento da requisição
switch ($method) {
    case 'PATCH':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["erro" => "ID do utilizador é obrigatório."]);
            exit();
        }
        atualizarEstadoUtilizador($db, $id);
        break;

    case 'GET':
        if ($id) {
            obterEstadoUtilizador($db, $id);
        } else {
            listarEstadosUtilizadores($db);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["erro" => "Método não permitido."]);
        break;
}

// ==========================================
// FUNÇÕES DE OPERAÇÃO NA TABELA 'UTILIZADORES'
// ==========================================

/**
 * Atualiza diretamente o campo 'estado' na tabela 'utilizadores'
 */
function atualizarEstadoUtilizador($db, $id) {
    try {
        $input = file_get_contents("php://input");
        $dados = json_decode($input, true);

        if (!isset($dados['estado']) || empty(trim($dados['estado']))) {
            http_response_code(400);
            echo json_encode(["erro" => "O parâmetro 'estado' é obrigatório."]);
            return;
        }

        $novoEstado = strtolower(trim($dados['estado']));

        // Validação estrita contra os valores permitidos no ENUM da tabela
        $enumPermitidos = ['ativo', 'pendente', 'bloqueado'];

        if (!in_array($novoEstado, $enumPermitidos)) {
            http_response_code(400);
            echo json_encode([
                "erro" => "Estado inválido. Valores aceitos: 'ativo', 'pendente' ou 'bloqueado'."
            ]);
            return;
        }

        // Executa o UPDATE no campo 'estado' da tabela 'utilizadores'
        $stmt = $db->prepare("UPDATE utilizadores SET estado = ? WHERE id = ?");
        
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(["erro" => "Erro na consulta SQL: " . $db->error]);
            return;
        }

        $stmt->bind_param("si", $novoEstado, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows >= 0) {
                // Mensagem formatada para o SweetAlert2 no frontend
                $mensagem = ($novoEstado === 'bloqueado') 
                    ? "Acesso bloqueado com sucesso!" 
                    : "Acesso reativado com sucesso!";

                http_response_code(200);
                echo json_encode([
                    "sucesso" => true,
                    "mensagem" => $mensagem,
                    "id" => $id,
                    "estado" => $novoEstado
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["erro" => "Utilizador não encontrado."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao atualizar estado: " . $stmt->error]);
        }

        $stmt->close();

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Exceção no servidor: " . $e->getMessage()]);
    }
}

/**
 * Consulta o estado atual de um único utilizador
 */
function obterEstadoUtilizador($db, $id) {
    $stmt = $db->prepare("SELECT id, nome, email, nip, estado FROM utilizadores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        http_response_code(200);
        echo json_encode($row);
    } else {
        http_response_code(404);
        echo json_encode(["erro" => "Utilizador não encontrado."]);
    }
    $stmt->close();
}

/**
 * Retorna a lista simples de utilizadores com seus estados atuais
 */
function listarEstadosUtilizadores($db) {
    $sql = "SELECT id, nome, email, nip, estado FROM utilizadores ORDER BY id DESC";
    $result = $db->query($sql);

    if (!$result) {
        http_response_code(500);
        echo json_encode(["erro" => $db->error]);
        return;
    }

    $lista = [];
    while ($row = $result->fetch_assoc()) {
        $lista[] = $row;
    }

    http_response_code(200);
    echo json_encode($lista);
}