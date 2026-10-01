<?php
// painel.php ou index principal da aplicação
require_once '../api/conexao.php';
require_once '../api/session.php';
$userLogado = getUtilizadorLogado();

// Gerar iniciais dinâmicas para o avatar (ex: "Prof. Dr. Geraldo" -> "PG")
$partesNome = explode(' ', trim($userLogado['nome']));
$iniciais = count($partesNome) >= 2
    ? mb_strtoupper(mb_substr($partesNome[0], 0, 1) . mb_substr(end($partesNome), 0, 1))
    : mb_strtoupper(mb_substr($userLogado['nome'], 0, 2));

// Obtém os dados do utilizador logado e o seu perfil
$perfilId = (int) $userLogado['perfil_id'];
// Garante o mapeamento do perfil para exibição visual no rodapé do menu
$nomePerfil = 'Utilizador';
$corPerfil = 'bg-slate-500/20 text-slate-400';

if ($perfilId === 1) {
    $nomePerfil = 'Administrador';
    $corPerfil = 'bg-green-500/20 text-green-400';
} elseif ($perfilId === 2) {
    $nomePerfil = 'Gerente';
    $corPerfil = 'bg-orange-500/20 text-orange-400';
} elseif ($perfilId === 3) {
    $nomePerfil = 'Técnico';
    $corPerfil = 'bg-indigo-500/20 text-indigo-400';
}

// Buscar nacionalidades ordenadas alfabeticamente na base de dados
$nacionalidadesList = [];
$sqlNac = "SELECT nome FROM nacionalidades ORDER BY nome ASC";
$resultadoNac = $conn->query($sqlNac);

if ($resultadoNac) {
    while ($row = $resultadoNac->fetch_assoc()) {
        $nacionalidadesList[] = $row['nome'];
    }
}

$profissoesList = [];
$sqlProf = "SELECT nome FROM profissoes ORDER BY nome ASC";
$resultadoProf = $conn->query($sqlProf);

if ($resultadoProf) {
    while ($row = $resultadoProf->fetch_assoc()) {
        $profissoesList[] = $row['nome'];
    }
}


$tiposCrimeList = [];
$sqlCrime = "SELECT nome FROM tipos_crime ORDER BY nome ASC";
$resultadoCrime = $conn->query($sqlCrime);

