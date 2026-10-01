<?php
require_once '../../api/conexao.php';
require_once '../../api/session.php';
require_once '../../dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Função para conversão segura de imagem para Base64
function imageToBase64($path)
{
    if (!file_exists($path))
        return '';
    $type = mime_content_type($path);
    $data = file_get_contents($path);
    return 'data:' . $type . ';base64,' . base64_encode($data);
}

// Carrega a logo antes de iniciar o buffer
$logoBase64 = imageToBase64(__DIR__ . '/logo.webp');

// Capturar parâmetros enviados pelo formulário (via GET)
$status = trim($_GET['status'] ?? 'todos');
$inicio = !empty($_GET['inicio']) ? $_GET['inicio'] . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
$fim    = !empty($_GET['fim']) ? $_GET['fim'] . ' 23:59:59' : date('Y-m-d') . ' 23:59:59';

// Data formatada para exibição no topo do mapa
$dataFormatada = date('d/m/Y', strtotime($_GET['inicio'] ?? date('Y-m-d')));
list($dia, $mes, $ano) = explode('/', $dataFormatada);

/**
 * Consulta SQL SEM BLOQUEIOS:
 * Traz todos os arguidos no período de datas, fazendo apenas o filtro opcional 
 * pelo estado do processo (Em Andamento ou Finalizado).
 * As colunas de ouvidos/não ouvidos são preenchidas de forma dinâmica via LEFT JOIN.
 */
$sql = "SELECT a.*, p.num_processo, p.estado_processo, 
               CASE WHEN ai.id IS NOT NULL THEN 'X' ELSE '' END AS ouvido_sim,
               CASE WHEN ai.id IS NULL THEN 'X' ELSE '' END AS ouvido_nao
        FROM arguidos a
        LEFT JOIN processos p ON a.processo_id = p.id
        LEFT JOIN auto_interrogatorios ai ON ai.arguido_id = a.id AND LOWER(ai.estado_auto) = 'finalizado'
        WHERE a.criado_em BETWEEN ? AND ?";

$params = [$inicio, $fim];
$types  = "ss";

// Adiciona o filtro de status do processo de forma flexível apenas se não for "todos"
if ($status !== 'todos' && $status !== '') {
    $sql .= " AND (p.estado_processo = ? OR LOWER(p.estado_processo) = LOWER(?))";
    $params[] = $status;
    $params[] = $status;
    $types   .= "ss";
}

$sql .= " ORDER BY a.criado_em ASC";

// Execução segura da query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro na preparação da query: " . $conn->error);
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$detidos = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Dividir em blocos de 15 por página
$paginas = array_chunk($detidos, 15);
$totalDetidos = count($detidos);

ob_start();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 9pt;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
        }

        .header-mapa {
            text-align: center;
            margin-bottom: 10px;
        }

        .header-mapa img {
            width: 55px;
            height: auto;
            margin-bottom: 2px;
        }

        .header-mapa h2 {
            font-size: 11pt;
            margin: 2px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-mapa h3 {
            font-size: 10pt;
            margin: 2px 0;
            font-weight: bold;
        }

        .info-topo {
            width: 100%;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 10pt;
        }

        .info-topo .esquadra {
            float: left;
        }

        .info-topo .data-mapa {
            float: right;
        }

        table.tabela-celas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table.tabela-celas th,
        table.tabela-celas td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
        }

        table.tabela-celas th {
            font-size: 7.5pt;
            background-color: #f2f2f2;
            text-transform: uppercase;
        }

        table.tabela-celas td {
            font-size: 8.5pt;
            height: 18px;
        }

        .col-nome {
            text-align: left !important;
            padding-left: 5px !important;
        }

        .assinaturas {
            width: 100%;
            margin-top: 20px;
        }

        .assinaturas table {
            width: 100%;
            border: none;
        }

        .assinaturas td {
            border: none;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }

        .linha-assinatura {
            display: inline-block;
            width: 250px;
            border-top: 1px solid #000;
            margin-top: 35px;
        }
    </style>
