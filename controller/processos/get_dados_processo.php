<?php
// api/get_dados_processo.php
session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    require_once '../../api/conexao.php';

    // 1. Calcular o próximo número de processo automático
    $sqlUltimo = "SELECT num_processo FROM processos ORDER BY id DESC LIMIT 1";
    $resUltimo = $conn->query($sqlUltimo);
    
    $proximoNumero = "PROC-001"; // Valor padrão caso não exista nenhum processo

    if ($resUltimo && $resUltimo->num_rows > 0) {
        $row = $resUltimo->fetch_assoc();
        $ultimoProcesso = $row['num_processo']; // Ex: PROC-005
        
        // Extrai os números do formato (ex: pega no '005')
        if (preg_match('/(\d+)$/', $ultimoProcesso, $matches)) {
            $numeroAtual = intval($matches[1]);
            $proximoNumero = 'PROC-' . str_pad($numeroAtual + 1, 3, '0', STR_PAD_LEFT);
        }
    }

    // 2. Buscar todos os técnicos ativos cadastrados
    $sqlTecnicos = "SELECT id, nome, nip FROM utilizadores WHERE estado = 'ativo' ORDER BY nome ASC";
    $resTecnicos = $conn->query($sqlTecnicos);
    
    $tecnicos = [];
    if ($resTecnicos) {
        while ($tec = $resTecnicos->fetch_assoc()) {
            $tecnicos[] = $tec;
        }
    }

    echo json_encode([
        'success' => true,
        'proximo_processo' => $proximoNumero,
        'tecnicos' => $tecnicos
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar dados: ' . $e->getMessage()
    ]);
}