if ($resultadoCrime) {
    while ($row = $resultadoCrime->fetch_assoc()) {
        $tiposCrimeList[] = $row['nome'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>NextGrade • Gestão de TCC & Monografias</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ======================================================= -->
    <!-- INTEGRAÇÕES VIA CDN (EXTERNO) E DE FORMA LOCAL          -->
    <!-- ======================================================= -->
    <!-- LINK CSS          -->
    <link rel="stylesheet" href="./integration/all.min.css">
    <link rel="stylesheet" href="./integration/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- LINK JS          -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../integration/sweatalert2@11.js"></script>
    <script src="../integration/tailwind.js"></script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e0e7ff;
            border-radius: 10px;
            transition: background 0.3s;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #c7d2fe;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        table,
        tr,
        td {
            overflow: visible !important;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen">
        <!-- MENU LATERAL (SIDEBAR) ADAPTADO PARA TCC -->
        <aside id="sidebar"
            class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-[#0f172a] text-slate-300 transform -translate-x-full md:translate-x-0 sidebar-transition flex flex-col border-r border-white/5">

            <div class="flex items-center justify-between px-8 py-8">
                <div class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-building-shield text-indigo-950 text-sm"></i>
                    </div>
                    Kidi<span class="text-blue-400">Software</span>
                </div>
                <button id="closeSidebar" class="md:hidden text-white/50 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 overflow-y-auto custom-scrollbar">
                <!-- FLUXO DE CADASTRO -->
                <div class="mb-6">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-[2px] text-slate-500 mb-2">Fluxo de Cadastro
                    </p>
                    <div class="space-y-1">
                        <!-- Validação: Administrador, Gerente e Técnicos -->
                        <a href="validacao_reicidencia.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:text-white hover:bg-white/5 transition-all group">
                            <i class="fa-solid fa-user-check w-5 group-hover:text-blue-400 transition-colors"></i>
                            <span class="text-sm font-medium">Validacao</span>
                        </a>
                        <a href="novo_processo.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:text-white hover:bg-white/5 transition-all group">
                            <i class="fa-solid fa-users-rectangle w-5 group-hover:text-blue-400 transition-colors"></i>
                            <span class="text-sm font-medium">Entrada de processos</span>
                        </a>
                        <a href="cadastro_arguido.php"
                            class="relative flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600/10 text-white transition-all">
                            <div class="absolute left-0 w-1 h-6 bg-blue-400 rounded-r-full"></div>
                            <i class="fa-solid fa-users w-5 text-blue-400 transition-colors"></i>
                            <span class="text-sm font-medium text-blue-400">cadastro de Arguidos</span>
                        </a>
                    </div>
                </div>

                <!-- PRINCIPAL -->
                <div class="mb-6">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-[2px] text-slate-500 mb-2">Principal</p>
                    <div class="space-y-1">
                        <!-- Deliberação: Apenas Administrador (Perfil 1) -->
                        <?php if ($perfilId === 1 || $perfilId === 99): ?>
                            <a href="deliberar_processos.php"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:text-white hover:bg-white/5 transition-all group">
                                <i class="fa-solid fa-calendar-days w-5 group-hover:text-blue-400"></i>
                                <span class="text-sm font-medium">Deliberação (Despachos)</span>
                            </a>
                        <?php endif; ?>

                        <!-- Proc. Interrogatório: Administrador, Gerente e Técnicos -->
                        <a href="processos_interrogatorio.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:text-white hover:bg-white/5 transition-all group">
                            <i class="fa-solid fa-file w-5 group-hover:text-blue-400 transition-colors"></i>
                            <span class="text-sm font-medium">Proc. Interrogatório</span>
                        </a>
                    </div>
                </div>

                <!-- SISTEMA -->
                <div class="mb-6">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-[2px] text-slate-500 mb-2">Sistema</p>
                    <div class="space-y-1">
                        <!-- Relatórios Gerenciais: Administrador, Gerente e Técnicos -->
                        <a href="relatorios_gerenciais.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:text-white hover:bg-white/5 transition-all group">
                            <i class="fa-solid fa-chart-simple w-5 group-hover:text-blue-400 transition-colors"></i>
                            <span class="text-sm font-medium">Relatórios Gerenciais</span>
                        </a>

                        <!-- Gestão de técnicos e Configurações: Apenas Gerente (Perfil 2) -->
                        <?php if ($perfilId === 2 || $perfilId === 99): ?>
                            <a href="gestao_tecnicos.php"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:text-white hover:bg-white/5 transition-all group">
                                <i class="fa-solid fa-user-group w-5 group-hover:text-blue-400 transition-colors"></i>
                                <span class="text-sm font-semibold tracking-wide">Gestão de técnicos</span>
                            </a>
                            <a href="configuracoes.php"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:text-white hover:bg-white/5 transition-all group">
                                <i class="fa-solid fa-gear w-5 group-hover:text-blue-400 transition-colors"></i>
                                <span class="text-sm font-medium">Configurações</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>

            <!-- AVATAR INFERIOR COM INICIAIS DINÂMICAS -->
            <div class="p-4 border-t border-white/5 bg-black/20">
                <div class="flex items-center gap-3 px-2 py-2">
                    <div
                        class="w-9 h-9 rounded-full bg-green-500/20 text-green-400 border border-green-500/30 font-bold flex items-center justify-center text-xs shrink-0">
                        <?php echo $iniciais; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-white truncate">
                            <?php echo htmlspecialchars($userLogado['nome']); ?>
                        </p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span
                                class="text-[9px] px-1.5 py-0.2 rounded font-semibold uppercase tracking-wider <?php echo $corPerfil; ?>">
                                <?php echo $nomePerfil; ?>
                            </span>
                            <span class="text-[10px] text-slate-500 truncate">
                                <?php echo htmlspecialchars(!empty($userLogado['nip']) ? $userLogado['nip'] : $userLogado['email']); ?>
                            </span>
                        </div>
                    </div>
                    <button onclick="abrirModalLogout()" class="text-slate-500 hover:text-red-400 transition shrink-0"
                        title="Terminar Sessão">
                        <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                    </button>
                </div>
            </div>
        </aside>

        <!-- CONTEÚDO PRINCIPAL -->
        <div class="flex-1 flex flex-col">
            <!-- TOPO / HEADER -->
            <header class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center relative z-40">
                <div class="flex items-center gap-4">
                    <button id="openSidebar" class="md:hidden text-gray-600 cursor-pointer">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>

                <div class="flex items-center gap-6">
                    <div class="relative">
                        <!-- Botão do Utilizador -->
                        <button id="userBtn" class="flex items-center gap-3 focus:outline-none cursor-pointer group">
                            <div class="text-right hidden sm:block">
                                <p
                                    class="text-sm font-bold text-gray-800 leading-none group-hover:text-indigo-600 transition">
                                    <?= htmlspecialchars($userLogado['nome']) ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">NIP:
                                    <?= htmlspecialchars($userLogado['nip'] ?: 'N/D') ?>
                                </p>
                            </div>
                            <div
                                class="w-10 h-10 bg-indigo-900 text-white flex items-center justify-center rounded-full font-bold text-sm shadow-sm ring-2 ring-indigo-100 transition group-hover:ring-indigo-300">
                                <?= htmlspecialchars($iniciais) ?>
                            </div>
                        </button>

                        <!-- Menu Dropdown Estilo Definições Windows -->
                        <div id="userMenu"
                            class="hidden absolute right-0 mt-3 w-64 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border border-gray-100 dark:border-zinc-800 rounded-2xl shadow-2xl shadow-indigo-950/10 z-50 overflow-hidden animate-in fade-in zoom-in-95 duration-150">

                            <div
                                class="p-4 bg-gray-50/70 dark:bg-zinc-800/50 border-b border-gray-100 dark:border-zinc-800">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 bg-indigo-600 text-white flex items-center justify-center rounded-xl font-bold text-xs shadow-sm">
                                        <?= htmlspecialchars($iniciais) ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-xs font-black text-gray-800 dark:text-zinc-100 truncate">
                                            <?= htmlspecialchars($userLogado['nome']) ?>
                                        </p>
                                        <p class="text-[11px] text-gray-500 dark:text-zinc-400 truncate">
                                            <?= htmlspecialchars($userLogado['email']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-2 space-y-1 text-xs text-gray-700 dark:text-zinc-300 font-medium">
                                <button onclick="abrirPerfilWindows(); fecharMenuUtilizador();"
                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-indigo-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-200 hover:text-indigo-600 transition group cursor-pointer">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs group-hover:scale-110 transition">
                                            <i class="fa-regular fa-user"></i>
                                        </div>
                                        <span>Meu Perfil & Definições</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                                </button>
                            </div>

                            <div class="h-px bg-gray-100 dark:bg-zinc-800 my-1"></div>

                            <div class="p-2">
                                <button onclick="abrirModalLogout(); fecharMenuUtilizador();"
                                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/30 text-red-600 dark:text-red-400 transition cursor-pointer font-semibold">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-red-50 dark:bg-red-950/50 text-red-600 flex items-center justify-center text-[10px]">
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    </div>
                                    <span>Terminar Sessão</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <script>
                async function abrirPerfilWindows() {
                    const isDark = document.documentElement.classList.contains('dark');

                    // Mostra loading enquanto busca os dados do utilizador logado
                    Swal.fire({
                        title: 'A carregar definições...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                        background: isDark ? '#18181b' : '#ffffff',
                        color: isDark ? '#f4f4f5' : '#18181b'
                    });

                    try {
                        const response = await fetch('../api/get_sessao_usuario.php');
                        const res = await response.json();

                        if (!res.success) throw new Error('Não foi possível carregar os dados.');

                        const u = res.utilizador;

                        const htmlConteudoWindows = `
                            <div class="flex flex-col md:flex-row text-left text-xs ${isDark ? 'text-zinc-300' : 'text-gray-700'} min-h-[300px] md:min-h-[380px] -m-4 md:-m-6">
                                <!-- Sidebar Lateral / Abas Mobile -->
                                <div class="w-full md:w-56 bg-gray-50/80 dark:bg-zinc-900/80 p-2 md:p-3 border-b md:border-b-0 md:border-r border-gray-100 dark:border-zinc-800 flex flex-row md:flex-col gap-1 overflow-x-auto shrink-0">
                                    <button type="button" onclick="mudarAbaPerfil('geral')" id="abaGeral" class="flex items-center gap-2 md:gap-3 px-3.5 py-2.5 rounded-xl font-bold bg-indigo-600 text-white transition cursor-pointer shadow-sm whitespace-nowrap">
                                        <i class="fa-solid fa-sliders w-4 text-center"></i> Geral & Conta
                                    </button>
                                    <button type="button" onclick="mudarAbaPerfil('seguranca')" id="abaSeguranca" class="flex items-center gap-2 md:gap-3 px-3.5 py-2.5 rounded-xl font-semibold hover:bg-gray-200/60 dark:hover:bg-zinc-800 text-gray-600 dark:text-zinc-400 transition cursor-pointer whitespace-nowrap">
                                        <i class="fa-solid fa-shield-halved w-4 text-center"></i> Segurança
                                    </button>
                                    <button type="button" onclick="mudarAbaPerfil('notificacoes')" id="abaNotificacoes" class="flex items-center gap-2 md:gap-3 px-3.5 py-2.5 rounded-xl font-semibold hover:bg-gray-200/60 dark:hover:bg-zinc-800 text-gray-600 dark:text-zinc-400 transition cursor-pointer whitespace-nowrap">
                                        <i class="fa-solid fa-bell w-4 text-center"></i> Notificações
                                    </button>
                                </div>

                                <!-- Conteúdo das Abas -->
                                <div class="flex-1 p-4 md:p-6 space-y-4 md:space-y-5 bg-white dark:bg-zinc-900/40 overflow-y-auto">
                                    
                                    <!-- 1. Painel Geral & Conta -->
                                    <form id="painelGeral" onsubmit="salvarPerfilGeral(event)" class="espaco-painel space-y-4">
                                        <div>
                                            <h4 class="text-sm font-black text-gray-900 dark:text-white">Informações da Conta</h4>
                                            <p class="text-[11px] text-gray-500 dark:text-zinc-400">Dados cadastrais registados no sistema.</p>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-[11px] font-bold text-gray-600 dark:text-zinc-400 mb-1">Nome Completo</label>
                                                <input type="text" id="inputPerfilNome" name="nome" value="${escapeHtml(u.nome)}" required 
                                                    class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700 text-gray-800 dark:text-zinc-200 rounded-xl font-semibold text-xs focus:outline-none focus:border-indigo-500 transition">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-bold text-gray-600 dark:text-zinc-400 mb-1">Correio Eletrónico</label>
                                                <input type="email" id="inputPerfilEmail" name="email" value="${escapeHtml(u.email)}" required 
                                                    class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700 text-gray-800 dark:text-zinc-200 rounded-xl font-semibold text-xs focus:outline-none focus:border-indigo-500 transition">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-bold text-gray-600 dark:text-zinc-400 mb-1">NIP de Identificação</label>
                                                <input type="text" value="${escapeHtml(u.nip)}" disabled 
                                                    class="w-full px-3.5 py-2 bg-gray-100 dark:bg-zinc-800/30 border border-gray-200 dark:border-zinc-800 text-gray-400 dark:text-zinc-500 rounded-xl font-semibold text-xs cursor-not-allowed">
                                                <span class="text-[10px] text-gray-400 dark:text-zinc-500 mt-1 block">O NIP não pode ser alterado diretamente.</span>
                                            </div>
                                        </div>

                                        <div class="pt-3 border-t border-gray-100 dark:border-zinc-800 flex justify-end">
                                            <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition cursor-pointer shadow-md shadow-indigo-100 dark:shadow-none flex items-center justify-center gap-2">
                                                <i class="fa-solid fa-floppy-disk"></i> Guardar Alterações
                                            </button>
                                        </div>
                                    </form>

                                    <!-- 2. Painel Segurança -->
                                    <form id="painelSeguranca" onsubmit="salvarSeguranca(event)" class="espaco-painel space-y-4 hidden">
                                        <div>
                                            <h4 class="text-sm font-black text-gray-900 dark:text-white">Segurança</h4>
                                            <p class="text-[11px] text-gray-500 dark:text-zinc-400">Altere a sua palavra-passe de acesso ao sistema.</p>
                                        </div>
                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-[11px] font-bold text-gray-600 dark:text-zinc-400 mb-1">Palavra-passe Atual</label>
                                                <input type="password" id="inputSenhaAtual" name="senha_atual" placeholder="••••••••" required 
                                                    class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700 text-gray-800 dark:text-zinc-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 transition">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-bold text-gray-600 dark:text-zinc-400 mb-1">Nova Palavra-passe</label>
                                                <input type="password" id="inputNovaSenha" name="nova_senha" placeholder="••••••••" required minlength="8" 
                                                    class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700 text-gray-800 dark:text-zinc-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 transition">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-bold text-gray-600 dark:text-zinc-400 mb-1">Confirmar Nova Palavra-passe</label>
                                                <input type="password" id="inputConfirmarSenha" name="confirmar_senha" placeholder="••••••••" required minlength="8" 
                                                    class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700 text-gray-800 dark:text-zinc-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 transition">
                                            </div>
                                        </div>

                                        <div class="pt-3 border-t border-gray-100 dark:border-zinc-800 flex justify-end">
                                            <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition cursor-pointer shadow-md shadow-indigo-100 dark:shadow-none flex items-center justify-center gap-2">
                                                <i class="fa-solid fa-key"></i> Atualizar Palavra-passe
                                            </button>
                                        </div>
                                    </form>

                                    <!-- 3. Painel Notificações -->
                                    <form id="painelNotificacoes" onsubmit="salvarNotificacoes(event)" class="espaco-painel space-y-4 hidden">
                                        <div>
                                            <h4 class="text-sm font-black text-gray-900 dark:text-white">Notificações</h4>
                                            <p class="text-[11px] text-gray-500 dark:text-zinc-400">Gerir preferências de alertas e avisos do sistema.</p>
                                        </div>
                                        <div class="space-y-3">
                                            <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-800/40 rounded-xl border border-gray-100 dark:border-zinc-800 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-800/60 transition">
                                                <span class="font-semibold text-gray-700 dark:text-zinc-300">Notificações por Email</span>
                                                <input type="checkbox" name="notif_email" ${u.notif_email == 1 ? 'checked' : ''} class="w-4 h-4 accent-indigo-600 rounded cursor-pointer">
                                            </label>
                                            <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-800/40 rounded-xl border border-gray-100 dark:border-zinc-800 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-800/60 transition">
                                                <span class="font-semibold text-gray-700 dark:text-zinc-300">Avisos de Novos Processos</span>
                                                <input type="checkbox" name="notif_novos" ${u.notif_novos == 1 ? 'checked' : ''} class="w-4 h-4 accent-indigo-600 rounded cursor-pointer">
                                            </label>
                                            <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-800/40 rounded-xl border border-gray-100 dark:border-zinc-800 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-800/60 transition">
                                                <span class="font-semibold text-gray-700 dark:text-zinc-300">Alertas de Processos Pendentes</span>
                                                <input type="checkbox" name="notif_pendentes" ${u.notif_pendentes == 1 ? 'checked' : ''} class="w-4 h-4 accent-indigo-600 rounded cursor-pointer">
                                            </label>
                                        </div>

                                        <div class="pt-3 border-t border-gray-100 dark:border-zinc-800 flex justify-end">
                                            <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition cursor-pointer shadow-md shadow-indigo-100 dark:shadow-none flex items-center justify-center gap-2">
                                                <i class="fa-solid fa-floppy-disk"></i> Guardar Preferências
                                            </button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        `;

                        Swal.fire({
                            title: '<div class="text-left font-black text-sm md:text-base text-gray-800 dark:text-white border-b border-gray-100 dark:border-zinc-800 pb-3"><i class="fa-solid fa-gear text-indigo-600 mr-2"></i> Definições da Conta</div>',
                            html: htmlConteudoWindows,
                            width: '92%',
                            showConfirmButton: false,
                            showCloseButton: true,
                            background: isDark ? '#18181b' : '#ffffff',
                            color: isDark ? '#f4f4f5' : '#18181b',
                            customClass: {
                                popup: 'rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-2xl max-w-[720px] w-full'
                            }
                        });

                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Não foi possível carregar o perfil.',
                            background: isDark ? '#18181b' : '#ffffff',
                            color: isDark ? '#f4f4f5' : '#18181b'
                        });
                    }
                }

                // --- FUNÇÕES GLOBAIS DE CONTROLO DO MENU E PERFIL ---

                function fecharMenuUtilizador() {
                    const userMenu = document.getElementById('userMenu');
                    if (userMenu) {
                        userMenu.classList.add('hidden');
                    }
                }

                function mudarAbaPerfil(aba) {
                    document.querySelectorAll('.espaco-painel').forEach(p => p.classList.add('hidden'));

                    ['abaGeral', 'abaSeguranca', 'abaNotificacoes'].forEach(id => {
                        const btn = document.getElementById(id);
                        if (btn) {
                            btn.className = "flex items-center gap-2 md:gap-3 px-3.5 py-2.5 rounded-xl font-semibold hover:bg-gray-200/60 dark:hover:bg-zinc-800 text-gray-600 dark:text-zinc-400 transition cursor-pointer whitespace-nowrap";
                        }
                    });

                    const painelAlvo = document.getElementById(`painel${aba.charAt(0).toUpperCase() + aba.slice(1)}`);
                    const botaoAlvo = document.getElementById(`aba${aba.charAt(0).toUpperCase() + aba.slice(1)}`);

                    if (painelAlvo) painelAlvo.classList.remove('hidden');
                    if (botaoAlvo) {
                        botaoAlvo.className = "flex items-center gap-2 md:gap-3 px-3.5 py-2.5 rounded-xl font-bold bg-indigo-600 text-white transition cursor-pointer shadow-sm whitespace-nowrap";
                    }
                }

                function mostrarAlertaSucesso(mensagem) {
                    const isDark = document.documentElement.classList.contains('dark');
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: mensagem,
                        timer: 1500,
                        showConfirmButton: false,
                        background: isDark ? '#18181b' : '#ffffff',
                        color: isDark ? '#f4f4f5' : '#18181b'
                    });
                }

                // Função de segurança auxiliar contra XSS
                function escapeHtml(text) {
                    if (!text) return '';
                    return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                }

            </script>

            <!-- CONTEÚDO PRINCIPAL (CORPO DA PÁGINA) -->
            <main class="p-6 max-w-[1600px] w-full mx-auto flex-1 overflow-y-auto animate-fade-in">

                <!-- 1. TOPO DA PÁGINA & CONTADORES RÁPIDOS -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
                    <div>
                        <h2 class="text-2xl font-black text-gray-800 tracking-tight">Gestão de Arguidos</h2>
                        <p class="text-sm font-medium text-gray-500 mt-1">Acompanhe, baixe e emita pareceres sobre os
                            arguidos, e seus antecedentes.</p>
                    </div>

                    <div
                        class="flex items-center gap-3 bg-amber-50 border border-amber-100/60 px-5 py-3 rounded-2xl self-start lg:self-auto">
                        <div
                            class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 text-base font-black">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-amber-700/80 uppercase tracking-wider">Aguardando
                                Avaliação</p>
                            <h3 id="total_processos" class="text-base font-black text-amber-800">---</h3>
                        </div>
                    </div>
                </div>

                <!-- 2. BARRA DE FILTROS, BUSCA E DEPÓSITO -->
                <div
                    class="bg-white p-4 rounded-3xl border border-gray-100 shadow-sm flex flex-col xl:flex-row xl:items-center justify-between gap-4 mb-6">
                    <div class="flex flex-wrap gap-2">
                        <button onclick="filtrarPor('todos')" id="badgeTotal"
                            class="bg-gray-50 hover:bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                            Total (0)
                        </button>
                        <button onclick="filtrarPor('finalizados')" id="badgeFinalizados"
                            class="bg-green-50 hover:bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                            Finalizados (0)
                        </button>
                        <button onclick="filtrarPor('pendentes')" id="badgePendentes"
                            class="bg-orange-50 hover:bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                            Pendentes (0)
                        </button>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto">
                        <div class="relative w-full sm:w-72">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-gray-400 text-xs"></i>
                            <input type="text" id="inputBuscaProcesso" placeholder="Buscar processo arguido..."
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 rounded-xl border border-gray-100 text-xs font-semibold text-gray-700 focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <button onclick="abrirModalFinalizar()"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl transition font-bold text-xs shadow-md shadow-indigo-100 cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Finalizar Processo
                        </button>
                    </div>
                </div>

                <!-- 3. GRID DE CARDS (Dinâmica) -->
                <div id="gridArguidos" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Os cards serão injetados automaticamente via JavaScript sem refresh -->
                </div>

                <!-- 4. PAGINAÇÃO ESTILO IPHONE -->
                <div id="paginacaoContainer" class="mt-8 flex items-center justify-center">
                    <!-- A paginação flutuante será gerada dinamicamente aqui -->
                </div>
            </main>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- OVERLAY E PAINEL DA FICHA DO ARGUIDO       -->
    <!-- ========================================== -->
    <div id="drawerFicha" class="fixed inset-0 z-50 hidden" aria-labelledby="slide-over-title" role="dialog"
        aria-modal="true">
        <!-- Fundo escuro com efeito Blur -->
        <div id="drawerOverlay" onclick="fecharFichaArguido()"
            class="fixed inset-0 bg-zinc-950/60 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <!-- Container do Painel -->
            <div id="drawerContent"
                class="w-screen max-w-2xl bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 ease-in-out">

                <!-- CABEÇALHO DO PAINEL -->
                <div
                    class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-950/50">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 flex items-center justify-center font-black">
                            <i class="fa-solid fa-id-card text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black uppercase text-zinc-900 dark:text-white tracking-wider">Ficha
                                Geral do Arguido</h3>
                            <p class="text-[11px] text-zinc-500 font-mono">PROCESSO Nº <span
                                    id="fichaNumProcesso">#2026/0481</span></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" id="btnImprimirFichaPDF" onclick="gerarPdfFichaArguido()"
                            class="p-2 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg transition-colors"
                            title="Imprimir Ficha">
                            <i class="fa-solid fa-print text-base"></i>
                        </button>
                        <button type="button" onclick="fecharFichaArguido()"
                            class="p-2 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- CORPO DO PAINEL (SCROLLÁVEL) -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">

                    <!-- CARTÃO DE RESUMO BIOMÉTRICO -->
                    <div
                        class="p-4 bg-zinc-50 dark:bg-zinc-950/40 rounded-2xl border border-zinc-200 dark:border-zinc-800 flex items-start gap-4">
                        <div
                            class="w-24 h-28 bg-zinc-200 dark:bg-zinc-800 rounded-xl border border-zinc-300 dark:border-zinc-700 overflow-hidden shrink-0 flex items-center justify-center">
                            <img id="fichaFoto" src="" alt="Foto do Arguido" class="w-full h-full object-cover hidden">
                            <i id="fichaFotoPlaceholder" class="fa-solid fa-user text-3xl text-zinc-400"></i>
                        </div>
                        <div class="flex-1 space-y-1">
                            <div class="flex items-center justify-between">
                                <span id="fichaStatus"
                                    class="px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                    Prisão Preventiva
                                </span>
                                <span class="text-[10px] font-mono text-zinc-400">AFIS Synced</span>
                            </div>
                            <h2 id="fichaNome"
                                class="text-base font-black text-zinc-900 dark:text-white uppercase leading-tight pt-1">
                                Manuel Domingos Eduardo
                            </h2>
                            <p class="text-xs text-zinc-500 font-medium">Alcunha: <span id="fichaAlcunha"
                                    class="text-zinc-800 dark:text-zinc-200 font-semibold">"Kito"</span></p>
                            <p class="text-xs text-zinc-500 font-mono pt-1">BI/Passaporte: <span id="fichaBI"
                                    class="text-zinc-800 dark:text-zinc-200 font-bold">-----</span></p>
                        </div>
                    </div>

                    <hr class="border-zinc-150 dark:border-zinc-800" />

                    <!-- DADOS PESSOAIS -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">Identidade Civíl &
                            Contactos</h4>
                        <div
                            class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-zinc-50/50 dark:bg-zinc-950/20 p-4 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 text-xs">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Data de Nasc.</span>
                                <span id="fichaDataNasc"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">12/05/1994 (31 anos)</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Nacionalidade</span>
                                <span id="fichaNacionalidade"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">-----</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Naturalidade</span>
                                <span id="fichaNaturalidade"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">-----</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Estado Civil</span>
                                <span id="fichaEstadoCivil"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">-----</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Profissão</span>
                                <span id="fichaProfissao"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">-----</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Idade
                                    (Estatuto)</span>
                                <span id="fichaIdade"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">-----</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Filiação</span>
                                <span id="fichaFiliacao" class="font-semibold text-zinc-800 dark:text-zinc-200">António
                                    Eduardo & Maria Domingos</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Residência
                                </span>
                                <span id="fichaResidencia"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">----</span>
                            </div>
                        </div>
                    </div>

                    <!-- MEDIDA COERCITIVA E HISTÓRICO -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">Situação Processual &
                            Medida</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3 bg-red-500/5 border border-red-500/20 rounded-xl space-y-1">
                                <span class="text-[10px] uppercase font-bold text-red-500 block">Tipificação do
                                    Crime</span>
                                <p id="fichaCrime" class="text-xs font-bold text-zinc-800 dark:text-zinc-100">Roubo
                                    Qualificado à Mão Armada</p>
                            </div>
                            <div
                                class="p-3 bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-200 dark:border-zinc-800 rounded-xl space-y-1">
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Prazo de
                                    Custódia</span>
                                <p id="fichaPrazo" class="text-xs font-bold text-zinc-800 dark:text-zinc-100">48 Horas
                                    (Detenção Provisória)</p>
                            </div>
                        </div>
                    </div>

                    <!-- BENS APREENDIDOS -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">Bens Apreendidos em
                            Custódia</h4>
                        <div
                            class="p-3 bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs space-y-1">
                            <p id="fichaBens" class="text-zinc-700 dark:text-zinc-300 font-medium">1x Telefone iPhone 13
                                Pro (Preto), 1x Carteira em pele contendo 15.000 Kz e cartões pessoais.</p>
                        </div>
                    </div>

                </div>

                <!-- RODAPÉ DO PAINEL -->
                <div
                    class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50 flex justify-between items-center">
                    <button type="button" id="btnImprimirFichaPDF" onclick="gerarPdfFichaArguido()"
                        class="px-4 py-2 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 text-xs font-bold uppercase rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf text-blue-600"></i>
                        Exportar PDF
                    </button>
                    <button type="button" onclick="fecharFichaArguido()"
                        class="px-5 py-2 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 text-xs font-black uppercase rounded-xl hover:opacity-90 transition-all">
                        Concluído
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SCRIPT DE RENDERIZAR       -->
    <!-- ========================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Estado atual da aplicação
            let estadoApp = {
                filtro: 'todos',
                busca: '',
                pagina: 1
            };

            let debounceTimer = null;

            // Elementos do DOM
            const gridArguidos = document.getElementById('gridArguidos');
            const inputBusca = document.getElementById('inputBuscaProcesso');
            const paginacaoContainer = document.getElementById('paginacaoContainer');

            const badgeTotal = document.getElementById('badgeTotal');
            const badgeTotal_processo = document.getElementById('total_processos');
            const badgeFinalizados = document.getElementById('badgeFinalizados');
            const badgePendentes = document.getElementById('badgePendentes');

            // Função central para carregar dados via Fetch
            window.carregarProcessos = function () {
                const url = `../controller/arguido/listar_processos.php?filtro=${estadoApp.filtro}&busca=${encodeURIComponent(estadoApp.busca)}&pagina=${estadoApp.pagina}`;

                // Feedback visual leve de carregamento na grid
                gridArguidos.style.opacity = '0.5';

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        gridArguidos.style.opacity = '1';
                        if (data.success) {
                            atualizarContadores(data.contadores);
                            atualizarContadores_processos(data.contadores);
                            renderizarCards(data.dados);
                            renderizarPaginacao(data.paginacao);
                        } else {
                            console.error('Erro ao carregar dados:', data.mensagem);
                        }
                    })
                    .catch(err => {
                        gridArguidos.style.opacity = '1';
                        console.error('Erro de rede:', err);
                    });
            };

            // Atualizar os números nos botões de filtro
            function atualizarContadores(c) {
                if (badgeTotal) badgeTotal.textContent = `Total (${c.total})`;
                if (badgeFinalizados) badgeFinalizados.textContent = `Finalizados (${c.finalizados})`;
                badgePendentes.textContent = `Pendentes (${c.pendentes})`;
            }

            function atualizarContadores_processos(c) {
                if (badgeTotal_processo) badgeTotal_processo.textContent = `${c.total} Processos`;
            }

            // Variável global para reter os dados ativos da grid
            let dadosAtuaisArguidos = [];

            // Renderizar a Grid de Cards Dinamicamente com Botões de Tamanhos Iguais e Responsivos
            function renderizarCards(lista) {
                if (!gridArguidos) return;

                // Guarda os dados na variável global para uso posterior no drawer da ficha
                dadosAtuaisArguidos = lista;

                if (lista.length === 99) {
                    gridArguidos.innerHTML = `
                <div class="col-span-full py-12 text-center text-gray-400 font-semibold text-xs">
                    <i class="fa-solid fa-folder-open text-3xl mb-2 opacity-40"></i>
                    <p>Nenhum registo encontrado com os critérios selecionados.</p>
                </div>`;
                    return;
                }

                gridArguidos.innerHTML = lista.map(item => {
                    const isFinalizado = item.status === 'finalizado';
                    const corStatusBg = isFinalizado ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700';
                    const iconeStatus = isFinalizado ? 'fa-circle-check' : 'fa-clock';
                    const iconeCardBg = isFinalizado ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-600';
                    const textoLabelStatus = isFinalizado ? 'Finalizado' : 'Agua. Interrogatório';

                    return `
                        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-xl hover:shadow-indigo-50/40 transition-all flex flex-col justify-between relative group">
                            <div>
                                <div class="flex items-start justify-between gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-xl ${iconeCardBg} flex items-center justify-center text-xl flex-shrink-0">
                                        <i class="fa-solid fa-scale-balanced"></i>
                                    </div>
                                    <span class="${corStatusBg} px-2.5 py-1 rounded-xl text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                                        <i class="fa-solid ${iconeStatus} mr-1"></i> ${textoLabelStatus}
                                    </span>
                                </div>

                                <div class="space-y-2 mb-5">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-indigo-50 text-indigo-700 px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider">
                                            Proc. nº ${item.num_processo}
                                        </span>
                                        <span class="text-[11px] text-gray-400 font-medium">${item.data}</span>
                                    </div>
                                    <div class="text-sm font-black text-gray-900 truncate">Arguido: ${item.arguido}</div>
                                    <h4 class="text-xs font-semibold text-gray-600 line-clamp-2 leading-snug">
                                        ${item.tipo_crime}
                                    </h4>
                                    
                                    <!-- Bloco de Ações com Larguras Uniformes (flex-1 em todos) -->
                                    <div class="bg-gray-50 p-2.5 rounded-xl flex items-center text-xs font-semibold text-gray-600 border border-gray-100/50 gap-2">
                                        <!-- Botão Ficha -->
                                        <button type="button" title="Ver Ficha do Arguido" onclick="abrirFichaArguido(${item.id})"
                                            class="h-9 px-2 flex-1 inline-flex items-center justify-center gap-1.5 bg-orange-800 hover:bg-orange-900 dark:bg-orange-700 dark:hover:bg-orange-600 text-white text-xs font-bold uppercase rounded-xl transition-all cursor-pointer shadow-sm">
                                            <i class="fa-solid fa-file-pdf text-sm"></i>
                                            <span class="hidden sm:inline">Ficha</span>
                                        </button>

                                        <!-- Botão Editar -->
                                        <a href="#" onclick="abrirModalCadastro(${item.id}); return false;" title="Editar Processo"
                                            class="h-9 px-2 flex-1 inline-flex items-center justify-center gap-1.5 text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition text-xs font-bold uppercase shadow-sm">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                            <span class="hidden sm:inline">Editar</span>
                                        </a>

                                        <!-- Botão Eliminar -->
                                        <button type="button" onclick="eliminarProcesso(${item.id})" title="Suspender Processo"
                                            class="h-9 px-2 flex-1 inline-flex items-center justify-center gap-1.5 text-white bg-gray-600 hover:bg-gray-700 rounded-xl transition text-xs font-bold uppercase shadow-sm cursor-pointer">
                                            <i class="fa-solid fa-ban text-sm"></i>
                                            <span class="hidden sm:inline">Suspender</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                                <span class="text-xs font-bold text-gray-500 truncate">
                                    <i class="fa-solid fa-user-shield text-gray-400 mr-1.5"></i> ${item.tecnico}
                                </span>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            // Renderizar Paginação iOS Dinâmica
            function renderizarPaginacao(pag) {
                if (!paginacaoContainer) return;

                if (pag.total_paginas <= 1) {
                    paginacaoContainer.innerHTML = '';
                    return;
                }

                let html = `
                    <nav aria-label="Navegação de Páginas" class="inline-flex items-center gap-1.5 p-1.5 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 rounded-full shadow-xl">
                        <!-- Anterior -->
                        <button type="button" onclick="mudarPagina(${pag.pagina_atual - 1})" ${pag.pagina_atual === 1 ? 'disabled' : ''}
                            class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                        <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-800 my-auto"></div>
                        <div class="flex items-center gap-1 px-1">`;

                for (let i = 1; i <= pag.total_paginas; i++) {
                    const ativa = i === pag.pagina_atual;
                    if (ativa) {
                        html += `<button type="button" class="px-3.5 py-1.5 rounded-full text-xs font-black bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 shadow-sm">${i}</button>`;
                    } else {
                        html += `<button type="button" onclick="mudarPagina(${i})" class="px-3.5 py-1.5 rounded-full text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer">${i}</button>`;
                    }
                }

                html += `</div>
                        <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-800 my-auto"></div>
                        <!-- Próximo -->
                        <button type="button" onclick="mudarPagina(${pag.pagina_atual + 1})" ${pag.pagina_atual === pag.total_paginas ? 'disabled' : ''}
                            class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </nav>
                `;

                paginacaoContainer.innerHTML = html;
            }

            // Eventos de Filtro por Categoria
            window.filtrarPor = function (tipo) {
                estadoApp.filtro = tipo;
                estadoApp.pagina = 1; // Resetar para a primeira página ao filtrar
                carregarProcessos();
            };

            // Evento de Mudança de Página Global
            window.mudarPagina = function (p) {
                estadoApp.pagina = p;
                carregarProcessos();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            // Evento de Busca em Tempo Real (Com Debounce de 300ms)
            if (inputBusca) {
                inputBusca.addEventListener('input', (e) => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        estadoApp.busca = e.target.value.trim();
                        estadoApp.pagina = 1;
                        carregarProcessos();
                    }, 300);
                });
            }

            // Carga inicial ao abrir a página
            carregarProcessos();

            window.eliminarProcesso = function (id) {
                Swal.fire({
                    title: 'Suspender Processo?',
                    text: "O processo será arquivado para efeitos de auditoria e deixará de aparecer na lista ativa. Poderá ser recuperado posteriormente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d97706', // Amarelo/Laranja escuro (Amber-600) para suspensão
                    cancelButtonColor: '#71717a',   // Cinzento
                    confirmButtonText: 'Sim, suspender!',
                    cancelButtonText: 'Cancelar',
                    background: document.documentElement.classList.contains('dark') ? '#18181b' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#f4f4f5' : '#18181b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`../controller/arguido/eliminar_processo.php?id=${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.sucesso || data.success) {
                                    Swal.fire({
                                        title: 'Suspenso!',
                                        text: data.mensagem || 'O processo foi arquivado com sucesso.',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    if (typeof carregarProcessos === 'function') {
                                        carregarProcessos();
                                    } else {
                                        setTimeout(() => location.reload(), 1500);
                                    }
                                } else {
                                    Swal.fire('Erro!', data.mensagem || 'Não foi possível suspender o processo.', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Erro na requisição:', error);
                                Swal.fire('Erro!', 'Ocorreu um erro de comunicação com o servidor.', 'error');
                            });
                    }
                });
            };

            /*/ <!-- ========================================== -->
            < !--SUPORTE JS FICHA DO ARGUIDO-- >
            < !-- ========================================== -->*/
            // Variável global para reter os dados ativos da grid
            let processoIdAtual = null; // Variável global para guardar o ID ativo no drawer

            window.abrirFichaArguido = function (id) {
                const item = dadosAtuaisArguidos.find(arg => String(arg.id) === String(id));
                if (!item) {
                    console.error("Registo não encontrado para o ID:", id);
                    return;
                }

                // Guarda o ID atual para o botão de PDF usar
                processoIdAtual = item.id;

                // 1. Cabeçalho & Status
                document.getElementById('fichaNumProcesso').textContent = item.num_processo || item.processo || '---';
                document.getElementById('fichaStatus').textContent = item.status_processual || item.status || 'Prisão Preventiva';

                // 2. Identificação Biométrica e Principal
                document.getElementById('fichaNome').textContent = item.arguido || item.nome || '---';
                document.getElementById('fichaAlcunha').textContent = item.alcunha ? `"${item.alcunha}"` : 'N/A';
                document.getElementById('fichaBI').textContent = item.bi || item.numero_bi || '---';

                // 3. Dados Pessoais & Contactos (Com suporte para Idade e Profissão corrigidos)
                document.getElementById('fichaDataNasc').textContent = item.data_nasc || item.data_nascimento || '---';
                document.getElementById('fichaNacionalidade').textContent = item.nacionalidade || '---';
                document.getElementById('fichaNaturalidade').textContent = item.naturalidade || '---';
                document.getElementById('fichaEstadoCivil').textContent = item.estado_civil || '---';
                document.getElementById('fichaProfissao').textContent = item.profissao || 'Não Especificada';
                document.getElementById('fichaIdade').textContent = item.idade || '---';
                document.getElementById('fichaFiliacao').textContent = item.filiacao || '---';
                document.getElementById('fichaResidencia').textContent = item.residencia || '---';

                // 4. Situação Processual & Medida
                document.getElementById('fichaCrime').textContent = item.tipo_crime || item.crime || '---';
                document.getElementById('fichaPrazo').textContent = item.prazo_custodia || '48 Horas';

                // 5. Bens Apreendidos
                document.getElementById('fichaBens').textContent = item.bens_apreendidos || item.bens || 'Nenhum bem registado.';

                // 6. Gestão da Foto
                const fotoImg = document.getElementById('fichaFoto');
                const fotoPlaceholder = document.getElementById('fichaFotoPlaceholder');

                if (item.foto && item.foto.trim() !== '') {
                    let caminhoFoto = item.foto;

                    if (!caminhoFoto.startsWith('http') && !caminhoFoto.startsWith('data:') && !caminhoFoto.startsWith('/') && !caminhoFoto.startsWith('../')) {
                        let nomeFicheiro = caminhoFoto.replace(/^uploads\//, '');
                        caminhoFoto = '../controller/arguidouploads/' + nomeFicheiro;
                    }

                    fotoImg.src = caminhoFoto;
                    fotoImg.classList.remove('hidden');
                    fotoPlaceholder.classList.add('hidden');
                } else {
                    fotoImg.classList.add('hidden');
                    fotoPlaceholder.classList.remove('hidden');
                }

                // 7. Animação e Abertura do Drawer
                const drawer = document.getElementById('drawerFicha');
                const overlay = document.getElementById('drawerOverlay');
                const content = document.getElementById('drawerContent');

                drawer.classList.remove('hidden');

                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');
                    content.classList.remove('translate-x-full');
                    content.classList.add('translate-x-0');
                }, 10);
            };

            // Função para Fechar o Drawer
            window.fecharFichaArguido = function () {
                const drawer = document.getElementById('drawerFicha');
                const overlay = document.getElementById('drawerOverlay');
                const content = document.getElementById('drawerContent');

                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
                content.classList.remove('translate-x-0');
                content.classList.add('translate-x-full');

                setTimeout(() => {
                    drawer.classList.add('hidden');
                }, 300);
            };

            window.gerarPdfFichaArguido = function () {
                if (!processoIdAtual) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Nenhum processo selecionado.',
                        confirmButtonColor: '#c2410c'
                    });
                    return;
                }

                // 1. Mostrar SweetAlert de carregamento (Gerando arquivo)
                Swal.fire({
                    title: 'A gerar documento...',
                    text: 'Por favor, aguarde enquanto o PDF está a ser processado.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const urlPdf = `../views/arguido/gerar_ficha.php?id=${processoIdAtual}`;

                // 2. Testar a requisição via fetch para garantir que o PDF gera sem erros 500/404
                fetch(urlPdf)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro ao gerar o ficheiro PDF no servidor.');
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        // Criar URL temporário do blob para forçar o download ou pré-visualização segura
                        const blobUrl = window.URL.createObjectURL(blob);

                        // Abrir numa nova aba
                        window.open(blobUrl, '_blank');

                        // Fechar o loading do SweetAlert com sucesso
                        Swal.fire({
                            icon: 'success',
                            title: 'PDF Gerado com Sucesso!',
                            text: 'O documento foi aberto numa nova aba.',
                            timer: 2500,
                            showConfirmButton: false
                        });
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Falha na Exportação',
                            text: 'Não foi possível gerar a ficha em PDF. Tente novamente.',
                            confirmButtonColor: '#c2410c'
                        });
                    });
            };

            // Fechar automaticamente ao pressionar a tecla 'ESC'
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    const drawer = document.getElementById('drawerFicha');
                    if (!drawer.classList.contains('hidden')) {
                        fecharFichaArguido();
                    }
                }
            });

        });
    </script>

    <!-- ========================================== -->
    <!-- MODAL: FINALIZAR REGISTRO / PROCESSO       -->
    <!-- ========================================== -->
    <div id="modalFinalizarProcesso"
        class="hidden fixed inset-0 bg-zinc-950/60 dark:bg-black/80 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 z-50 animate-fade-in">

        <div
            class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl w-full max-w-lg flex flex-col shadow-2xl overflow-hidden transform scale-95 transition-all duration-300">

            <!-- CABEÇALHO -->
            <div
                class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-white dark:bg-zinc-900 shrink-0">
                <div>
                    <h3
                        class="text-sm font-black uppercase text-zinc-900 dark:text-white tracking-wide flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up text-indigo-600 dark:text-indigo-400"></i>
                        Finalização de Registro
                    </h3>
                    <p class="text-xs text-zinc-400 mt-0.5">Informe o processo para validar a atribuição e
                        concluir.</p>
                </div>

                <button onclick="fecharModalFinalizar()" type="button"
                    class="p-2 -mr-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white rounded-xl transition-colors hover:bg-zinc-100 dark:hover:bg-zinc-800/60">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- CORPO DO MODAL -->
            <form class="p-6 space-y-5 text-left overflow-y-auto max-h-[75vh]">

                <!-- Campo de Busca do Número do Processo -->
                <div class="flex flex-col gap-1.5">
                    <label class="block text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                        Número do Processo
                    </label>
                    <div class="relative">
                        <input type="text" id="numProcessoBusca" oninput="validarEBuscarProcesso()"
                            placeholder="Ex: PROC-2026-001"
                            class="w-full pl-4 pr-10 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm font-mono uppercase text-zinc-800 dark:text-white placeholder:text-zinc-400 focus:ring-2 focus:ring-indigo-600 focus:outline-hidden transition-all">
                        <div class="absolute right-3 top-3 text-zinc-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- CARD DE INFORMAÇÕES DO PROCESSO (Exibido após encontrar) -->
                <div id="cardDetalhesProcesso"
                    class="hidden space-y-3 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/30">

                    <!-- Nome do Arguido -->
                    <div
                        class="flex justify-between items-start border-b border-zinc-200/60 dark:border-zinc-800/60 pb-2.5">
                        <span class="text-xs font-bold text-zinc-400 uppercase">Arguido:</span>
                        <span id="labelNomeArguido"
                            class="text-sm font-black text-zinc-900 dark:text-white text-right">--</span>
                    </div>

                    <!-- Técnico Responsável -->
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-zinc-400 uppercase">Técnico Atribuído:</span>
                        <span id="labelTecnicoResponsavel"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-zinc-200/60 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
                            <i class="fa-solid fa-user-gear text-[10px]"></i>
                            <span id="nomeTecnicoTexto">--</span>
                        </span>
                    </div>
                </div>

                <!-- NOTIFICAÇÃO DE ALERTA: RESTRIÇÃO DE ACESSO -->
                <div id="alertaPermissaoNegada"
                    class="hidden p-4 rounded-xl border border-amber-500/30 bg-amber-500/10 text-amber-800 dark:text-amber-300 space-y-1">
                    <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400"></i>
                        <span>Aviso de Restrição</span>
                    </div>
                    <p class="text-xs leading-relaxed opacity-90">
                        Este processo está sob responsabilidade de <strong id="nomeTecnicoAlerta"
                            class="underline">--</strong>. Apenas o técnico designado possui permissão para
                        concluir este registro.
                    </p>
                </div>

                <!-- NOTIFICAÇÃO: PROCESSO NÃO ENCONTRADO -->
                <div id="alertaNaoEncontrado"
                    class="hidden p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800/40 text-zinc-500 dark:text-zinc-400 text-xs text-center font-medium">
                    Nenhum processo localizado com este número.
                </div>

                <!-- BOTOES DE AÇÃO -->
                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <button type="reset"
                        class="w-full sm:w-1/3 py-2.5 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white text-xs font-bold uppercase rounded-xl transition-all">
                        Limpar
                    </button>

                    <!-- Botão Habilitado Apenas quando a Validação passar -->
                    <button type="button" id="btnConcluirRegistro" disabled onclick="abrirModalCadastro()"
                        class="w-full sm:w-2/3 py-2.5 bg-indigo-600 disabled:bg-zinc-300 dark:disabled:bg-zinc-800 disabled:text-zinc-500 dark:disabled:text-zinc-600 text-white text-xs font-black uppercase tracking-wider rounded-xl cursor-pointer hover:bg-indigo-700 disabled:cursor-not-allowed transition-all shadow-md shadow-indigo-100 dark:shadow-none flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i> Concluir Registro
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL DO USUÁRIO LOGADO E BASE DE DADOS DE PROCESSOS       -->
    <!-- ========================================== -->
    <script>
        // Função para abrir o modal de finalização
        function abrirModalFinalizar() {
            const modal = document.getElementById('modalFinalizarProcesso');
            if (modal) {
                modal.classList.remove('hidden');

                // Opcional: focar automaticamente no input de busca ao abrir
                setTimeout(() => {
                    const input = document.getElementById('numProcessoBusca');
                    if (input) input.focus();
                }, 100);
            } else {
                console.error("Elemento #modalFinalizarProcesso não encontrado no DOM.");
            }
        }

        // Função para fechar o modal de finalização e limpar os campos
        function fecharModalFinalizar() {
            const modal = document.getElementById('modalFinalizarProcesso');
            if (modal) {
                modal.classList.add('hidden');

                // Limpa os campos e estados ao fechar
                const input = document.getElementById('numProcessoBusca');
                if (input) input.value = '';

                document.getElementById('cardDetalhesProcesso').classList.add('hidden');
                document.getElementById('alertaPermissaoNegada').classList.add('hidden');
                document.getElementById('alertaNaoEncontrado').classList.add('hidden');
                document.getElementById('btnConcluirRegistro').setAttribute('disabled', 'true');
            }
        }

        let debounceTimer;

        // Removemos a declaração global de 'tempoEsperaBusca' para evitar conflitos de re-declaração.
        if (typeof window.tempoEsperaBusca === 'undefined') {
            window.tempoEsperaBusca = null;
        }

        function validarEBuscarProcesso() {
            const inputBusca = document.getElementById('numProcessoBusca');
            const cardDetalhes = document.getElementById('cardDetalhesProcesso');
            const alertaPermissao = document.getElementById('alertaPermissaoNegada');
            const alertaNaoEncontrado = document.getElementById('alertaNaoEncontrado');
            const btnConcluir = document.getElementById('btnConcluirRegistro');

            const labelNome = document.getElementById('labelNomeArguido');
            const nomeTecnicoTexto = document.getElementById('nomeTecnicoTexto');
            const nomeTecnicoAlerta = document.getElementById('nomeTecnicoAlerta');

            if (!inputBusca) return;

            const termoBusca = inputBusca.value.trim();

            if (!termoBusca) {
                if (cardDetalhes) cardDetalhes.classList.add('hidden');
                if (alertaPermissao) alertaPermissao.classList.add('hidden');
                if (alertaNaoEncontrado) alertaNaoEncontrado.classList.add('hidden');
                if (btnConcluir) btnConcluir.disabled = true;
                return;
            }

            fetch(`../api/buscar_processo.php?numero=${encodeURIComponent(termoBusca)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.processo) {
                        if (alertaNaoEncontrado) alertaNaoEncontrado.classList.add('hidden');

                        if (labelNome) labelNome.textContent = data.processo.arguido || 'N/D';
                        if (nomeTecnicoTexto) nomeTecnicoTexto.textContent = data.processo.tecnicoNome || 'Não atribuído';
                        if (nomeTecnicoAlerta) nomeTecnicoAlerta.textContent = data.processo.tecnicoNome || 'Outro técnico';

                        if (cardDetalhes) cardDetalhes.classList.remove('hidden');

                        // VERIFICAÇÃO DE JÁ REGISTADO NA TABELA ARGUIDOS
                        if (data.ja_registrado) {
                            if (alertaPermissao) {
                                alertaPermissao.textContent = data.mensagem || 'Este processo já possui um arguido registado.';
                                alertaPermissao.classList.remove('hidden');
                            }
                            if (btnConcluir) btnConcluir.disabled = true;
                            return;
                        }

                        // Validação normal de autorização do técnico responsável
                        if (data.autorizado) {
                            if (alertaPermissao) alertaPermissao.classList.add('hidden');
                            if (btnConcluir) btnConcluir.disabled = false;

                            const campoNomeForm = document.getElementById('nome');
                            const campoProcessoForm = document.getElementById('n_processo');

                            if (campoNomeForm) campoNomeForm.value = data.processo.arguido || '';
                            if (campoProcessoForm) campoProcessoForm.value = data.processo.id || '';

                        } else {
                            if (alertaPermissao) {
                                alertaPermissao.textContent = 'Não tem permissão para registar dados neste processo de outro técnico.';
                                alertaPermissao.classList.remove('hidden');
                            }
                            if (btnConcluir) btnConcluir.disabled = true;
                        }

                    } else {
                        if (cardDetalhes) cardDetalhes.classList.add('hidden');
                        if (alertaPermissao) alertaPermissao.classList.add('hidden');
                        if (alertaNaoEncontrado) {
                            alertaNaoEncontrado.textContent = data.mensagem || 'Processo não encontrado.';
                            alertaNaoEncontrado.classList.remove('hidden');
                        }
                        if (btnConcluir) btnConcluir.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Erro na comunicação com a API:', error);
                });
        }

        /**
         * Função responsável por transitar da etapa de busca para o preenchimento das abas do arguido
         */
        function prosseguirParaAbasCadastro() {
            const modal = document.getElementById('modalCadastroArguido');

            if (modal) {
                modal.classList.remove('hidden'); // Remove o 'hidden' e exibe o modal
            }

            // Reinicia o estado inicial para a primeira aba (Biometria)
            abaAtual = 0;

            // Atualiza a visualização da primeira aba e oculta as restantes
            const abasElementos = ['abaBiometria', 'abaDadosPessoais', 'abaAntecedentes', 'abaConclusao'];
            abasElementos.forEach((id, index) => {
                const el = document.getElementById(id);
                if (el) {
                    if (index === 99) {
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                }
            });

            // --- ATUALIZAÇÃO VISUAL DAS ABAS E NAVEGAÇÃO ---
            atualizarEstiloAbasTopo();   // Destaca visualmente o separador 1 (Biometria) no topo
            atualizarBotoesNavegacao(); // Ajusta os botões inferior (Próximo / Anterior)

            // Se houver câmera na primeira aba, tenta iniciá-la
            if (typeof iniciarCamera === 'function') {
                iniciarCamera();
            }
        }

        function executarFinalizacao() {
            const numeroProcesso = document.getElementById('numProcessoBusca').value;
            if (typeof abrirConclusaoProcesso === 'function') {
                abrirConclusaoProcesso(numeroProcesso);
            }
        }
    </script>

    <!-- ======================================================= -->
    <!-- MODAL 1: ARGUIDO (CADASTRO / EDIÇÃO) -->
    <!-- ======================================================= -->

    <!-- OVERLAY DO MODAL DE CADASTRO DO ARGUIDO (DESIGN FLUIDO & RESPONSIVO) -->
    <div id="modalCadastroArguido"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-3 sm:p-6 animate-fade-in">

        <!-- CONTAINER DO MODAL: Altura fluida e bordas suaves -->
        <div
            class="bg-white dark:bg-zinc-900 w-full max-w-6xl max-h-[88vh] sm:max-h-[90vh] rounded-2xl sm:rounded-3xl shadow-2xl border border-zinc-100 dark:border-zinc-800/80 flex flex-col overflow-hidden transition-all">

            <!-- CABEÇALHO DO MODAL + BARRA DE ABAS -->
            <div
                class="px-5 pt-5 sm:px-8 sm:pt-7 pb-0 border-b border-zinc-100 dark:border-zinc-800/60 bg-zinc-50/40 dark:bg-zinc-950/20 shrink-0">

                <!-- TÍTULO E BOTÃO FECHAR -->
                <div class="flex items-start sm:items-center justify-between gap-4 mb-4 sm:mb-6">
                    <div>
                        <h3
                            class="text-base sm:text-xl font-bold text-zinc-900 dark:text-white uppercase tracking-wider">
                            Cadastro de Arguido
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Preencha as informações necessárias organizadas por etapas.
                        </p>
                    </div>
                    <button type="button" onclick="fecharModalCadastro()"
                        class="text-zinc-400 hover:text-zinc-700 dark:hover:text-white p-2 rounded-2xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- NAVEGAÇÃO DE ABAS (ÍCONES NO MOBILE / TEXTO NO DESKTOP) -->
                <nav class="flex items-center justify-between sm:justify-start gap-2 sm:gap-6 border-b border-zinc-200 dark:border-zinc-800 -mb-px overflow-x-auto no-scrollbar"
                    id="tabNav">

                    <!-- ABA 1: BIOMETRIA -->
                    <button type="button" onclick="irParaAba(0)" title="Biometria"
                        class="tab-btn pb-3 px-2 sm:px-1 text-xs font-black uppercase text-zinc-900 dark:text-white border-b-2 border-zinc-900 dark:border-white flex items-center gap-2.5 transition cursor-pointer shrink-0">
                        <span
                            class="tab-badge w-8 h-8 sm:w-6 sm:h-6 rounded-xl sm:rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-xs sm:text-[10px] flex items-center justify-center font-bold transition">
                            <i class="fa-solid fa-fingerprint text-sm sm:hidden"></i>
                            <span class="hidden sm:inline">1</span>
                        </span>
                        <span class="hidden sm:inline tracking-wider">Biometria</span>
                    </button>

                    <!-- ABA 2: DADOS PESSOAIS -->
                    <button type="button" onclick="irParaAba(1)" title="Dados Pessoais"
                        class="tab-btn pb-3 px-2 sm:px-1 text-xs font-semibold uppercase text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 border-b-2 border-transparent flex items-center gap-2.5 transition cursor-pointer shrink-0">
                        <span
                            class="tab-badge w-8 h-8 sm:w-6 sm:h-6 rounded-xl sm:rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs sm:text-[10px] flex items-center justify-center font-bold transition">
                            <i class="fa-solid fa-user text-sm sm:hidden"></i>
                            <span class="hidden sm:inline">2</span>
                        </span>
                        <span class="hidden sm:inline tracking-wider">Dados Pessoais</span>
                    </button>

                    <!-- ABA 3: ANTECEDENTES -->
                    <button type="button" onclick="irParaAba(2)" title="Antecedentes"
                        class="tab-btn pb-3 px-2 sm:px-1 text-xs font-semibold uppercase text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 border-b-2 border-transparent flex items-center gap-2.5 transition cursor-pointer shrink-0">
                        <span
                            class="tab-badge w-8 h-8 sm:w-6 sm:h-6 rounded-xl sm:rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs sm:text-[10px] flex items-center justify-center font-bold transition">
                            <i class="fa-solid fa-gavel text-sm sm:hidden"></i>
                            <span class="hidden sm:inline">3</span>
                        </span>
                        <span class="hidden sm:inline tracking-wider">Antecedentes</span>
                    </button>

                    <!-- ABA 4: BENS / CUSTÓDIA -->
                    <button type="button" onclick="irParaAba(3)" title="Bens / Custódia"
                        class="tab-btn pb-3 px-2 sm:px-1 text-xs font-semibold uppercase text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 border-b-2 border-transparent flex items-center gap-2.5 transition cursor-pointer shrink-0">
                        <span
                            class="tab-badge w-8 h-8 sm:w-6 sm:h-6 rounded-xl sm:rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs sm:text-[10px] flex items-center justify-center font-bold transition">
                            <i class="fa-solid fa-box-archive text-sm sm:hidden"></i>
                            <span class="hidden sm:inline">4</span>
                        </span>
                        <span class="hidden sm:inline tracking-wider">Bens / Custódia</span>
                    </button>
                </nav>
            </div>

            <!-- CORPO DO MODAL (ESPAÇOSO E COM ROLAGEM SUAVE) -->
            <div class="flex-1 overflow-y-auto p-5 sm:p-8 space-y-6">
                <form id="cadastroForm" autocomplete="off" onsubmit="event.preventDefault();"
                    class="h-full flex flex-col justify-between">

                    <!-- INPUTS OCULTOS PARA TRANSPORTE DE BIOMETRIA AO PHP -->
                    <input type="hidden" id="biometria_face" name="biometria_face">
                    <input type="hidden" id="biometria_digital" name="biometria_digital">
                    <!-- Input para o ID do Arguido (Indica ao PHP se é Edição ou Novo Registo) -->
                    <input type="hidden" id="arguido_id" name="arguido_id">

                    <!-- Input para o Número do Processo -->
                    <input type="hidden" id="n_processo" name="processo_id">

                    <div class="flex-1 overflow-y-auto pr-1 space-y-4">
                        <!-- ABA 01: CAPTURA BIOMÉTRICA -->
                        <div id="abaBiometria" class="space-y-6 animate-fade-in">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Reconhecimento Facial -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">01.1 -
                                        Reconhecimento Facial (ICAO Standard) <span class="text-red-500">*</span></h4>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="flex flex-col gap-2">
                                            <div
                                                class="w-full aspect-4/3 bg-black rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800 shadow-inner">
                                                <video id="video" autoplay playsinline muted
                                                    class="w-full h-full object-cover"></video>
                                            </div>
                                            <button type="button" id="captureBtn" onclick="capturarFace()"
                                                class="py-2.5 bg-zinc-950 text-white dark:bg-zinc-800 text-xs font-bold uppercase rounded-xl cursor-pointer hover:opacity-90 transition-opacity">
                                                Capturar Face
                                            </button>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <div
                                                class="w-full aspect-4/3 bg-zinc-50 dark:bg-zinc-950 rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800 flex items-center justify-center">
                                                <canvas id="canvas" class="w-full h-full object-cover hidden"></canvas>
                                                <span id="canvasPlaceholder"
                                                    class="text-xs text-zinc-400 uppercase font-bold">Aguardando...</span>
                                            </div>
                                            <span
                                                class="text-xs text-center text-zinc-500 font-mono py-2.5 bg-zinc-100 dark:bg-zinc-950/40 rounded-xl border border-zinc-200/50 dark:border-zinc-800/50">
                                                Snapshot Oficial
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Impressões Digitais -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">01.2 -
                                        Varredura Dactiloscópica (AFIS)</h4>
                                    <div
                                        class="p-4 bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center gap-4">
                                        <div id="hardwareLeitor" onclick="simularLeituraDigital()"
                                            class="w-20 h-24 border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-zinc-900 dark:hover:border-white rounded-xl flex flex-col items-center justify-center p-2 cursor-pointer transition-all bg-white dark:bg-zinc-900 group relative overflow-hidden shrink-0">
                                            <svg id="iconDigital"
                                                class="w-8 h-8 text-zinc-300 group-hover:text-zinc-600 dark:group-hover:text-zinc-400 transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M12 11c0-.517.074-1.017.212-1.492M12 11c0 .517-.074 1.017-.212 1.492M12 11a4 4 0 100-8 4 4 0 000 8zm0 0v5a3 3 0 01-3 3m3-8a3 3 0 00-3 3v1m6-4a3 3 0 013 3v1m-6 4h.01M12 16h.01" />
                                            </svg>
                                            <span id="textoLeitor"
                                                class="text-[9px] font-black uppercase text-zinc-400 tracking-wider mt-2 group-hover:text-zinc-900 dark:group-hover:text-white">Pressione</span>
                                            <div id="laserScan"
                                                class="absolute inset-x-0 h-0.5 bg-emerald-500 shadow-xs top-0 hidden">
                                            </div>
                                        </div>
                                        <div class="space-y-1.5">
                                            <span id="badgeDigitalStatus"
                                                class="inline-block px-2.5 py-1 text-xs font-bold bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-md uppercase">
                                                Hardware Desconectado
                                            </span>
                                            <p class="text-xs text-zinc-500 leading-relaxed">Posicione o polegar direito
                                                do arguido sobre a lente para validação imediata.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ABA 02: DADOS PESSOAIS -->
                        <div id="abaDadosPessoais" class="space-y-6 hidden animate-fade-in">
                            <div class="space-y-4">
                                <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">
                                    02.1 - Dados de Identidade Civil
                                </h4>

                                <!-- Grid Responsivo: 1 col (Mobile) -> 2 cols (Tablet) -> 3 cols (Desktop) -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">

                                    <!-- Nome Completo -->
                                    <div class="sm:col-span-2 lg:col-span-3 flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Nome Completo <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="nome" name="nome" required readonly
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>

                                    <!-- Alcunha -->
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Alcunha (Opcional)
                                        </label>
                                        <input type="text" id="alcunha" name="alcunha"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>

                                    <!-- Data de Nascimento -->
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Data de nascimento <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" id="data_nascimento" name="data_nascimento" required
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>

                                    <!-- BI / Passaporte -->
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Nº do BI / Passaporte <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="bi" name="bi" required
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm font-mono focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white uppercase transition-all">
                                    </div>

                                    <!-- Nacionalidade com Dropdown Pesquisável -->
                                    <div class="flex flex-col gap-1.5 relative">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Nacionalidade <span class="text-red-500">*</span>
                                        </label>

                                        <input type="hidden" id="nacionalidade" name="nacionalidade" value="Angolana">

                                        <div class="relative">
                                            <input type="text" id="nacionalidade_input"
                                                placeholder="Digite para pesquisar..." value="Angolana"
                                                autocomplete="off"
                                                class="w-full px-3.5 pr-10 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm h-11 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-700 dark:text-zinc-300 transition-all">
                                            <span
                                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                                                <i class="fa-solid fa-chevron-down text-xs"></i>
                                            </span>
                                        </div>

                                        <div id="nacionalidade_dropdown"
                                            class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl max-h-60 overflow-y-auto z-50 hidden">
                                        </div>
                                    </div>

                                    <script>
                                        const nacionalidadesList = <?php echo json_encode($nacionalidadesList); ?>;

                                        document.addEventListener('DOMContentLoaded', function () {
                                            const input = document.getElementById('nacionalidade_input');
                                            const hiddenInput = document.getElementById('nacionalidade');
                                            const dropdown = document.getElementById('nacionalidade_dropdown');

                                            if (!input || !dropdown) return;

                                            function renderizarOpcoes(filtro = '') {
                                                const termo = filtro.toLowerCase();
                                                const filtradas = nacionalidadesList.filter(n => n.toLowerCase().includes(termo));

                                                if (filtradas.length === 0) {
                                                    dropdown.innerHTML = `<div class="px-4 py-3 text-xs text-zinc-400">Nenhuma nacionalidade encontrada</div>`;
                                                    return;
                                                }

                                                dropdown.innerHTML = filtradas.map(n => `
                                                    <div class="px-4 py-3 sm:py-2.5 text-base sm:text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 active:bg-zinc-200 cursor-pointer transition-colors"
                                                        onclick="selecionarNacionalidade('${n}')">
                                                        ${n}
                                                    </div>
                                                `).join('');
                                            }

                                            input.addEventListener('focus', () => {
                                                dropdown.classList.remove('hidden');
                                                renderizarOpcoes(input.value);
                                            });

                                            input.addEventListener('input', (e) => {
                                                dropdown.classList.remove('hidden');
                                                hiddenInput.value = e.target.value;
                                                renderizarOpcoes(e.target.value);
                                            });

                                            document.addEventListener('click', (e) => {
                                                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                                                    dropdown.classList.add('hidden');
                                                }
                                            });
                                        });

                                        function selecionarNacionalidade(valor) {
                                            const input = document.getElementById('nacionalidade_input');
                                            const hiddenInput = document.getElementById('nacionalidade');
                                            const dropdown = document.getElementById('nacionalidade_dropdown');

                                            if (input && hiddenInput && dropdown) {
                                                input.value = valor;
                                                hiddenInput.value = valor;
                                                dropdown.classList.add('hidden');
                                            }
                                        }
                                    </script>

                                    <!-- Naturalidade -->
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Naturalidade <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="naturalidade" name="naturalidade"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>

                                    <!-- Estado Civil -->
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Estado Civil <span class="text-red-500">*</span>
                                        </label>
                                        <select id="estado_civil" name="estado_civil"
                                            class="w-full px-3.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm h-11 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-700 dark:text-zinc-300 transition-all">
                                            <option value="solteiro">Solteiro(a)</option>
                                            <option value="casado">Casado(a)</option>
                                            <option value="viuvo">Viúvo(a)</option>
                                            <option value="divorciado">Divorciado(a)</option>
                                        </select>
                                    </div>

                                    <!-- Profissão Atual com Dropdown Pesquisável -->
                                    <div class="flex flex-col gap-1.5 relative">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Profissão Atual <span class="text-red-500">*</span>
                                        </label>

                                        <input type="hidden" id="profissao" name="profissao" value="">

                                        <div class="relative">
                                            <input type="text" id="profissao_input"
                                                placeholder="Ex: Estudante, Comerciante..." autocomplete="off"
                                                class="w-full px-3.5 pr-10 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm h-11 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                            <span
                                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                                                <i class="fa-solid fa-chevron-down text-xs"></i>
                                            </span>
                                        </div>

                                        <div id="profissao_dropdown"
                                            class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl max-h-60 overflow-y-auto z-50 hidden">
                                        </div>
                                    </div>

                                    <script>
                                        const profissoesList = <?php echo json_encode($profissoesList); ?>;

                                        document.addEventListener('DOMContentLoaded', function () {
                                            const input = document.getElementById('profissao_input');
                                            const hiddenInput = document.getElementById('profissao');
                                            const dropdown = document.getElementById('profissao_dropdown');

                                            if (!input || !dropdown) return;

                                            function renderizarOpcoesProfissao(filtro = '') {
                                                const termo = filtro.toLowerCase();
                                                const filtradas = profissoesList.filter(p => p.toLowerCase().includes(termo));

                                                if (filtradas.length === 0) {
                                                    dropdown.innerHTML = `<div class="px-4 py-3 text-xs text-zinc-400">Nenhuma profissão encontrada</div>`;
                                                    return;
                                                }

                                                dropdown.innerHTML = filtradas.map(p => `
                                                    <div class="px-4 py-3 sm:py-2.5 text-base sm:text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 active:bg-zinc-200 cursor-pointer transition-colors"
                                                        onclick="selecionarProfissao('${p}')">
                                                        ${p}
                                                    </div>
                                                `).join('');
                                            }

                                            input.addEventListener('focus', () => {
                                                dropdown.classList.remove('hidden');
                                                renderizarOpcoesProfissao(input.value);
                                            });

                                            input.addEventListener('input', (e) => {
                                                dropdown.classList.remove('hidden');
                                                hiddenInput.value = e.target.value;
                                                renderizarOpcoesProfissao(e.target.value);
                                            });

                                            document.addEventListener('click', (e) => {
                                                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                                                    dropdown.classList.add('hidden');
                                                }
                                            });
                                        });

                                        function selecionarProfissao(valor) {
                                            const input = document.getElementById('profissao_input');
                                            const hiddenInput = document.getElementById('profissao');
                                            const dropdown = document.getElementById('profissao_dropdown');

                                            if (input && hiddenInput && dropdown) {
                                                input.value = valor;
                                                hiddenInput.value = valor;
                                                dropdown.classList.add('hidden');
                                            }
                                        }
                                    </script>

                                    <!-- Nome do Pai -->
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Nome do Pai (Opcional)
                                        </label>
                                        <input type="text" id="nome_pai" name="nome_pai"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>

                                    <!-- Nome da Mãe -->
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Nome da Mãe (Opcional)
                                        </label>
                                        <input type="text" id="nome_mae" name="nome_mae"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>

                                    <!-- Telefone -->
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Telefone (Opcional)
                                        </label>
                                        <input type="tel" id="telefone" name="telefone"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>

                                    <!-- Idade e Estatuto -->
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex justify-between items-center flex-wrap gap-1">
                                            <label
                                                class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                                Idade / Estatuto
                                            </label>
                                            <span id="badge-estatuto"
                                                class="hidden px-2 py-0.5 text-[10px] font-bold rounded-md"></span>
                                        </div>
                                        <input type="text" id="idade" name="idade" readonly required
                                            placeholder="Calculada automaticamente"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-900 text-base sm:text-sm text-zinc-600 dark:text-zinc-400 cursor-not-allowed transition-all">
                                    </div>

                                    <!-- Residência Frequente / Morada -->
                                    <div class="sm:col-span-2 lg:col-span-3 flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Residência Frequente / Morada <span class="text-red-500">*</span>
                                        </label>
                                        <textarea id="residencia" name="residencia" rows="2"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-base sm:text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white resize-none transition-all"></textarea>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- ABA 03: ANTECEDENTES E MEDIDAS COERCITIVAS -->
                        <div id="abaAntecedentes" class="space-y-6 hidden animate-fade-in">
                            <div class="space-y-4">
                                <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">03.1 -
                                    Antecedentes Criminais</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="sm:col-span-2 flex flex-col gap-1.5 relative">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">
                                            Tipo de Crime Cometido <span class="text-red-500">*</span>
                                        </label>

                                        <!-- Campo Oculto que envia o valor real para a base de dados -->
                                        <input type="hidden" id="tipo_crime" name="tipo_crime" value="">

                                        <!-- Campo Visível para Digitar e Filtrar -->
                                        <div class="relative">
                                            <input type="text" id="tipo_crime_input"
                                                placeholder="Ex: Roubo Qualificado, Tráfico de Estupefacientes"
                                                autocomplete="off"
                                                class="w-full px-3.5 pr-10 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm h-11 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">

                                            <span
                                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                                                <i class="fa-solid fa-chevron-down text-xs"></i>
                                            </span>
                                        </div>

                                        <!-- Lista Flutuante Dinâmica -->
                                        <div id="tipo_crime_dropdown"
                                            class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl max-h-60 overflow-y-auto z-50 hidden">
                                        </div>
                                    </div>
                                    <script>
                                        // Injeta a lista de crimes vinda do PHP de forma segura no JavaScript
                                        const tiposCrimeList = <?php echo json_encode($tiposCrimeList); ?>;

                                        document.addEventListener('DOMContentLoaded', function () {
                                            const input = document.getElementById('tipo_crime_input');
                                            const hiddenInput = document.getElementById('tipo_crime');
                                            const dropdown = document.getElementById('tipo_crime_dropdown');

                                            if (!input || !dropdown) return;

                                            function renderizarOpcoesCrime(filtro = '') {
                                                const termo = filtro.toLowerCase();
                                                const filtradas = tiposCrimeList.filter(c => c.toLowerCase().includes(termo));

                                                if (filtradas.length === 0) {
                                                    dropdown.innerHTML = `<div class="px-4 py-2.5 text-xs text-zinc-400">Nenhum tipo de crime encontrado</div>`;
                                                    return;
                                                }

                                                dropdown.innerHTML = filtradas.map(c => `
                                                    <div class="px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors"
                                                        onclick="selecionarCrime('${c.replace(/'/g, "\\'")}')">
                                                        ${c}
                                                    </div>
                                                `).join('');
                                            }

                                            input.addEventListener('focus', () => {
                                                dropdown.classList.remove('hidden');
                                                renderizarOpcoesCrime(input.value);
                                            });

                                            input.addEventListener('input', (e) => {
                                                dropdown.classList.remove('hidden');
                                                hiddenInput.value = e.target.value; // Permite digitação livre caso necessário
                                                renderizarOpcoesCrime(e.target.value);
                                            });

                                            document.addEventListener('click', (e) => {
                                                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                                                    dropdown.classList.add('hidden');
                                                }
                                            });
                                        });

                                        function selecionarCrime(valor) {
                                            const input = document.getElementById('tipo_crime_input');
                                            const hiddenInput = document.getElementById('tipo_crime');
                                            const dropdown = document.getElementById('tipo_crime_dropdown');

                                            if (input && hiddenInput && dropdown) {
                                                input.value = valor;
                                                hiddenInput.value = valor;
                                                dropdown.classList.add('hidden');
                                            }
                                        }
                                    </script>
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Já
                                            cumpriu Pena?</label>
                                        <select id="reincidente" name="reincidente"
                                            class="w-full px-3.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm h-11 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-700 dark:text-zinc-300 transition-all">
                                            <option value="Não">Não (Primário)</option>
                                            <option value="Sim">Sim (Reincidente)</option>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2 flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Estabelecimento
                                            Prisional Anterior <span class="text-red-500">*</span></label>
                                        <input type="text" id="estabelecimento_prisional"
                                            name="estabelecimento_prisional" placeholder="Ex: Comarca de Viana"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Tempo
                                            Cumprido (Opcional)</label>
                                        <input type="text" id="tempo_cumprido" name="tempo_cumprido"
                                            placeholder="Ex: 2 anos e 4 meses"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>
                                </div>
                            </div>

                            <hr class="border-zinc-150 dark:border-zinc-800" />

                            <div class="space-y-4">
                                <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">03.2 - Captura &
                                    Medida de Coação</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                                    <div class="sm:col-span-2 flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Regime
                                            / Tipo de Medida <span class="text-red-500">*</span></label>
                                        <select id="tipo_medida" name="tipo_medida"
                                            class="w-full px-3.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm h-11 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-700 dark:text-zinc-300 transition-all">
                                            <option value="Detenção Provisória">Detenção Provisória (Polícia)</option>
                                            <option value="Prisão Preventiva">Prisão Preventiva (Juiz de Garantias)
                                            </option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Grandeza
                                            do Prazo <span class="text-red-500">*</span></label>
                                        <select id="prazo_unidade" name="prazo_unidade"
                                            class="w-full px-3.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm h-11 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-700 dark:text-zinc-300 transition-all">
                                            <option value="Horas">Horas</option>
                                            <option value="Dias">Dias</option>
                                            <option value="Meses">Meses</option>
                                            <option value="Anos">Anos</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Qtd.
                                            do Prazo <span class="text-red-500">*</span></label>
                                        <input type="number" id="prazo_quantidade" name="prazo_quantidade" min="1"
                                            placeholder="Ex: 48, 3, 1"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>
                                    <div class="sm:col-span-2 flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Data/Hora
                                            da Captura (Início do Prazo) <span class="text-red-500">*</span></label>
                                        <input type="datetime-local" id="data_inicio" name="data_inicio"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>
                                    <div class="sm:col-span-2 flex flex-col gap-1.5">
                                        <label
                                            class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Observações
                                            da Custódia</label>
                                        <input type="text" id="observacoes_detencao" name="observacoes_detencao"
                                            placeholder="Ex: Sem lesões visíveis aparentes"
                                            class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ABA 04: REGISTO DE BENS -->
                        <div id="abaConclusao" class="space-y-4 hidden animate-fade-in">
                            <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">04.1 - Bens
                                Apreendidos & Custódia</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label
                                        class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Descrição
                                        detalhada dos Bens (Opcional)</label>
                                    <input type="text" id="bem_descricao" name="bem_descricao"
                                        placeholder="Ex: Telemóvel iPhone 13, Carteira com documentos"
                                        class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all">
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label
                                        class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Categoria
                                        do Bem <span class="text-red-500">*</span></label>
                                    <select id="bem_categoria" name="bem_categoria"
                                        class="w-full px-3.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm h-11 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-zinc-200 transition-all">
                                        <option value="" disabled selected>Selecione uma categoria...</option>
                                        <option value="eletronicos">Eletrónicos e Digital</option>
                                        <option value="valores">Valores Monetários</option>
                                        <option value="documentos">Documentação</option>
                                        <option value="armamento">Armamento e Munições</option>
                                        <option value="veiculos">Veículos e Transportes</option>
                                        <option value="pessoais">Bens Pessoais / Outros</option>
                                        <option value="ilicitos">Substâncias / Ilícitos</option>
                                        <option value="outros">Outros</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2 flex flex-col gap-1.5">
                                    <label
                                        class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Observações
                                        de Recebimento</label>
                                    <textarea id="bem_observacoes" name="bem_observacoes" rows="3"
                                        placeholder="Insira anotações sobre o estado de conservação dos bens..."
                                        class="w-full px-3.5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm h-24 resize-none focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-zinc-800 dark:text-white transition-all"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LINHA DE AÇÃO DE NAVEGAÇÃO ENTRE ABAS -->
                    <div
                        class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-800 flex justify-between items-center shrink-0">
                        <button type="button" id="btnAnterior" onclick="mudarAba(-1)"
                            class="px-5 py-2.5 border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-zinc-900 dark:hover:text-white text-xs font-bold uppercase tracking-wider rounded-xl cursor-pointer disabled:opacity-0 disabled:pointer-events-none transition-all">
                            Voltar
                        </button>

                        <div class="flex gap-2">
                            <button type="button" id="closeModalBtn" onclick="fecharModalCadastro()"
                                class="px-4 py-2.5 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 text-xs font-bold uppercase tracking-wider cursor-pointer">
                                Fechar
                            </button>
                            <button type="button" id="btnProximo" onclick="mudarAba(1)"
                                class="px-6 py-2.5 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 text-xs font-black uppercase tracking-wider rounded-xl cursor-pointer hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-all">
                                Avançar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- ======================================================= -->
    <!-- SUPORTE JS -->
    <!-- ======================================================= -->
    <script>
        let abaAtual = 0;
        const abas = ['abaBiometria', 'abaDadosPessoais', 'abaAntecedentes', 'abaConclusao'];

        // Função que calcula a idade exacta e determina se é Menor ou Adulto
        function calcularIdadeEstatuto(dataNascimento) {
            if (!dataNascimento) return { idadeStr: '', estatuto: '', eMenor: false };

            const hoje = new Date();
            const nascimento = new Date(dataNascimento);

            let idade = hoje.getFullYear() - nascimento.getFullYear();
            const m = hoje.getMonth() - nascimento.getMonth();

            if (m < 0 || (m === 99 && hoje.getDate() < nascimento.getDate())) {
                idade--;
            }

            const eMenor = idade < 18;
            const estatuto = eMenor ? 'Menor' : 'Adulto';
            const idadeStr = `${idade} anos`;

            return { idadeStr, estatuto, eMenor };
        }

        // Função que aplica os valores nos inputs e no elemento visual
        function processarCalculoIdade(dataNascimento) {
            const inputIdade = document.getElementById('idade');
            const badgeEstatuto = document.getElementById('badge-estatuto');

            if (!inputIdade) return;

            const { idadeStr, estatuto, eMenor } = calcularIdadeEstatuto(dataNascimento);

            if (dataNascimento) {
                inputIdade.value = idadeStr;

                if (badgeEstatuto) {
                    badgeEstatuto.classList.remove('hidden');
                    badgeEstatuto.textContent = estatuto.toUpperCase();

                    if (eMenor) {
                        badgeEstatuto.className = 'px-2 py-0.5 text-[10px] font-bold rounded-md bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400';
                    } else {
                        badgeEstatuto.className = 'px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400';
                    }
                }
            } else {
                inputIdade.value = '';
                if (badgeEstatuto) badgeEstatuto.classList.add('hidden');
            }
        }

        // Evento disparado em tempo real sempre que o utilizador altera a data de nascimento manualmente
        document.getElementById('data_nascimento')?.addEventListener('change', function () {
            processarCalculoIdade(this.value);
        });

        // Função unificada para abrir o modal (Novo Registo ou Edição com Verificação)
        function abrirModalCadastro(id = null) {
            // Se for um novo registo (sem ID), abre o modal diretamente com os campos limpos
            if (!id) {
                prepararNovoRegisto();
                return;
            }

            // 1. Abre a espécie de modal de requisição (Loading do SweetAlert2)
            Swal.fire({
                title: 'A processar requisição...',
                text: 'A consultar os registos do arguido...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // 2. Faz a requisição ao servidor
            fetch(`../controller/arguido/obter_arguido.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    // Fecha o loading de requisição
                    Swal.close();

                    if (data.sucesso) {
                        // 3. Caso encontre: Preenche os dados e abre o modal de cadastro
                        preencherFormularioComDados(data);

                        const modal = document.getElementById('modalCadastroArguido');
                        if (modal) {
                            modal.classList.remove('hidden');
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        } else {
                            console.error("Elemento #modalCadastroArguido não encontrado no DOM.");
                        }
                    } else {
                        // 4. Caso não encontre registo: Notifica com erro
                        Swal.fire({
                            icon: 'error',
                            title: 'Registo não encontrado',
                            text: data.mensagem || 'Não foi encontrado nenhum arguido associado a este identificador.',
                            confirmButtonColor: '#18181b'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Erro na requisição:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro de Comunicação',
                        text: 'Ocorreu um erro ao tentar comunicar com o servidor.',
                        confirmButtonColor: '#18181b'
                    });
                });
        }

        // Função auxiliar para preparar o formulário para um Novo Registo
        function prepararNovoRegisto() {
            const modal = document.getElementById('modalCadastroArguido');
            if (!modal) return;

            // Reseta para a primeira aba
            abaAtual = 0;
            abas.forEach((abaId, index) => {
                const elemento = document.getElementById(abaId);
                if (elemento) {
                    if (index === 0) elemento.classList.remove('hidden');
                    else elemento.classList.add('hidden');
                }
            });

            // Limpa todos os inputs do formulário se necessário (opcional)
            // document.getElementById('seuFormularioId').reset();
            let inputIdHidden = document.getElementById('arguido_id');
            if (inputIdHidden) inputIdHidden.value = '';

            atualizarBotoesNavegacao();
            modal.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Função auxiliar segura para evitar erros caso algum ID não exista no HTML
        function setValorCampo(id, valor) {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.value = (valor !== null && valor !== undefined) ? valor : '';
            }
        }

        // Função para carregar a imagem existente no canvas do modo de edição
        function setImagemCanvas(caminho) {
            const canvas = document.getElementById('canvas');
            const placeholder = document.getElementById('canvasPlaceholder');

            if (!canvas || !placeholder) return;

            if (caminho && caminho.trim() !== '') {
                let caminhoFoto = caminho;

                // Aplica a mesma regra de caminhos robusta que já utiliza no projeto
                if (!caminhoFoto.startsWith('http') && !caminhoFoto.startsWith('data:') && !caminhoFoto.startsWith('/') && !caminhoFoto.startsWith('../')) {
                    let nomeFicheiro = caminhoFoto.replace(/^uploads\//, '');
                    caminhoFoto = '../controller/arguidouploads/' + nomeFicheiro;
                }

                const ctx = canvas.getContext('2d');
                const img = new Image();

                img.crossOrigin = 'anonymous';
                img.onload = function () {
                    // Ajusta o tamanho real do canvas para corresponder à imagem e evitar distorções
                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;

                    // Desenha a imagem dentro do canvas
                    ctx.drawImage(img, 0, 0);

                    // Mostra o canvas e esconde o texto de placeholder
                    canvas.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };

                img.onerror = function () {
                    console.error("Erro ao carregar a imagem para o canvas:", caminhoFoto);
                    canvas.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                    placeholder.textContent = 'Erro ao carregar foto';
                };

                img.src = caminhoFoto;
            } else {
                // Se não houver foto, esconde o canvas e mostra o placeholder
                canvas.classList.add('hidden');
                placeholder.classList.remove('hidden');
                placeholder.textContent = 'Aguardando...';
            }
        }

        function preencherFormularioComDados(data) {
            abaAtual = 0;
            abas.forEach((abaId, index) => {
                const elemento = document.getElementById(abaId);
                if (elemento) {
                    if (index === 99) elemento.classList.remove('hidden');
                    else elemento.classList.add('hidden');
                }
            });

            if (typeof mudarParaIndiceAba === 'function') {
                mudarParaIndiceAba(0);
            }

            // --- ABA 01: Biometria / Canvas ---
            setImagemCanvas(data.biometria_face);

            // --- Identificadores Ocultos e Biometria ---
            setValorCampo('arguido_id', data.id);              // ID do registo do arguido
            setValorCampo('n_processo', data.processo_id);     // ID do processo (aponta para o ID real do seu HTML)
            setValorCampo('biometria_face', data.biometria_face); // Caminho da foto em string para o PHP guardar

            // --- Restantes campos ---
            setValorCampo('nome', data.nome);
            setValorCampo('alcunha', data.alcunha);
            setValorCampo('data_nascimento', data.data_nascimento);
            setValorCampo('bi', data.bi);
            setValorCampo('nacionalidade', data.nacionalidade);
            setValorCampo('naturalidade', data.naturalidade);
            setValorCampo('estado_civil', data.estado_civil);
            setValorCampo('profissao', data.profissao);

            // --- Tratamento da Filiação ---
            let filiacaoTexto = data.filiacao || '';
            let nomePai = '';
            let nomeMae = '';

            if (filiacaoTexto.includes('Pai: ') || filiacaoTexto.includes('Mãe: ')) {
                let matchPai = filiacaoTexto.match(/Pai:\s*([^|]+)/i);
                if (matchPai) nomePai = matchPai[1].trim();

                let matchMae = filiacaoTexto.match(/Mãe:\s*(.+)/i);
                if (matchMae) nomeMae = matchMae[1].trim();
            } else {
                nomePai = filiacaoTexto;
            }
            setValorCampo('nome_pai', nomePai);
            setValorCampo('nome_mae', nomeMae);

            // --- Mapeamento correto das chaves do teu JSON ---
            setValorCampo('telefone', data.contacto);
            setValorCampo('residencia', data.morada);

            setValorCampo('tipo_crime', data.tipo_crime);
            setValorCampo('reincidente', data.reincidente || 'Não');
            setValorCampo('estabelecimento_prisional', data.estabelecimento_prisional);
            setValorCampo('tempo_cumprido', data.tempo_cumprido);

            setValorCampo('tipo_medida', data.tipo_medida);
            setValorCampo('prazo_unidade', data.prazo_unidade);
            setValorCampo('prazo_quantidade', data.prazo_quantidade);
            setValorCampo('data_inicio', data.data_inicio ? data.data_inicio.replace(' ', 'T') : '');
            setValorCampo('observacoes_detencao', data.observacoes_detencao);

            if (data.bens && data.bens.length > 0) {
                setValorCampo('bem_descricao', data.bens[0].descricao);
                setValorCampo('bem_categoria', data.bens[0].categoria);
                setValorCampo('bem_observacoes', data.bens[0].observacoes);
            } else {
                setValorCampo('bem_descricao', '');
                setValorCampo('bem_categoria', '');
                setValorCampo('bem_observacoes', '');
            }

            // Dentro da função preencherFormularioComDados(data):
            setValorCampo('data_nascimento', data.data_nascimento);

            // Dispara o cálculo automático da idade e verificação de menor/adulto
            processarCalculoIdade(data.data_nascimento);

            atualizarBotoesNavegacao();
        }

        function fecharModalCadastro() {
            const modal = document.getElementById('modalCadastroArguido');
            if (modal) {
                modal.classList.add('hidden');
            }

            // 1. Limpa o formulário de forma nativa caso exista a tag <form>
            const form = document.querySelector('form'); // ou use o ID do seu form ex: document.getElementById('seuFormId')
            if (form) {
                form.reset();
            }

            // 2. Limpa explicitamente cada campo de todas as abas (para garantir que nada fica preso)
            const camposParaLimpar = [
                'arguido_id', 'n_processo', 'biometria_face', 'biometria_digital',
                'nome', 'alcunha', 'data_nascimento', 'bi', 'nacionalidade', 'naturalidade',
                'estado_civil', 'filiacao', 'contacto', 'morada',
                'tipo_crime', 'reincidente', 'estabelecimento_prisional', 'tempo_cumprido',
                'tipo_medida', 'prazo_unidade', 'prazo_quantidade', 'data_inicio', 'observacoes_detencao',
                'bem_descricao', 'bem_categoria', 'bem_observacoes'
            ];

            camposParaLimpar.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.value = '';
                    // Se usar selects ou componentes especiais, remove seleções extra se necessário
                }
            });

            // 3. Reseta o Canvas e o Placeholder da foto (Aba 01)
            const canvas = document.getElementById('canvas');
            const canvasPlaceholder = document.getElementById('canvasPlaceholder');
            if (canvas && canvasPlaceholder) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                canvas.classList.add('hidden');
                canvasPlaceholder.classList.remove('hidden');
                canvasPlaceholder.textContent = 'Aguardando...';
            }

            // 4. Retorna a navegação para a primeira aba (Biometria) e reseta o topo
            abaAtual = 0;
            if (typeof atualizarEstiloAbasTopo === 'function') {
                atualizarEstiloAbasTopo();
            }
            if (typeof atualizarBotoesNavegacao === 'function') {
                atualizarBotoesNavegacao();
            }
        }

        function validarEtapaAtual(etapa) {
            switch (etapa) {
                case 0:
                    return true;
                case 1:
                    const nome = document.getElementById('nome').value.trim();
                    const dataNascimento = document.getElementById('data_nascimento').value;
                    const bi = document.getElementById('bi').value.trim();

                    if (!nome || !dataNascimento || !bi) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Campos Obrigatórios',
                            text: 'Por favor, preencha todos os campos obrigatórios de Identidade Civil (*).',
                            confirmButtonColor: '#18181b'
                        });
                        return false;
                    }
                    return true;
                case 2:
                case 3:
                    return true;
                default:
                    return true;
            }
        }

        function enviarFormulario() {
            if (!validarEtapaAtual(abaAtual)) return;

            // VERIFICAÇÃO NA CONSOLA: Veja se aparece um número válido (ex: 1, 5, 12) ou se está vazio/undefined
            const processoIdDebug = document.getElementById('n_processo').value;
            console.log("DEBUG - ID do Processo a enviar:", processoIdDebug);

            if (!processoIdDebug) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro Crítico',
                    text: 'O ID do processo está vazio. Certifique-se de que abriu o modal a partir de um processo válido.'
                });
                return;
            }

            const form = document.getElementById('cadastroForm');
            const formData = new FormData(form);

            if (canvas && !canvas.classList.contains('hidden')) {
                formData.append('biometria_face', canvas.toDataURL('image/png'));
            }

            Swal.fire({
                title: 'A guardar registo...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch('../controller/arguido/salvar_arguido.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: data.mensagem
                        }).then(() => { location.reload(); });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Atenção',
                            text: data.mensagem // Aqui agora vai aparecer a mensagem exata do PHP se o ID falhar
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição:', error);
                });
        }

        // --- Biometria Facial ---
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const canvasPlaceholder = document.getElementById('canvasPlaceholder');

        async function iniciarCamera() {
            if (!video) return;
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } });
                video.srcObject = stream;
            } catch (error) {
                console.error("Erro ao aceder à câmara:", error);
            }
        }

        function capturarFace() {
            if (!video || !canvas) return;

            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;

            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.classList.remove('hidden');
            if (canvasPlaceholder) canvasPlaceholder.classList.add('hidden');
        }

        // --- Simulação de Impressão Digital (AFIS) ---
        function simularLeituraDigital() {
            const badgeStatus = document.getElementById('badgeDigitalStatus');
            const textoLeitor = document.getElementById('textoLeitor');
            const laserScan = document.getElementById('laserScan');

            if (badgeStatus) {
                badgeStatus.textContent = 'A processar leitura...';
                badgeStatus.className = 'inline-block px-2.5 py-1 text-xs font-bold bg-amber-500/10 text-amber-500 rounded-md uppercase';
            }

            if (laserScan) laserScan.classList.remove('hidden');

            setTimeout(() => {
                if (laserScan) laserScan.classList.add('hidden');
                if (badgeStatus) {
                    badgeStatus.textContent = 'Hardware Conectado (AFIS OK)';
                    badgeStatus.className = 'inline-block px-2.5 py-1 text-xs font-bold bg-emerald-500/10 text-emerald-500 rounded-md uppercase';
                }
                if (textoLeitor) textoLeitor.textContent = 'Capturado';
            }, 2000);
        }

        // Inicialização automática ao carregar a página/modal
        window.addEventListener('DOMContentLoaded', () => {
            iniciarCamera();
            atualizarBotoesNavegacao();
        });

        // 1. Função chamada pelos botões do topo (<nav id="tabNav">)
        function irParaAba(indice) {
            // Se tentar avançar pulando etapas, valida a etapa atual primeiro
            if (indice > abaAtual) {
                for (let i = abaAtual; i < indice; i++) {
                    if (!validarEtapaAtual(i)) return;
                }
            }
            mudarParaIndiceAba(indice);
        }

        // 2. Função chamada pelos botões inferiores ("Próximo" / "Anterior")
        function mudarAba(direcao) {
            let novoIndice = abaAtual + direcao;

            // Se estiver a avançar, valida a etapa atual
            if (direcao > 1 || (direcao === 1 && !validarEtapaAtual(abaAtual))) {
                if (direcao > 1) { /* Permitir salto direto se necessário */ }
                else return;
            }

            if (novoIndice < 0) novoIndice = 0;
            if (novoIndice >= abas.length) novoIndice = abas.length - 1;

            mudarParaIndiceAba(novoIndice);
        }

        // 3. Motor central que efetivamente troca a aba visível e atualiza o topo e o fundo
        function mudarParaIndiceAba(indice) {
            // Esconde a aba atual
            const abaAtualElement = document.getElementById(abas[abaAtual]);
            if (abaAtualElement) {
                abaAtualElement.classList.add('hidden');
            }

            // Atualiza o índice global
            abaAtual = indice;

            // Mostra a nova aba selecionada
            const novaAbaElement = document.getElementById(abas[abaAtual]);
            if (novaAbaElement) {
                novaAbaElement.classList.remove('hidden');
            }

            // Atualiza o estilo visual dos botões do topo e os botões inferiores
            atualizarEstiloAbasTopo();
            atualizarBotoesNavegacao();

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // 4. Atualiza o visual do menu superior (destaca a aba ativa e apaga as inativas)
        function atualizarEstiloAbasTopo() {
            const botoes = document.querySelectorAll('#tabNav .tab-btn');
            botoes.forEach((btn, index) => {
                const badge = btn.querySelector('.tab-badge');
                if (index === abaAtual) {
                    // Aba Ativa
                    btn.className = "tab-btn pb-3 px-3 text-xs font-black uppercase text-zinc-900 dark:text-white border-b-2 border-zinc-900 dark:border-white flex items-center gap-2 whitespace-nowrap transition-all cursor-pointer";
                    if (badge) {
                        badge.className = "tab-badge w-5 h-5 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-[10px] flex items-center justify-center font-bold";
                    }
                } else {
                    // Aba Inativa
                    btn.className = "tab-btn pb-3 px-3 text-xs font-semibold uppercase text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 border-b-2 border-transparent flex items-center gap-2 whitespace-nowrap transition-all cursor-pointer";
                    if (badge) {
                        badge.className = "tab-badge w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[10px] flex items-center justify-center font-bold";
                    }
                }
            });
        }

        // 5. Atualiza os botões inferiores e valida se é Edição ou Novo Registo
        function atualizarBotoesNavegacao() {
            const btnAnterior = document.getElementById('btnAnterior');
            const btnProximo = document.getElementById('btnProximo');

            // 1. Verifica se estamos em modo de edição olhando para o ID do arguido
            const arguidoIdInput = document.getElementById('n_processo');
            const isEdicao = arguidoIdInput && arguidoIdInput.value.trim() !== '';

            if (btnAnterior) {
                btnAnterior.disabled = abaAtual === 99;
                btnAnterior.style.opacity = abaAtual === 99 ? '0.5' : '1';
            }

            if (btnProximo) {
                if (abaAtual === abas.length - 1) {
                    // Se estiver na última aba, define o texto com base em ser edição ou novo registo
                    if (isEdicao) {
                        btnProximo.textContent = 'Atualizar Registo';
                    } else {
                        btnProximo.textContent = 'Concluir Registo';
                    }

                    // Opcional: Se o processo estiver vazio, pode desativar o botão ou deixá-lo alertar ao clicar
                    btnProximo.setAttribute('onclick', 'enviarFormulario()');
                } else {
                    btnProximo.textContent = 'Próximo';
                    btnProximo.setAttribute('onclick', 'mudarAba(1)');
                }
            }
        }
    </script>

    <!-- ======================================================= -->
    <!-- MODAL 1: NOVO DEPÓSITO (CADASTRO / EDIÇÃO) -->
    <!-- ======================================================= -->
    <div id="depositoModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white w-full max-w-lg rounded-[32px] border border-gray-100 shadow-2xl overflow-hidden flex flex-col transform transition-all scale-95 opacity-0 duration-300"
            id="depositoContainer">
            <!-- Cabeçalho -->
            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-black text-gray-800">Depositar Versão / Documento</h3>
                    <p class="text-xs text-gray-500 font-semibold mt-0.5">Faça a submissão formal de arquivos para
                        avaliação oficial.</p>
                </div>
                <button onclick="closeModal('depositoModal', 'depositoContainer')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Formulário -->
            <form id="depositoForm" onsubmit="handleDepositoSubmit(event)" class="p-6 space-y-4">
                <input type="hidden" name="id" id="depositoId">

                <!-- Tipo de Entrega -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Etapa / Tipo do
                        Depósito *</label>
                    <select name="tipo" required
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm font-semibold text-gray-800 bg-white focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition">
                        <option value="Relatório Parcial">Relatório Parcial de Pesquisa</option>
                        <option value="Versão de Qualificação">Versão de Qualificação</option>
                        <option value="Pré-Defesa">Entrega Pré-Defesa (Banca)</option>
                        <option value="Versão Final">Versão Final Homologada</option>
                    </select>
                </div>

                <!-- Upload de Arquivos -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Ficheiro TCC
                        (Apenas PDF) *</label>
                    <div
                        class="border-2 border-dashed border-gray-200 hover:border-indigo-500 rounded-2xl p-6 text-center cursor-pointer transition bg-gray-50/50 group">
                        <input type="file" id="fileInput" required accept=".pdf" class="hidden"
                            onchange="updateFileName(this)">
                        <label for="fileInput" class="cursor-pointer space-y-2 block">
                            <div class="text-2xl text-gray-400 group-hover:text-indigo-500 transition"><i
                                    class="fa-solid fa-cloud-arrow-up"></i></div>
                            <p class="text-xs font-bold text-gray-700" id="fileNameText">Clique para selecionar ou
                                arraste o ficheiro</p>
                            <p class="text-[10px] text-gray-500 font-semibold">Tamanho limite: 50MB (formato .pdf)</p>
                        </label>
                    </div>
                </div>

                <!-- Observações -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Observações para
                        o Orientador</label>
                    <textarea name="notas" rows="3"
                        placeholder="Insira uma nota para o orientador caso tenha feito alterações importantes..."
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm font-semibold text-gray-800 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition resize-none"></textarea>
                </div>

                <!-- Rodapé / Ações -->
                <div class="pt-4 border-t border-gray-50 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModal('depositoModal', 'depositoContainer')"
                        class="px-5 py-3 rounded-2xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition">Cancelar</button>
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold text-xs transition shadow-lg shadow-indigo-100">Submeter
                        Documento</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- MODAL 2: PARECER TÉCNICO (ADICIONAR / VISUALIZAR PARECER) -->
    <!-- ======================================================= -->
    <div id="parecerModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white w-full max-w-xl rounded-[32px] border border-gray-100 shadow-2xl overflow-hidden flex flex-col transform transition-all scale-95 opacity-0 duration-300"
            id="parecerContainer">
            <!-- Cabeçalho -->
            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-emerald-50/30">
                <div>
                    <h3 class="text-lg font-black text-gray-800" id="parecerTitle">Avaliação e Emissão de Parecer</h3>
                    <p class="text-xs text-gray-500 font-semibold mt-0.5">Analise a entrega e registe as correções
                        académicas oficiais.</p>
                </div>
                <button onclick="closeModal('parecerModal', 'parecerContainer')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Formulário -->
            <form id="parecerForm" onsubmit="handleParecerSubmit(event)" class="p-6 space-y-4">
                <input type="hidden" name="id" id="parecerId">

                <!-- Dados do Arquivo Submetido pelo Aluno -->
                <div class="bg-gray-50 border border-gray-100 p-4 rounded-2xl flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div
                            class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-base flex-shrink-0">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-wider">Documento
                                do Aluno</span>
                            <span class="text-xs font-bold text-gray-800 truncate block"
                                id="parecerFileName">documento_estudante.pdf</span>
                        </div>
                    </div>
                    <button type="button" onclick="alert('Descarregando arquivo para revisão...')"
                        class="flex items-center gap-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-4 py-2.5 rounded-xl transition text-xs font-bold flex-shrink-0">
                        <i class="fa-solid fa-download"></i> Descarregar
                    </button>
                </div>

                <!-- Estado e Nota -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Definição do
                            Estado *</label>
                        <select name="status" id="parecerStatus" required
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm font-semibold text-gray-800 bg-white focus:outline-none focus:border-indigo-500 transition">
                            <option value="Homologado">Aprovado (Homologado)</option>
                            <option value="Ajustes">Solicitar Ajustes/Correções</option>
                            <option value="Em Revisão">Manter Sob Revisão</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label
                            class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Nota/Pontuação
                            (Opcional)</label>
                        <input type="number" name="nota" id="parecerNota" min="0" max="20" placeholder="Ex: 18"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm font-semibold text-gray-800 focus:outline-none focus:border-indigo-500 transition">
                    </div>
                </div>

                <!-- Texto do Parecer -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Parecer Detalhado
                        / Observações de Correção *</label>
                    <textarea name="parecer" id="parecerTexto" required rows="4"
                        placeholder="Escreva de forma clara os pontos fortes, as correções obrigatórias ou o parecer de homologação..."
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm font-semibold text-gray-800 placeholder-gray-400 focus:outline-none focus:border-indigo-500 transition resize-none"></textarea>
                </div>

                <!-- Rodapé / Ações -->
                <div class="pt-4 border-t border-gray-50 flex items-center justify-end gap-3" id="parecerFooter">
                    <button type="button" onclick="closeModal('parecerModal', 'parecerContainer')"
                        class="px-5 py-3 rounded-2xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition">Cancelar</button>
                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-bold text-xs transition shadow-lg shadow-emerald-100">Salvar
                        Avaliação</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- SCRIPTS DE CONTROLO (Funções para abrir/fechar os modais) -->
    <!-- ======================================================= -->
    <script>
        // Abre qualquer um dos modais aplicando animação de Fade-In e Zoom
        function openModal(modalId, containerId) {
            const modal = document.getElementById(modalId);
            const container = document.getElementById(containerId);

            modal.classList.remove('hidden');
            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        // Fecha os modais aplicando animação reversa
        function closeModal(modalId, containerId) {
            const modal = document.getElementById(modalId);
            const container = document.getElementById(containerId);

            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 250);
        }

        // Atualiza o texto dinâmico da área de upload quando um arquivo é anexado
        function updateFileName(input) {
            if (input.files && input.files[0]) {
                document.getElementById('fileNameText').innerText = `Ficheiro: ${input.files[0].name}`;
            }
        }

        // Abre o modal de parecer adaptando-se caso seja apenas para Visualizar (viewOnly = true) ou Adicionar (viewOnly = false)
        function openParecerModal(id, viewOnly = false) {
            const fileText = document.getElementById('parecerFileName');
            const statusSelect = document.getElementById('parecerStatus');
            const notaInput = document.getElementById('parecerNota');
            const parecerTexto = document.getElementById('parecerTexto');
            const footer = document.getElementById('parecerFooter');
            const title = document.getElementById('parecerTitle');

            // Configuração de Leitura vs Edição
            if (viewOnly) {
                title.innerText = "Parecer Técnico Emitido";
                statusSelect.disabled = true;
                notaInput.disabled = true;
                parecerTexto.disabled = true;
                footer.classList.add('hidden');
            } else {
                title.innerText = "Avaliação e Emissão de Parecer";
                statusSelect.disabled = false;
                notaInput.disabled = false;
                parecerTexto.disabled = false;
                footer.classList.remove('hidden');

                // Limpa dados antigos para nova edição
                statusSelect.value = "Homologado";
                notaInput.value = "";
                parecerTexto.value = "";
            }

            openModal('parecerModal', 'parecerContainer');
        }

        // Handlers de Submissão dos Formulários
        function handleDepositoSubmit(event) {
            event.preventDefault();
            // Sua lógica Ajax / Fetch aqui
            closeModal('depositoModal', 'depositoContainer');
        }

        function handleParecerSubmit(event) {
            event.preventDefault();
            // Sua lógica Ajax / Fetch aqui
            closeModal('parecerModal', 'parecerContainer');
        }
    </script>

    <!-- MODAL LOGOUT -->
    <div id="modal-logout"
        class="hidden fixed inset-0 z-[100] items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
        <div class="bg-white w-full max-w-sm rounded-[32px] shadow-2xl overflow-hidden transform transition-all">
            <div class="bg-red-50 p-8 flex justify-center">
                <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-door-open"></i>
                </div>
            </div>
            <div class="p-8 text-center">
                <h3 class="text-xl font-bold text-gray-800">Terminar Sessão?</h3>
                <p class="text-gray-500 mt-2 text-sm leading-relaxed">Tens a certeza que desejas sair do NextGrade TCC?
                </p>
            </div>
            <div class="p-6 bg-gray-50 flex gap-3">
                <button onclick="fecharModalLogout()"
                    class="flex-1 px-4 py-3 text-gray-500 font-bold hover:bg-gray-200 rounded-2xl transition-all">Cancelar</button>
                <button onclick="executarSair()"
                    class="flex-[1.5] px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-2xl shadow-lg shadow-red-500/30 transition-all">Sim,
                    Sair</button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT GERAL DE CONTROLE -->
    <script>
        function abrirModalLogout() {
            const modal = document.getElementById('modal-logout');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function fecharModalLogout() {
            const modal = document.getElementById('modal-logout');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function executarSair() {
            window.location.href = 'index.php';
        }

        function toggleEstudanteMenu(event, menuId) {
            event.stopPropagation();
            const currentMenu = document.getElementById(menuId);
            document.querySelectorAll('[id^="menu-est-"]').forEach(menu => {
                if (menu.id !== menuId) menu.classList.add('hidden');
            });
            currentMenu.classList.toggle('hidden');
        }

        window.onclick = function () {
            document.querySelectorAll('[id^="menu-est-"]').forEach(menu => menu.classList.add('hidden'));
            document.getElementById('userMenu').classList.add('hidden');
        };

        const userBtn = document.getElementById('userBtn');
        if (userBtn) {
            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                document.getElementById('userMenu').classList.toggle('hidden');
            });
        }

        // Controle básico de Sidebar responsivo
        const openSidebarBtn = document.getElementById('openSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');

        if (openSidebarBtn && sidebar) {
            openSidebarBtn.addEventListener('click', () => sidebar.classList.remove('-translate-x-full'));
        }
        if (closeSidebarBtn && sidebar) {
            closeSidebarBtn.addEventListener('click', () => sidebar.classList.add('-translate-x-full'));
        }
    </script>
</body>

</html>