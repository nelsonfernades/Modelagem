<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_OFF);

// Aumenta os limites de memória e tempo de execução para bancos grandes
ini_set('memory_limit', '512M');
set_time_limit(300);

header("Access-Control-Allow-Origin: *");
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
        // Rota para descarregar o ficheiro .sql no computador
        if (isset($_GET['download'])) {
            downloadBackupFile($_GET['download']);
        } else {
            obterDadosSalvaguarda($db);
        }
        break;

    case 'POST':
        executarBackupCompletoBD($db);
        break;

    default:
        http_response_code(405);
        echo json_encode(["erro" => "Método não permitido."]);
        break;
}

/**
 * Retorna dados para o painel (Último backup + lista de audit logs)
 */
function obterDadosSalvaguarda($db)
{
    header("Content-Type: application/json; charset=UTF-8");

    $resBackup = $db->query("SELECT data_backup, nome_ficheiro, tamanho_mb FROM backups_historico ORDER BY id DESC LIMIT 1");
    $ultimoBackup = $resBackup ? $resBackup->fetch_assoc() : null;

    $dataUltimoBackup = $ultimoBackup
        ? date('d/m/Y \à\s H:i', strtotime($ultimoBackup['data_backup'])) . " (" . ($ultimoBackup['tamanho_mb'] ?? '0 MB') . ")"
        : 'Nenhum backup realizado';

    $resLogs = $db->query("SELECT DATE_FORMAT(data_hora, '%d/%m/%Y %H:%i:%s') as data_formatada, operador, acao, ip, resultado FROM audit_logs ORDER BY id DESC LIMIT 50");

    $logs = [];
    if ($resLogs) {
        while ($row = $resLogs->fetch_assoc()) {
            $logs[] = $row;
        }
    }

    echo json_encode([
        "ultimo_backup" => $dataUltimoBackup,
        "status_servidor" => "Ativo (Sincronizado)",
        "logs" => $logs
    ]);
}

/**
 * Força o download do ficheiro .sql diretamente no browser do utilizador
 */
function downloadBackupFile($nomeFicheiro)
{
    $nomeFicheiro = basename($nomeFicheiro);
    $diretorioBackups = __DIR__ . '/../../backups/';
    $caminhoCompleto = $diretorioBackups . $nomeFicheiro;

    if (!empty($nomeFicheiro) && pathinfo($nomeFicheiro, PATHINFO_EXTENSION) === 'sql' && file_exists($caminhoCompleto)) {
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $nomeFicheiro . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($caminhoCompleto));

        readfile($caminhoCompleto);
        exit();
    } else {
        http_response_code(404);
        echo "Ficheiro de backup não encontrado.";
        exit();
    }
}

/**
 * Gera um DUMP INTEGRAL da base de dados para recuperação de desastres
 */
function executarBackupCompletoBD($db)
{
    header("Content-Type: application/json; charset=UTF-8");

    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $operador = "Administrador do Sistema";

        $diretorioBackups = __DIR__ . '/../../backups/';
        if (!is_dir($diretorioBackups)) {
            mkdir($diretorioBackups, 0755, true);
        }

        // Nome único com data e hora
        $nomeFicheiro = 'BACKUP_TOTAL_BANCO_' . date('Y-m-d_H-i-s') . '.sql';
        $caminhoCompleto = $diretorioBackups . $nomeFicheiro;

        // Gera o dump estruturado
        $gerado = exportarBancoDeDadosCompleto($db, $caminhoCompleto);

        if (!$gerado || !file_exists($caminhoCompleto) || filesize($caminhoCompleto) === 0) {
            throw new Exception("Falha ao gerar o ficheiro físico de backup.");
        }

        $tamanhoMB = round(filesize($caminhoCompleto) / (1024 * 1024), 2) . ' MB';

        // 1. Regista no Histórico de Backups (com verificação de erro do prepare)
        $sqlBkp = "INSERT INTO backups_historico (data_backup, status, tipo, executado_por, nome_ficheiro, tamanho_mb) VALUES (NOW(), 'SUCESSO', 'MANUAL', ?, ?, ?)";
        $stmtBkp = $db->prepare($sqlBkp);

        if (!$stmtBkp) {
            throw new Exception("Erro de SQL ao registar backup: " . $db->error);
        }

        $stmtBkp->bind_param("sss", $operador, $nomeFicheiro, $tamanhoMB);
        $stmtBkp->execute();
        $stmtBkp->close();

        // 2. Regista no Audit Log (com verificação de erro do prepare)
        $acao = "Cópia de Segurança Total Exportada: {$nomeFicheiro} ({$tamanhoMB})";
        $sqlLog = "INSERT INTO audit_logs (data_hora, operador, acao, ip, resultado) VALUES (NOW(), ?, ?, ?, 'SUCESSO')";
        $stmtLog = $db->prepare($sqlLog);

        if (!$stmtLog) {
            throw new Exception("Erro de SQL ao registar log: " . $db->error);
        }

        $stmtLog->bind_param("sss", $operador, $acao, $ip);
        $stmtLog->execute();
        $stmtLog->close();

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Backup completo da base de dados gerado com sucesso!",
            "ficheiro" => $nomeFicheiro,
            "tamanho" => $tamanhoMB
        ]);

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro crítico ao gerar backup: " . $e->getMessage()]);
    }
}

