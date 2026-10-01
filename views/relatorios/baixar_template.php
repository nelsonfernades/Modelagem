<?php
require_once '../../dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Função para conversão segura de imagem para Base64
function imageToBase64($path)
{
    if (!file_exists($path)) return '';
    $type = mime_content_type($path);
    $data = file_get_contents($path);
    return 'data:' . $type . ';base64,' . base64_encode($data);
}

// Carrega a logo institucional
$logoBase64 = imageToBase64('logo.webp');

// Identifica o tipo de template solicitado via GET (padrão: celas)
$tipo = $_GET['tipo'] ?? 'celas';

$dia = date('d');
$mes = date('m');
$ano = date('Y');

ob_start();

if ($tipo === 'interrogatorio'):
    // ==========================================
    // TEMPLATE: AUTO DE INTERROGATÓRIO VAZIO
    // ==========================================
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Minuta - Auto de Interrogatório de Arguido</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 13pt; color: #000; line-height: 1.8; margin: 0; padding: 0; }
        .header-instituicao { text-align: center; margin-bottom: 25px; }
        .header-instituicao img { width: 75px; height: auto; margin-bottom: 5px; }
        .header-instituicao h2 { font-size: 12pt; margin: 2px 0; font-weight: bold; text-transform: uppercase; }
        .header-instituicao h3 { font-size: 11pt; margin: 2px 0; font-weight: normal; }
        .titulo-documento { text-align: center; font-size: 14pt; font-weight: bold; margin: 25px 0; text-transform: uppercase; }
        .corpo-texto { text-align: justify; text-justify: inter-word; margin-bottom: 15px; }
        .espaco-linha { border-bottom: 1px dotted #000; display: inline-block; min-width: 150px; height: 16px; }
        .transcricao-vazia { margin-top: 15px; border: 1px dotted #000; min-height: 250px; padding: 10px; }
        .assinaturas { width: 100%; margin-top: 40px; }
        .assinaturas table { width: 100%; border: none; }
        .assinaturas td { border: none; text-align: center; font-weight: bold; font-size: 11pt; }
        .linha-assinatura { display: inline-block; width: 250px; border-top: 1px solid #000; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header-instituicao">
        <?php if (!empty($logoBase64)): ?><img src="<?= $logoBase64 ?>" alt="República de Angola"><?php endif; ?>
        <h2>REPÚBLICA DE ANGOLA</h2>
        <h2>PROCURADORIA GERAL DA REPÚBLICA</h2>
        <h3>Gabinete do Procurador Junto da DNIC</h3>
        <h3>Província de Luanda</h3>
    </div>

    <div class="titulo-documento">Auto de Interrogatório de Arguido (Minuta em Branco)</div>

    <div class="corpo-texto">
        Aos <span class="espaco-linha" style="width: 40px; text-align: center;"></span> dias do mês de <span class="espaco-linha" style="width: 80px; text-align: center;"></span> de 20<span class="espaco-linha" style="width: 40px; text-align: center;"></span>, nesta Cidade de <span class="espaco-linha" style="width: 180px;"></span>, no Gabinete do digno Magistrado do Ministério Público, onde se achava o Exmo. Senhor <span class="espaco-linha" style="width: 250px;"></span> e o defensor <span class="espaco-linha" style="width: 200px;"></span> e comigo <span class="espaco-linha" style="width: 200px;"></span>, aqui foi presente o arguido, <span class="espaco-linha" style="width: 280px;"></span>, que depois de advertido de que a falta de resposta às perguntas sobre sua identidade e antecedentes criminais o fará incorrer na pena de desobediência e a sua falsidade na pena de falsas declarações, disse chamar-se <span class="espaco-linha" style="width: 280px;"></span>, no estado de <span class="espaco-linha" style="width: 120px;"></span>, de profissão <span class="espaco-linha" style="width: 150px;"></span>, de <span class="espaco-linha" style="width: 40px; text-align: center;"></span> anos de idade, nascido em <span class="espaco-linha" style="width: 90px; text-align: center;"></span>, natural de <span class="espaco-linha" style="width: 150px;"></span>, filho de <span class="espaco-linha" style="width: 220px;"></span> e de <span class="espaco-linha" style="width: 220px;"></span>, e residente <span class="espaco-linha" style="width: 280px;"></span>, titular do B.I. n.º <span class="espaco-linha" style="width: 150px;"></span>, passado pelo Arquivo de Identificação de <span class="espaco-linha" style="width: 120px;"></span> aos <span class="espaco-linha" style="width: 90px; text-align: center;"></span>. Perguntado se já esteve alguma vez preso, quando e porquê, se foi ou não condenado, quando e porquê, interrogado seguidamente sobre os factos que lhe são imputados, que acabam de ser expostos depois de esclarecido de que não é obrigado a responder às perguntas que lhe vão ser feitas sobre esses factos ou sobre as declarações que cerca deles prestar, respondeu:<span class="espaco-linha" style="width: 5822px; text-align: center;"></span>
    </div>
</body>
</html>
<?php
    $html = ob_get_clean();
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('Minuta_Auto_Interrogatorio_Vazio.pdf', ["Attachment" => true]);
    exit;

else:
    // ==========================================
    // TEMPLATE: MAPA DE CELAS VAZIO (PADRÃO)
    // ==========================================
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Minuta - Mapa de Controlo de Celas</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 9pt; color: #000; margin: 0; padding: 0; }
        .header-mapa { text-align: center; margin-bottom: 10px; }
        .header-mapa img { width: 55px; height: auto; margin-bottom: 2px; }
        .header-mapa h2 { font-size: 11pt; margin: 2px 0; font-weight: bold; text-transform: uppercase; }
        .header-mapa h3 { font-size: 10pt; margin: 2px 0; font-weight: bold; }
        .info-topo { width: 100%; margin-bottom: 8px; font-weight: bold; font-size: 10pt; }
        .info-topo .esquadra { float: left; }
        .info-topo .data-mapa { float: right; }
        table.tabela-celas { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.tabela-celas th, table.tabela-celas td { border: 1px solid #000; padding: 4px 2px; text-align: center; }
        table.tabela-celas th { font-size: 7.5pt; background-color: #f2f2f2; text-transform: uppercase; }
        table.tabela-celas td { font-size: 8.5pt; height: 18px; }
        .assinaturas { width: 100%; margin-top: 20px; }
        .assinaturas table { width: 100%; border: none; }
        .assinaturas td { border: none; text-align: center; font-weight: bold; font-size: 10pt; }
        .linha-assinatura { display: inline-block; width: 250px; border-top: 1px solid #000; margin-top: 35px; }
    </style>
</head>
<body>
    <div class="header-mapa">
        <?php if (!empty($logoBase64)): ?><img src="<?= $logoBase64 ?>" alt="Logo"><?php endif; ?>
        <h2>REPÚBLICA DE ANGOLA</h2>
        <h2>PROCURADORIA GERAL DA REPÚBLICA</h2>
        <h3>PGR SIC LUANDA SUL</h3>
        <h3>MAPA DE CONTROLO DE PROCESSOS E CELAS (MINUTA EM BRANCO)</h3>
    </div>

    <div class="info-topo">
        <div class="esquadra">TOTAL DE REGISTOS: <strong>______</strong></div>
        <div class="data-mapa">DATA: <u> <?= $dia ?> </u> / <u> <?= $mes ?> </u> / <u> <?= $ano ?> </u></div>
        <div style="clear: both;"></div>
    </div>

    <table class="tabela-celas">
        <thead>
            <tr>
                <th style="width: 28px;">Nº</th>
                <th>NOME</th>
                <th style="width: 100px;">CRIME</th>
                <th style="width: 45px;">MENORES</th>
                <th style="width: 40px;">SEXO</th>
                <th style="width: 38px;">IDADE</th>
                <th style="width: 105px;">REGEDORIA / B.HORIZONTE / L.SUL / COMANDO</th>
                <th style="width: 70px;">DATA DA DETENÇÃO</th>
                <th style="width: 80px;">ESQUADRA LOCAL</th>
                <th style="width: 70px;">DATA DE ENTRADA</th>
                <th style="width: 45px;">OUVIDOS</th>
                <th style="width: 45px;">N OUVIDOS</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= 15; $i++): ?>
                <tr>
                    <td><?= $i ?></td>
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div class="assinaturas">
        <table>
            <tr>
                <td>O PROCURADOR<div class="linha-assinatura"></div></td>
                <td>O TÉCNICO<div class="linha-assinatura"></div></td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php
    $html = ob_get_clean();
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('Minuta_Mapa_Celas_Vazio.pdf', ["Attachment" => true]);
    exit;
endif;
?>