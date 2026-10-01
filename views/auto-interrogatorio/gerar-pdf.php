<?php
require_once '../../api/conexao.php';
require_once '../../api/session.php';

require_once '../../dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
$processo_id = isset($_GET['processo_id']) ? filter_var($_GET['processo_id'], FILTER_VALIDATE_INT) : null;

if (!$id && !$processo_id) {
    die('Identificador do auto ou processo não fornecido.');
}

// Buscar dados do auto na base de dados
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM auto_interrogatorios WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
} else {
    $stmt = $conn->prepare("SELECT * FROM auto_interrogatorios WHERE processo_id = ? LIMIT 1");
    $stmt->bind_param("i", $processo_id);
}

$stmt->execute();
$auto = $stmt->get_result()->fetch_assoc();

if (!$auto) {
    die('Auto de interrogatório não encontrado.');
}

// Função para conversão segura de imagem para Base64
function imageToBase64($path)
{
    if (!file_exists($path)) return '';
    $type = mime_content_type($path);
    $data = file_get_contents($path);
    return 'data:' . $type . ';base64,' . base64_encode($data);
}

// Converte a logo usando o caminho absoluto da pasta atual (__DIR__)
// Ajusta 'logo.webp' para o nome exato do teu ficheiro de imagem se necessário
$logoBase64 = imageToBase64('logo.webp');

// Formatação de datas
$dataDiligencia = !empty($auto['data_diligencia']) ? new DateTime($auto['data_diligencia']) : new DateTime();
$dia = $dataDiligencia->format('d');
$mes = $dataDiligencia->format('m');
$ano = $dataDiligencia->format('Y');

$dataNasc = !empty($auto['data_nascimento']) ? date('d/m/Y', strtotime($auto['data_nascimento'])) : '________________';
$biData = !empty($auto['bi_data_emissao']) ? date('d/m/Y', strtotime($auto['bi_data_emissao'])) : '________';

// Iniciar o buffer de saída para capturar o HTML estruturado
ob_start();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Auto de Interrogatório de Arguido - Processo Nº <?= htmlspecialchars($auto['numero_processo']) ?></title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13pt;
            color: #000;
            line-height: 1.8;
            margin: 0;
            padding: 0;
        }
        .header-instituicao {
            text-align: center;
            margin-bottom: 25px;
        }
        .header-instituicao img {
            width: 75px;
            height: auto;
            margin-bottom: 5px;
        }
        .header-instituicao h2 {
            font-size: 12pt;
            margin: 2px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-instituicao h3 {
            font-size: 11pt;
            margin: 2px 0;
            font-weight: normal;
        }
        .titulo-documento {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 25px 0;
        }
        .corpo-texto {
            text-align: justify;
            text-justify: inter-word;
            margin-bottom: 15px;
        }
        .campo-preenchido {
            font-weight: bold;
            border-bottom: 1px dotted #000;
            padding: 0 4px;
        }
        .transcricao-texto {
            text-align: justify;
            text-justify: inter-word;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header-instituicao">
        <?php if (!empty($logoBase64)): ?>
            <img src="<?= $logoBase64 ?>" alt="República de Angola">
        <?php endif; ?>
        <h2>REPÚBLICA DE ANGOLA</h2>
        <h2>PROCURADORIA GERAL DA REPÚBLICA</h2>
        <h3>Gabinete do Procurador Junto da DNIC</h3>
        <h3>Província de Luanda</h3>
    </div>

    <div class="titulo-documento">
        Auto de Interrogatório de Arguido
    </div>

    <div class="corpo-texto">
        Aos <span class="campo-preenchido"><?= $dia ?></span> dias do mês de <span class="campo-preenchido"><?= $mes ?></span> de <span class="campo-preenchido"><?= $ano ?></span>, nesta Cidade de <span class="campo-preenchido"><?= htmlspecialchars($auto['cidade']) ?></span>, no Gabinete do digno Magistrado do Ministério Público, onde se achava o Exmo. Senhor <span class="campo-preenchido"><?= htmlspecialchars($auto['magistrado_mp'] ?? '________________________________________') ?></span> e o defensor <span class="campo-preenchido"><?= htmlspecialchars($auto['defensor_advogado'] ?? '________________________') ?></span> e comigo <span class="campo-preenchido"><?= htmlspecialchars($auto['oficial_escrivao'] ?? '________________________') ?></span>, aqui foi presente o arguido, <span class="campo-preenchido"><?= htmlspecialchars($auto['nome_completo']) ?></span>, que depois de advertido de que a falta de resposta às perguntas sobre sua identidade e antecedentes criminais o fará incorrer na pena de desobediência e a sua falsidade na pena de falsas declarações, disse chamar-se <span class="campo-preenchido"><?= htmlspecialchars($auto['nome_completo']) ?></span>, no estado de <span class="campo-preenchido"><?= htmlspecialchars($auto['estado_civil'] ?? '____________') ?></span>, de profissão <span class="campo-preenchido"><?= htmlspecialchars($auto['profissao'] ?? '____________________') ?></span>, de <span class="campo-preenchido"><?= htmlspecialchars($auto['idade'] ?? '__') ?></span> anos de idade, nascido em <span class="campo-preenchido"><?= $dataNasc ?></span>, natural de <span class="campo-preenchido"><?= htmlspecialchars($auto['naturalidade'] ?? '____________________') ?></span>, filho de <span class="campo-preenchido"><?= htmlspecialchars($auto['nome_pai'] ?? '________________________________') ?></span> e de <span class="campo-preenchido"><?= htmlspecialchars($auto['nome_mae'] ?? '________________________________') ?></span>, e residente <span class="campo-preenchido"><?= htmlspecialchars($auto['residencia_habitual'] ?? '________________________________________') ?></span>, titular do B.I. n.º <span class="campo-preenchido"><?= htmlspecialchars($auto['bi_numero'] ?? '__________________') ?></span>, passado pelo Arquivo de Identificação de <span class="campo-preenchido"><?= htmlspecialchars($auto['bi_arquivo_emissao'] ?? '____________') ?></span> aos <span class="campo-preenchido"><?= $biData ?></span>. Perguntado se já esteve alguma vez preso, quando e porquê, se foi ou não condenado, quando e porquê, interrogado seguidamente sobre os factos que lhe são imputados, que acabam de ser expostos depois de esclarecido de que não é obrigado a responder às perguntas que lhe vão ser feitas sobre esses factos ou sobre as declarações que cerca deles prestar, respondeu:
    </div>

    <div class="transcricao-texto">
        <span class="campo-preenchido"><?= htmlspecialchars($auto['transcricao_declaracoes'] ?? '________________________________________') ?></span>
    </div>

</body>
</html>
<?php
$html = ob_get_clean();

// Configuração do Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$nomeFicheiro = 'Auto_Interrogatorio_' . preg_replace('/[^a-zA-Z0-9-_]/', '_', $auto['numero_processo']) . '.pdf';

$dompdf->stream($nomeFicheiro, ["Attachment" => true]);
exit;