/**
 * Função responsável por varrer TODAS as tabelas, estruturas e dados para restauração total
 */
function exportarBancoDeDadosCompleto($db, $caminhoFicheiro)
{
    $dbNameRes = $db->query("SELECT DATABASE()");
    $dbName = $dbNameRes ? $dbNameRes->fetch_row()[0] : 'base_dados';

    $handle = fopen($caminhoFicheiro, 'w');
    if (!$handle)
        return false;

    // Cabeçalho SQL para garantir compatibilidade e desativar restrições durante o restauro
    $header = "-- ========================================================\n";
    $header .= "-- DUMP COMPLETO PARA RECUPERAÇÃO DE DESASTRES\n";
    $header .= "-- Base de Dados: `{$dbName}`\n";
    $header .= "-- Data de Geracão: " . date('Y-m-d H:i:s') . "\n";
    $header .= "-- ========================================================\n\n";
    $header .= "SET FOREIGN_KEY_CHECKS=0;\n";
    $header .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $header .= "SET AUTOCOMMIT = 0;\n";
    $header .= "START TRANSACTION;\n";
    $header .= "SET time_zone = \"+00:00\";\n\n";
    fwrite($handle, $header);

    // Obtém todas as tabelas
    $tabelas = [];
    $resTabelas = $db->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    while ($row = $resTabelas->fetch_row()) {
        $tabelas[] = $row[0];
    }

    foreach ($tabelas as $tabela) {
        fwrite($handle, "\n-- --------------------------------------------------------\n");
        fwrite($handle, "-- Estrutura da tabela `{$tabela}`\n");
        fwrite($handle, "-- --------------------------------------------------------\n");
        fwrite($handle, "DROP TABLE IF EXISTS `{$tabela}`;\n");

        $resCreate = $db->query("SHOW CREATE TABLE `{$tabela}`");
        if ($resCreate && $rowCreate = $resCreate->fetch_row()) {
            fwrite($handle, $rowCreate[1] . ";\n\n");
        }

        // Exportação de dados em lotes (evita sobrecarga de memória)
        $resCount = $db->query("SELECT COUNT(*) FROM `{$tabela}`");
        $totalLinhas = $resCount ? $resCount->fetch_row()[0] : 0;

        if ($totalLinhas > 0) {
            fwrite($handle, "-- Extraindo dados da tabela `{$tabela}`\n");
            $limite = 500;
            $offset = 0;

            while ($offset < $totalLinhas) {
                $resDados = $db->query("SELECT * FROM `{$tabela}` LIMIT {$limite} OFFSET {$offset}");
                if ($resDados && $resDados->num_rows > 0) {
                    $insertPrefix = "INSERT INTO `{$tabela}` VALUES ";
                    $linhasInsert = [];

                    while ($row = $resDados->fetch_assoc()) {
                        $valores = [];
                        foreach ($row as $val) {
                            if (is_null($val)) {
                                $valores[] = "NULL";
                            } else {
                                $valores[] = "'" . $db->real_escape_string($val) . "'";
                            }
                        }
                        $linhasInsert[] = "(" . implode(", ", $valores) . ")";
                    }

                    fwrite($handle, $insertPrefix . implode(",\n", $linhasInsert) . ";\n");
                }
                $offset += $limite;
            }
            fwrite($handle, "\n");
        }
    }

    // Rodapé para reativar chaves e efetivar transação
    $footer = "\nCOMMIT;\n";
    $footer .= "SET FOREIGN_KEY_CHECKS=1;\n";
    $footer .= "-- FIM DO DUMP COMPLETO --\n";
    fwrite($handle, $footer);

    fclose($handle);
    return true;
}