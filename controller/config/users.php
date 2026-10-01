<?php
// Desativa exibição de erros HTML brutais para não corromper o retorno JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Cabeçalhos HTTP para REST API e CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Trata requisição Preflight do navegador (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inclui o arquivo de conexão existente
$caminho_conexao = '../../api/conexao.php';
if (!file_exists($caminho_conexao)) {
    http_response_code(500);
    echo json_encode(["erro" => "Arquivo conexao.php nao encontrado em: " . realpath(__DIR__ . '/..')]);
    exit();
}

require_once $caminho_conexao;

// Identifica a variável da conexão ($conexao ou $conn)
$db = isset($conexao) ? $conexao : (isset($conn) ? $conn : null);

if (!$db || $db->connect_error) {
    http_response_code(500);
    echo json_encode(["erro" => "Falha na conexão com o banco de dados MySQL."]);
    exit();
}

// Captura o ID caso seja passado via Query String (?id=X) ou PATH_INFO (/1)
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$id && isset($_SERVER['PATH_INFO'])) {
    $path_parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
    if (!empty($path_parts[0]) && is_numeric($path_parts[0])) {
        $id = intval($path_parts[0]);
    }
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if ($id) {
            obterUtilizador($db, $id);
        } else {
            listarUtilizadores($db);
        }
        break;

    case 'POST':
        cadastrarUtilizador($db);
        break;

    case 'PUT':
        if ($id) {
            atualizarUtilizador($db, $id);
        } else {
            http_response_code(400);
            echo json_encode(["erro" => "ID do operador e obrigatorio."]);
        }
        break;

    case 'PATCH':
        if ($id) {
            atualizarStatus($db, $id);
        } else {
            http_response_code(400);
            echo json_encode(["erro" => "ID do operador e obrigatorio."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["erro" => "Metodo nao permitido."]);
        break;
}

// ==========================================
// FUNÇÕES DE OPERAÇÃO
// ==========================================

function listarUtilizadores($db) {
    // Usamos 'p.nome AS cargo' e subconsultas para calcular métricas de processos por utilizador
    $sql = "SELECT 
                u.id, 
                u.nome, 
                u.nip, 
                p.nome AS cargo, 
                p.nome AS perfil_nome,
                u.email, 
                u.telefone, 
                u.perfil_id, 
                u.estado,
                DATE_FORMAT(u.criado_em, '%d/%m/%Y %H:%i') AS criado_em,
                
                -- Quantidade de processos atribuídos hoje a este utilizador
                (SELECT COUNT(*) 
                 FROM processos 
                 WHERE tecnico_responsavel_id = u.id 
                   AND DATE(criado_em) = CURRENT_DATE()) AS processos_hoje,
                
                -- Total acumulado de processos deste utilizador
                (SELECT COUNT(*) 
                 FROM processos 
                 WHERE tecnico_responsavel_id = u.id) AS total_processos

            FROM utilizadores u 
            LEFT JOIN perfis p ON u.perfil_id = p.id 
            ORDER BY u.id DESC";

    $result = $db->query($sql);

    if (!$result) {
        http_response_code(500);
        echo json_encode([
            "erro" => "Erro na consulta à base de dados.",
            "detalhe" => $db->error
        ]);
        return;
    }

    $utilizadores = [];
    while ($row = $result->fetch_assoc()) {
        $row['processos_hoje'] = intval($row['processos_hoje']);
        $row['total_processos'] = intval($row['total_processos']);
        $utilizadores[] = $row;
    }

    http_response_code(200);
    echo json_encode($utilizadores);
}

function obterUtilizador($db, $id) {
    $stmt = $db->prepare("SELECT 
                            u.id, 
                            u.nome, 
                            u.nip, 
                            p.nome AS cargo, 
                            p.nome AS perfil_nome, 
                            u.email, 
                            u.telefone, 
                            u.perfil_id, 
                            u.estado,
                            (SELECT COUNT(*) FROM processos WHERE tecnico_responsavel_id = u.id AND DATE(criado_em) = CURRENT_DATE()) AS processos_hoje,
                            (SELECT COUNT(*) FROM processos WHERE tecnico_responsavel_id = u.id) AS total_processos
                          FROM utilizadores u 
                          LEFT JOIN perfis p ON u.perfil_id = p.id 
                          WHERE u.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $row['processos_hoje'] = intval($row['processos_hoje']);
        $row['total_processos'] = intval($row['total_processos']);
        http_response_code(200);
        echo json_encode($row);
    } else {
        http_response_code(404);
        echo json_encode(["erro" => "Operador nao encontrado."]);
    }
    $stmt->close();
}

function cadastrarUtilizador($db) {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (empty($dados['nome']) || empty($dados['nip']) || empty($dados['email'])) {
        http_response_code(400);
        echo json_encode(["erro" => "Preencha nome, NIP e email."]);
        return;
    }

    $nome = $dados['nome'];
    $nip = $dados['nip'];
    $email = $dados['email'];
    $telefone = !empty($dados['telefone']) ? $dados['telefone'] : 'N/A';
    $perfil_id = isset($dados['perfil_id']) ? intval($dados['perfil_id']) : 3;
    $estado = isset($dados['estado']) ? $dados['estado'] : 'ativo';

    // Senha padrão criptografada caso não venha no payload
    $senha_raw = !empty($dados['senha']) ? $dados['senha'] : '123456';
    $senha_hash = password_hash($senha_raw, PASSWORD_DEFAULT);

    // Ajustado para colunas existentes na tabela 'utilizadores' (sem a coluna 'cargo')
    $stmt = $db->prepare("INSERT INTO utilizadores (nome, nip, email, telefone, perfil_id, estado, senha_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiss", $nome, $nip, $email, $telefone, $perfil_id, $estado, $senha_hash);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["mensagem" => "Operador credenciado com sucesso!", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["erro" => "Erro ao cadastrar: " . $stmt->error]);
    }
    $stmt->close();
}

function atualizarUtilizador($db, $id) {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (empty($dados['perfil_id']) || empty($dados['estado'])) {
        http_response_code(400);
        echo json_encode(["erro" => "Perfil e estado sao obrigatorios."]);
        return;
    }

    $perfil_id = intval($dados['perfil_id']);
    $estado = $dados['estado'];

    $stmt = $db->prepare("UPDATE utilizadores SET perfil_id = ?, estado = ? WHERE id = ?");
    $stmt->bind_param("isi", $perfil_id, $estado, $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["mensagem" => "Operador atualizado!"]);
    } else {
        http_response_code(500);
        echo json_encode(["erro" => $stmt->error]);
    }
    $stmt->close();
}

function atualizarStatus($db, $id) {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (empty($dados['estado'])) {
        http_response_code(400);
        echo json_encode(["erro" => "Estado nao informado."]);
        return;
    }

    $estado = $dados['estado'];

    $stmt = $db->prepare("UPDATE utilizadores SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $estado, $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["mensagem" => "Status alterado para " . $estado]);
    } else {
        http_response_code(500);
        echo json_encode(["erro" => $stmt->error]);
    }
    $stmt->close();
}