</head>

<body>

    <?php 
    if (empty($paginas)): 
    ?>
        <div class="header-mapa">
            <?php if (!empty($logoBase64)): ?>
                <img src="<?= $logoBase64 ?>" alt="Logo">
            <?php endif; ?>
            <h2>REPÚBLICA DE ANGOLA</h2>
            <h2>PROCURADORIA GERAL DA REPÚBLICA</h2>
            <h3>PGR SIC LUANDA SUL</h3>
            <h3>MAPA DE CONTROLO DE PROCESSOS E CELAS</h3>
        </div>

        <div class="info-topo">
            <div class="esquadra">TOTAL DE REGISTOS: <strong>0</strong></div>
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
                <tr>
                    <td colspan="12" style="text-align: center; padding: 20px; font-weight: bold;">Nenhum registo encontrado para os filtros selecionados.</td>
                </tr>
            </tbody>
        </table>
    <?php 
    else:
        $linhaCount = 1;
        foreach ($paginas as $index => $listaDetidos):
    ?>
        <div class="<?= ($index < count($paginas) - 1) ? 'page-break' : '' ?>">

            <div class="header-mapa">
                <?php if (!empty($logoBase64)): ?>
                    <img src="<?= $logoBase64 ?>" alt="Logo">
                <?php endif; ?>
                <h2>REPÚBLICA DE ANGOLA</h2>
                <h2>PROCURADORIA GERAL DA REPÚBLICA</h2>
                <h3>PGR SIC LUANDA SUL</h3>
                <h3>MAPA DE CONTROLO DE PROCESSOS E CELAS</h3>
            </div>

            <div class="info-topo">
                <div class="esquadra">TOTAL DE REGISTOS: <strong><?= $totalDetidos ?></strong></div>
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
                    <?php foreach ($listaDetidos as $row):
                        $classificacaoIdade = '-';
                        if (!empty($row['data_nascimento']) && $row['data_nascimento'] != '0000-00-00') {
                            $nasc = new DateTime($row['data_nascimento']);
                            $hoje = new DateTime();
                            $idadeAnos = $hoje->diff($nasc)->y;

                            if ($idadeAnos < 18) {
                                $classificacaoIdade = 'Menor';
                            } else {
                                $classificacaoIdade = 'Adulto';
                            }
                        }

                        $sexoExibir = !empty($row['sexo']) ? $row['sexo'] : 'N/D';
                    ?>
                        <tr>
                            <td><?= $linhaCount++ ?></td>
                            <td class="col-nome"><?= htmlspecialchars($row['nome']) ?></td>
                            <td><?= htmlspecialchars($row['tipo_crime'] ?? '-') ?></td>
                            <td><?= $classificacaoIdade ?></td>
                            <td><?= htmlspecialchars($sexoExibir) ?></td>
                            <td><?= htmlspecialchars($row['idade'] ?? '-') ?></td>
                            <td>Luanda Sul</td>
                            <td><?= !empty($row['data_inicio']) ? date('d/m/Y', strtotime($row['data_inicio'])) : '-' ?></td>
                            <td>Esquadra Local</td>
                            <td><?= !empty($row['criado_em']) ? date('d/m/Y', strtotime($row['criado_em'])) : '-' ?></td>
                            <td><?= $row['ouvido_sim'] ?></td>
                            <td><?= $row['ouvido_nao'] ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php for ($i = count($listaDetidos); $i < 15; $i++): ?>
                        <tr>
                            <td><?= $linhaCount++ ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="assinaturas">
                <table>
                    <tr>
                        <td>O PROCURADOR<div class="linha-assinatura"></div>
                        </td>
                        <td>O TÉCNICO<div class="linha-assinatura"></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    <?php 
        endforeach; 
    endif; 
    ?>

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
$dompdf->stream('Mapa_Geral_Processos.pdf', ["Attachment" => true]);