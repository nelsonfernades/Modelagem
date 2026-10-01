<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
session_start();

// 4. Carregar Dompdf (Autoload do Composer - Ajusta o caminho se necessário)
require_once '../../dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 1. Validar Sessão
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_perfil'])) {
    die("Acesso negado. Sessão inválida ou expirada.");
}

$processoId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($processoId <= 0) {
    die("ID do processo inválido.");
}

try {
    // Ajusta o caminho da conexão conforme a localização real deste ficheiro
    require_once '../../api/conexao.php';

    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception("Erro de conexão com a base de dados.");
    }

    // 2. Buscar Dados Completos do Processo e Arguido
    $sql = "SELECT p.id, p.num_processo, p.nome_arguido, p.criado_em, p.estado_processo, 
            u.nome AS tecnico_nome, u.email AS tecnico_email,
            ar.id as arguido_id, ar.alcunha, ar.bi, ar.data_nascimento, ar.nacionalidade, 
            ar.naturalidade, ar.estado_civil, ar.profissao, ar.idade, ar.filiacao, ar.morada, ar.contacto,
            ar.tipo_crime, ar.tipo_medida, ar.prazo_quantidade, ar.prazo_unidade, ar.biometria_face
            FROM processos p
            INNER JOIN arguidos ar ON ar.processo_id = p.id
            JOIN utilizadores u ON p.tecnico_responsavel_id = u.id
            WHERE p.id = ? LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $processoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $dados = $result->fetch_assoc();
    $stmt->close();

    if (!$dados) {
        throw new Exception("Registo de processo ou arguido não encontrado.");
    }

    // 3. Buscar Bens Apreendidos e Agrupar Dinamicamente (Sem alterar a Base de Dados)
    $bens = [];
    if (!empty($dados['arguido_id'])) {
        $stmtBens = $conn->prepare("SELECT descricao, categoria FROM bens WHERE arguido_id = ?");
        $stmtBens->bind_param("i", $dados['arguido_id']);
        $stmtBens->execute();
        $resBens = $stmtBens->get_result();

        $bensTemp = [];
        while ($b = $resBens->fetch_assoc()) {
            $chave = strtolower(trim($b['descricao'])) . '|' . $b['categoria'];

            if (isset($bensTemp[$chave])) {
                $bensTemp[$chave]['quantidade']++;
            } else {
                $bensTemp[$chave] = [
                    'descricao' => $b['descricao'],
                    'categoria' => $b['categoria'],
                    'quantidade' => 1
                ];
            }
        }
        $stmtBens->close();
        $bens = array_values($bensTemp);
    }

    $conn->close();

    // Formatações úteis
    $dataNascFormatada = !empty($dados['data_nascimento']) ? date('d/m/Y', strtotime($dados['data_nascimento'])) : '---';
    $prazoCustodia = '48 Horas';
    if (!empty($dados['prazo_quantidade']) && !empty($dados['prazo_unidade'])) {
        $prazoCustodia = $dados['prazo_quantidade'] . ' ' . ucfirst($dados['prazo_unidade']);
    }

    // Processar Foto (Caminho absoluto do servidor para o Dompdf)
    $fotoHtml = '<div style="width: 90px; height: 110px; background: #eee; border: 1px solid #ccc; text-align: center; line-height: 110px; font-size: 10px; color: #666;">Sem Foto</div>';
    if (!empty($dados['biometria_face'])) {
        $caminhoFoto = $dados['biometria_face'];
        if (!str_starts_with($caminhoFoto, 'http') && !str_starts_with($caminhoFoto, 'data:')) {
            $caminhoServidor = __DIR__ . '/../../controller/arguidouploads/' . str_replace('uploads/', '', $caminhoFoto);
            if (file_exists($caminhoServidor)) {
                $tipo = pathinfo($caminhoServidor, PATHINFO_EXTENSION);
                $dadosImagem = file_get_contents($caminhoServidor);
                $base64 = 'data:image/' . $tipo . ';base64,' . base64_encode($dadosImagem);
                $fotoHtml = '<img src="' . $base64 . '" style="width: 90px; height: 110px; object-fit: cover; border: 1px solid #999;" />';
            }
        } else {
            $fotoHtml = '<img src="' . $caminhoFoto . '" style="width: 90px; height: 110px; object-fit: cover; border: 1px solid #999;" />';
        }
    }

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);

    // 5. Construção do HTML do PDF
    $html = '
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: "Helvetica", "Arial", sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; line-height: 1.4; }
            .header { text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 8px; margin-bottom: 15px; }
            .header h2 { margin: 0; font-size: 15px; text-transform: uppercase; color: #1e293b; }
            .header p { margin: 3px 0 0 0; font-size: 10px; color: #64748b; }
            
            .box-top { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
            .box-top td { vertical-align: top; }
            
            .section-title { background: #1e293b; color: #fff; font-size: 10px; font-weight: bold; text-transform: uppercase; padding: 4px 8px; margin-top: 12px; margin-bottom: 8px; }
            
            table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            table.data-table td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
            table.data-table td.label { font-weight: bold; color: #475569; width: 28%; }
            table.data-table td.value { color: #0f172a; width: 22%; }
            
            .bens-list { margin: 0; padding-left: 15px; }
            .bens-list li { margin-bottom: 3px; }
            
            .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #cbd5e1; padding-top: 5px; }
        </style>
    </head>
    <body>

        <div class="header">
            <h2>Ficha Geral de Identificação do Arguido</h2>
            <p>Processo N.º: <strong>' . htmlspecialchars($dados['num_processo']) . '</strong> | Emitido em: ' . date('d/m/Y H:i') . '</p>
        </div>

        <table class="box-top">
            <tr>
                <td style="width: 100px;">' . $fotoHtml . '</td>
                <td style="padding-left: 15px;">
                    <table class="data-table" style="margin: 0;">
                        <tr>
                            <td class="label" style="width: 35%;">Nome Completo:</td>
                            <td class="value" style="width: 65%;" colspan="3"><strong>' . htmlspecialchars($dados['nome_arguido']) . '</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Alcunha:</td>
                            <td class="value">' . (!empty($dados['alcunha']) ? htmlspecialchars($dados['alcunha']) : 'N/A') . '</td>
                            <td class="label">N.º BI:</td>
                            <td class="value">' . htmlspecialchars($dados['bi'] ?? '---') . '</td>
                        </tr>
                        <tr>
                            <td class="label">Data de Nasc.:</td>
                            <td class="value">' . $dataNascFormatada . '</td>
                            <td class="label">Idade / Estatuto:</td>
                            <td class="value">' . (!empty($dados['idade']) ? htmlspecialchars($dados['idade']) : '---') . '</td>
                        </tr>
                        <tr>
                            <td class="label">Nacionalidade:</td>
                            <td class="value">' . htmlspecialchars($dados['nacionalidade'] ?? 'Angolana') . '</td>
                            <td class="label">Naturalidade:</td>
                            <td class="value">' . htmlspecialchars($dados['naturalidade'] ?? '---') . '</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="section-title">Outros Dados Biográficos & Contactos</div>
        <table class="data-table">
            <tr>
                <td class="label">Estado Civil:</td>
                <td class="value">' . htmlspecialchars($dados['estado_civil'] ?? '---') . '</td>
                <td class="label">Profissão:</td>
                <td class="value">' . htmlspecialchars($dados['profissao'] ?? 'Não especificada') . '</td>
            </tr>
            <tr>
                <td class="label">Filiação:</td>
                <td class="value" colspan="3">' . htmlspecialchars($dados['filiacao'] ?? '---') . '</td>
            </tr>
            <tr>
                <td class="label">Residência / Morada:</td>
                <td class="value" colspan="3">' . htmlspecialchars($dados['morada'] ?? '---') . '</td>
            </tr>
        </table>

        <div class="section-title">Situação Processual e Medida Coercitiva</div>
        <table class="data-table">
            <tr>
                <td class="label">Tipo de Crime:</td>
                <td class="value" colspan="3">' . htmlspecialchars($dados['tipo_crime'] ?? '---') . '</td>
            </tr>
            <tr>
                <td class="label">Medida Coercitiva:</td>
                <td class="value">' . htmlspecialchars($dados['tipo_medida'] ?? 'Prisão Preventiva') . '</td>
                <td class="label">Prazo de Custódia:</td>
                <td class="value">' . $prazoCustodia . '</td>
            </tr>
        </table>

        <div class="section-title">Bens Apreendidos em Custódia</div>';

    if (!empty($bens)) {
        $html .= '<ul class="bens-list">';
        foreach ($bens as $b) {
            $descricaoBem = !empty($b['descricao']) ? htmlspecialchars($b['descricao']) : 'Item sem descrição detalhada';
            $html .= '<li><strong>' . intval($b['quantidade']) . 'x</strong> ' . $descricaoBem . ' (Categoria: ' . ucfirst($b['categoria']) . ')</li>';
        }
        $html .= '</ul>';
    } else {
        $html .= '<p style="padding-left: 6px; color: #666; font-style: italic;">Nenhum bem registado em custódia.</p>';
    }

    $html .= '
        <div class="section-title" style="margin-top: 20px;">Responsável pelo Processo</div>
        <table class="data-table">
            <tr>
                <td class="label">Técnico Atribuído:</td>
                <td class="value">' . htmlspecialchars($dados['tecnico_nome']) . '</td>
                <td class="label">Data de Registo:</td>
                <td class="value">' . date('d/m/Y H:i', strtotime($dados['criado_em'])) . '</td>
            </tr>
        </table>

        <div class="footer">
            Documento gerado eletronicamente pelo Kidi • Software
        </div>

    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // 6. Enviar o PDF como anexo para ser capturado via fetch blob no frontend
    $nomeFicheiro = "Ficha_Arguido_" . preg_replace('/[^A-Za-z0-9_-]/', '_', $dados['num_processo']) . ".pdf";
    $dompdf->stream($nomeFicheiro, ["Attachment" => true]);

} catch (Throwable $e) {
    // Retorna mensagem limpa caso ocorra erro
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo "Erro ao gerar PDF: " . $e->getMessage();
}