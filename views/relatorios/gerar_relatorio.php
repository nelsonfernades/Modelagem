<?php
require_once '../../api/conexao.php';
require_once '../../api/session.php';
require_once '../../dompdf/vendor/autoload.php'; // Dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

// Captura de parâmetros enviados pela Central de Emissão
$tipo    = $_GET['tipo'] ?? 'geral_instrucao';
$status  = $_GET['status'] ?? 'todos';
$inicio  = ($_GET['inicio'] ?? date('Y-m-d')) . ' 00:00:00';
$fim     = ($_GET['fim'] ?? date('Y-m-d')) . ' 23:59:59';
$formato = $_GET['formato'] ?? 'pdf';

// Mapeamento seguro dos tipos de relatórios para os respetivos ficheiros
$mapeamento_relatorios = [
    'mapa_celas'          => 'gerar-mapa-celas.php',
    'auto_interrogatorio' => 'gerar-auto-interrogatorio.php'
];

// Verifica se o tipo selecionado existe no mapeamento
if (array_key_exists($tipo, $mapeamento_relatorios)) {
    $ficheiro_alvo = $mapeamento_relatorios[$tipo];
    
    if (file_exists($ficheiro_alvo)) {
        include $ficheiro_alvo;
        exit;
    } else {
        die("Erro crítico: O ficheiro correspondente ao relatório '{$tipo}' ({$ficheiro_alvo}) não foi encontrado no servidor.");
    }
}

// =========================================================================
// FLUXO PADRÃO (Caso não caia em nenhum dos módulos acima, ex: geral_instrucao)
// =========================================================================

$sql = "SELECT p.*, a.nome as nome_arguido, a.bi, a.tipo_crime 
        FROM processos p 
        LEFT JOIN arguidos a ON p.id = a.processo_id
        WHERE p.criado_em BETWEEN ? AND ?";

if ($status !== 'todos') {
    $sql .= " AND p.estado_processo = ?";
}

$stmt = $conn->prepare($sql);
if ($status !== 'todos') {
    $stmt->bind_param("sss", $inicio, $fim, $status);
} else {
    $stmt->bind_param("ss", $inicio, $fim);
}
$stmt->execute();
$dados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Geração de Hash SHA-256 para garantia da cadeia de custódia
$hashCustodia = hash('sha256', serialize($dados) . date('Y-m-d H:i:s'));

// Exportação em Formato Planilha (Excel/CSV)
if ($formato === 'excel') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Relatorio_' . $tipo . '_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Nº Processo', 'Arguido', 'BI', 'Tipo de Crime', 'Data de Registo', 'Hash SHA-256']);
    foreach ($dados as $row) {
        fputcsv($output, [$row['num_processo'], $row['nome_arguido'], $row['bi'], $row['tipo_crime'], $row['criado_em'], $hashCustodia]);
    }
    fclose($output);
    exit;
} 

// Exportação em Formato Documento PDF
$html = "
<!DOCTYPE html>
<html lang='pt'>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2, .header h3 { margin: 2px 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; font-size: 9pt; }
        th { background-color: #f2f2f2; text-transform: uppercase; }
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 7pt; border-top: 1px solid #999; padding-top: 4px; color: #333; }
    </style>
</head>
<body>
    <div class='header'>
        <h2>República de Angola</h2>
        <h3>Procuradoria Geral da República</h3>
        <h3>Relatório: " . ucwords(str_replace('_', ' ', $tipo)) . "</h3>
        <p style='font-size: 9pt; margin-top: 5px;'>Período: <strong>" . date('d/m/Y', strtotime($inicio)) . "</strong> a <strong>" . date('d/m/Y', strtotime($fim)) . "</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nº Processo</th>
                <th>Arguido</th>
                <th>BI</th>
                <th>Tipo de Crime</th>
                <th>Data de Registo</th>
            </tr>
        </thead>
        <tbody>";

if (empty($dados)) {
    $html .= "<tr><td colspan='5' style='text-align: center;'>Nenhum registo encontrado para os filtros selecionados.</td></tr>";
} else {
    foreach ($dados as $row) {
        $html .= "<tr>
            <td>{$row['num_processo']}</td>
            <td>" . ($row['nome_arguido'] ?? '-') . "</td>
            <td>" . ($row['bi'] ?? '-') . "</td>
            <td>" . ($row['tipo_crime'] ?? '-') . "</td>
            <td>{$row['criado_em']}</td>
        </tr>";
    }
}

$html .= "</tbody>
    </table>

    <div class='footer'>
        <strong>Garantia de Cadeia de Custódia - Assinatura Digital e Selo SHA-256:</strong> {$hashCustodia}
    </div>
</body>
</html>";

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("Relatorio_" . $tipo . "_" . date('Y-m-d') . ".pdf", ["Attachment" => true]);
exit;