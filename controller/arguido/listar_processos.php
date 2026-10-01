<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    require_once '../../api/conexao.php';

    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception("Erro de conexão com a base de dados.");
    }

    // Verificar se o utilizador está autenticado e obter o seu perfil e ID
    $perfilId = isset($_SESSION['user_perfil']) ? intval($_SESSION['user_perfil']) : 0;
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

    // Validação de sessão permitindo o perfil master (99) ou ID -999
    if ($userId === 0 || ($perfilId === 0 && $userId !== -999)) {
        throw new Exception("Sessão inválida ou expirada.");
    }

    // Parâmetros recebidos via GET
    $filtro = $_GET['filtro'] ?? 'todos'; // 'todos', 'finalizados', 'pendentes'
    $busca = trim($_GET['busca'] ?? '');
    $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $limite = 8; // Número de cards por página
    $offset = ($pagina - 1) * $limite;

    // Regra de Visibilidade por Perfil:
    // Administrador (1) e Técnico (3) só veem os seus processos. Gerente (2) vê tudo.
    $condicaoVisibilidade = "";
    $paramVisibilidade = [];
    $typeVisibilidade = "";

    if ($perfilId === 1 || $perfilId === 3) {
        $condicaoVisibilidade = "p.tecnico_responsavel_id = ?";
        $paramVisibilidade[] = $userId;
        $typeVisibilidade .= "i";
    }

    // --- CONTADORES ---
    $whereContadores = ["p.ativo = 1"];
    $paramsContadores = [];
    $typesContadores = "";

    if (!empty($condicaoVisibilidade)) {
        $whereContadores[] = $condicaoVisibilidade;
        $paramsContadores[] = $userId;
        $typesContadores .= "i";
    }
    $strWhereContadores = "WHERE " . implode(" AND ", $whereContadores);

    // Contadores baseados estritamente em processos que possuem arguidos registados (INNER JOIN implícito)
    $sqlContadores = "SELECT 
        (SELECT COUNT(DISTINCT p.id) FROM processos p INNER JOIN arguidos ar ON ar.processo_id = p.id $strWhereContadores) as total,
        (SELECT COUNT(DISTINCT ar.processo_id) FROM arguidos ar JOIN processos p ON ar.processo_id = p.id $strWhereContadores) as finalizados,
        (SELECT COUNT(DISTINCT p.id) FROM processos p INNER JOIN arguidos ar ON ar.processo_id = p.id $strWhereContadores AND p.id NOT IN (SELECT COALESCE(processo_id, 0) FROM auto_interrogatorios)) as pendentes";

    if (!empty($paramsContadores)) {
        $stmtCont = $conn->prepare($sqlContadores);
        $allParams = array_merge($paramsContadores, $paramsContadores, $paramsContadores);
        $allTypes = str_repeat($typeVisibilidade, 3);
        $stmtCont->bind_param($allTypes, ...$allParams);
        $stmtCont->execute();
        $contadores = $stmtCont->get_result()->fetch_assoc() ?? ['total' => 0, 'finalizados' => 0, 'pendentes' => 0];
        $stmtCont->close();
    } else {
        $resContadores = $conn->query($sqlContadores);
        $contadores = $resContadores->fetch_assoc() ?? ['total' => 0, 'finalizados' => 0, 'pendentes' => 0];
    }

    // --- LISTAGEM E FILTROS ---
    $whereClauses = ["p.ativo = 1"];
    $params = [];
    $types = "";

    // Aplicar restrição de perfil na listagem
    if (!empty($condicaoVisibilidade)) {
        $whereClauses[] = $condicaoVisibilidade;
        $params[] = $userId;
        $types .= "i";
    }

    if ($busca !== '') {
        $whereClauses[] = "(p.num_processo LIKE ? OR p.nome_arguido LIKE ? OR u.nome LIKE ? OR ar.tipo_crime LIKE ?)";
        $termoLike = "%" . $busca . "%";
        $params[] = $termoLike;
        $params[] = $termoLike;
        $params[] = $termoLike;
        $params[] = $termoLike;
        $types .= "ssss";
    }

    if ($filtro === 'finalizados') {
        $whereClauses[] = "EXISTS (SELECT 1 FROM auto_interrogatorios ai_f WHERE ai_f.processo_id = p.id AND ai_f.estado_auto = 'finalizado')";
    } elseif ($filtro === 'pendentes') {
        $whereClauses[] = "NOT EXISTS (SELECT 1 FROM auto_interrogatorios ai_p WHERE ai_p.processo_id = p.id AND ai_p.estado_auto = 'finalizado')";
    }

    $sqlWhere = "WHERE " . implode(" AND ", $whereClauses);

    // Contar total filtrado (Utilizando INNER JOIN arguidos para garantir apenas processos com arguidos)
    $sqlCount = "SELECT COUNT(DISTINCT p.id) as total_filtrado 
                 FROM processos p 
                 INNER JOIN arguidos ar ON ar.processo_id = p.id
                 JOIN utilizadores u ON p.tecnico_responsavel_id = u.id 
                 LEFT JOIN auto_interrogatorios ai ON ai.processo_id = p.id
                 $sqlWhere";

    $stmtCount = $conn->prepare($sqlCount);
    if (!empty($params)) {
        $stmtCount->bind_param($types, ...$params);
    }
    $stmtCount->execute();
    $totalFiltrado = $stmtCount->get_result()->fetch_assoc()['total_filtrado'] ?? 0;
    $totalPaginas = max(1, ceil($totalFiltrado / $limite));
    $stmtCount->close();

    // --- BUSCAR DADOS PAGINADOS (Adicionados ar.profissao e ar.idade) ---
    $sqlDados = "SELECT p.id, p.num_processo, p.nome_arguido, p.criado_em, u.nome AS tecnico_nome,
                 ar.id as arguido_id, ar.alcunha, ar.bi, ar.data_nascimento, ar.nacionalidade, 
                 ar.naturalidade, ar.estado_civil, ar.profissao, ar.idade, ar.filiacao, ar.morada, ar.contacto,
                 ar.tipo_crime, ar.tipo_medida, ar.prazo_quantidade, ar.prazo_unidade, 
                 ar.biometria_face, p.estado_processo,
                 (SELECT estado_auto FROM auto_interrogatorios WHERE processo_id = p.id LIMIT 1) as estado_auto
                 FROM processos p
                 INNER JOIN arguidos ar ON ar.processo_id = p.id
                 JOIN utilizadores u ON p.tecnico_responsavel_id = u.id
                 LEFT JOIN auto_interrogatorios ai ON ai.processo_id = p.id
                 $sqlWhere
                 GROUP BY p.id
                 ORDER BY p.id DESC
                 LIMIT ? OFFSET ?";

    $stmtDados = $conn->prepare($sqlDados);

    $paramsFinal = $params;
    $paramsFinal[] = $limite;
    $paramsFinal[] = $offset;
    $typesFinal = $types . "ii";

    $stmtDados->bind_param($typesFinal, ...$paramsFinal);
    $stmtDados->execute();
    $resultDados = $stmtDados->get_result();

    $processos = [];

    while ($row = $resultDados->fetch_assoc()) {
        $isFinalizado = ($row['estado_auto'] === 'finalizado');
        $arguidoId = $row['arguido_id'];

        $bensTexto = 'Nenhum bem registado.';
        if ($arguidoId) {
            $stmtBens = $conn->prepare("SELECT descricao, categoria FROM bens WHERE arguido_id = ?");
            $stmtBens->bind_param("i", $arguidoId);
            $stmtBens->execute();
            $resBens = $stmtBens->get_result();
            $listaBens = [];
            while ($b = $resBens->fetch_assoc()) {
                $listaBens[] = "1x " . $b['descricao'] . " (" . ucfirst($b['categoria']) . ")";
            }
            if (!empty($listaBens)) {
                $bensTexto = implode(', ', $listaBens);
            }
            $stmtBens->close();
        }

        $prazoCustodia = '48 Horas';
        if (!empty($row['prazo_quantidade']) && !empty($row['prazo_unidade'])) {
            $prazoCustodia = $row['prazo_quantidade'] . ' ' . ucfirst($row['prazo_unidade']);
        }

        $idadeStr = '';
        if (!empty($row['data_nascimento'])) {
            $nasc = new DateTime($row['data_nascimento']);
            $hoje = new DateTime('today');
            $idadeStr = ' (' . $nasc->diff($hoje)->y . ' anos)';
        }

        $processos[] = [
            'id' => $row['id'],
            'num_processo' => $row['num_processo'],
            'arguido' => $row['nome_arguido'] ?? 'Por Definir',
            'alcunha' => $row['alcunha'] ?? '',
            'bi' => $row['bi'] ?? '',
            'data_nasc' => !empty($row['data_nascimento']) ? date('d/m/Y', strtotime($row['data_nascimento'])) : '---',
            'nacionalidade' => $row['nacionalidade'] ?? 'Angolana',
            'naturalidade' => $row['naturalidade'] ?? '',
            'estado_civil' => $row['estado_civil'] ?? '',
            'profissao' => $row['profissao'] ?? 'Não Especificada', // 🔥 Atualizado
            'idade' => $row['idade'] ?? '---',                     // 🔥 Atualizado
            'num_filhos' => 0,
            'filiacao' => $row['filiacao'] ?? '',
            'residencia' => $row['morada'] ?? '',
            'tipo_crime' => $row['tipo_crime'] ?? 'Instrução de Processo / Aguarda Registo',
            'prazo_custodia' => $prazoCustodia . (!empty($row['tipo_medida']) ? ' (' . $row['tipo_medida'] . ')' : ''),
            'bens_apreendidos' => $bensTexto,
            'foto' => $row['biometria_face'] ?? '',
            'status_processual' => $row['tipo_medida'] ?? 'Prisão Preventiva',
            'data' => date('d/m/Y H:i', strtotime($row['criado_em'] ?? 'now')),
            'tecnico' => $row['tecnico_nome'],
            'status' => $isFinalizado ? 'finalizado' : 'pendente',
            'status_label' => $isFinalizado ? 'Finalizado' : 'Aguarda Interrogatório',
        ];
    }
    $stmtDados->close();
    $conn->close();

    echo json_encode([
        'success' => true,
        'contadores' => [
            'total' => intval($contadores['total']),
            'finalizados' => intval($contadores['finalizados']),
            'pendentes' => intval($contadores['pendentes'])
        ],
        'dados' => $processos,
        'paginacao' => [
            'pagina_atual' => $pagina,
            'total_paginas' => $totalPaginas,
            'total_registos' => $totalFiltrado
        ]
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'mensagem' => $e->getMessage()
    ]);
}