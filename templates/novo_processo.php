<?php
// painel.php ou index principal da aplicação
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

        .font-display {
            font-family: 'Zilla Slab', serif;
        }

        .font-mono {
            font-family: 'IBM Plex Mono', monospace;
        }

        /* Aba de pasta de processo — a marca registrada deste layout */
        .file-tab {
            position: relative;
        }

        .file-tab::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 20px;
            width: 64px;
            height: 10px;
            background: var(--ink);
            border-radius: 6px 6px 0 0;
            opacity: 0.9;
        }

        .perf-line {
            background-image: repeating-linear-gradient(to right, var(--line) 0, var(--line) 5px, transparent 5px, transparent 11px);
            height: 1px;
        }

        .status-chip {
            font-family: 'IBM Plex Mono', monospace;
            letter-spacing: 0.04em;
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
                            class="relative flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600/10 text-white transition-all">
                            <div class="absolute left-0 w-1 h-6 bg-blue-400 rounded-r-full"></div>
                            <i class="fa-solid fa-users-rectangle w-5 text-blue-400 transition-colors"></i>
                            <span class="text-sm font-medium text-blue-400">Entrada de processos</span>
                        </a>
                        <a href="cadastro_arguido.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:text-white hover:bg-white/5 transition-all group">
                            <i class="fa-solid fa-folder-open w-5 group-hover:text-blue-400 transition-colors"></i>
                            <span class="text-sm font-medium">cadastro de Arguidos</span>
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
            </script>

            <!-- ======================================================= -->
            <!-- ABA: GESTÃO DA BANCA EXAMINADORA                        -->
            <!-- ======================================================= -->
            <main class="p-6 max-w-[1600px] w-full mx-auto flex-1 overflow-y-auto animate-fade-in">

                <!-- 1. TOPO DA PÁGINA & MÉTRICAS RÁPIDAS -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
                    <div>
                        <h2 class="text-2xl font-black text-gray-800 tracking-tight">Processos em Instrução</h2>
                        <p class="text-sm font-medium text-gray-500 mt-1">Nomeie técnicos, insira o nome do arguido e
                            selecione o número de processo.</p>
                    </div>

                    <!-- Indicador de Bancas do Mês -->
                    <div
                        class="flex items-center gap-3 bg-indigo-50 border border-indigo-100/60 px-5 py-3 rounded-2xl self-start lg:self-auto">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-600 text-base font-black">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-indigo-700/80 uppercase tracking-wider">Entradas ao dia
                            </p>
                            <h3 id="contadorEntradasHoje" class="text-base font-black text-indigo-800">0 Processos</h3>
                        </div>
                    </div>
                </div>

                <!-- 2. BARRA DE FILTROS, BUSCA E AGENDAMENTO -->
                <div
                    class="bg-white p-4 rounded-3xl border border-gray-100 shadow-sm flex flex-col xl:flex-row xl:items-center justify-between gap-4 mb-6">

                    <!-- Container onde o JS vai renderizar os botões dos técnicos dinamicamente -->
                    <div id="containerFiltrosTecnicos" class="flex flex-wrap gap-2">
                        <!-- Carregado via API -->
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto">
                        <div class="relative w-full sm:w-72">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-gray-400 text-xs"></i>
                            <input id="inputBuscaGlobal" type="text" placeholder="Buscar por processo ou técnico..."
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 rounded-xl border border-gray-100 text-xs font-semibold text-gray-700 focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <?php if ($perfilId === 2 || $perfilId === 99): ?>
                            <button onclick="abrirModalNovoProcesso()"
                                class="w-full sm:w-auto flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl transition font-bold text-xs shadow-md shadow-indigo-100 cursor-pointer">
                                <i class="fa-solid fa-users-gear"></i> Novo processo
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 3. GRID DE CARDS DE DEPÓSITOS / PROCESSOS PENAIS -->
                <div id="gridProcessos" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Os cards de processos serão injetados aqui pelo JavaScript -->
                </div>

                <!-- 4. PAGINAÇÃO ESTILO IPHONE (iOS FLUTUANTE) -->
                <div class="mt-8 flex items-center justify-center">
                    <nav id="containerPaginacao" aria-label="Navegação de Páginas"
                        class="inline-flex items-center gap-1.5 p-1.5 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 rounded-full shadow-xl shadow-zinc-950/5 transition-all">
                        <!-- Paginação gerada dinamicamente pelo JavaScript -->
                    </nav>
                </div>
            </main>

            <script>
                let paginaAtual = 1;
                let tecnicoSelecionadoId = '';
                let termoBusca = '';

                document.addEventListener('DOMContentLoaded', () => {
                    carregarDadosPainel();

                    // Evento de pesquisa em tempo real com debounce
                    const inputBusca = document.getElementById('inputBuscaGlobal');
                    if (inputBusca) {
                        let timerBusca;
                        inputBusca.addEventListener('input', (e) => {
                            clearTimeout(timerBusca);
                            termoBusca = e.target.value.trim();
                            paginaAtual = 1; // Reseta para a primeira página ao pesquisar
                            timerBusca = setTimeout(() => {
                                carregarDadosPainel();
                            }, 300);
                        });
                    }
                });

                async function carregarDadosPainel() {
                    try {
                        let url = `../controller/processos/listar_processos.php?pagina=${paginaAtual}`;
                        if (tecnicoSelecionadoId !== '') {
                            url += `&tecnico_id=${tecnicoSelecionadoId}`;
                        }
                        if (termoBusca !== '') {
                            url += `&busca=${encodeURIComponent(termoBusca)}`;
                        }

                        const response = await fetch(url);
                        const res = await response.json();

                        if (res.success) {
                            // 1. Atualizar contadores de entradas ao dia
                            const spanEntradas = document.getElementById('contadorEntradasHoje');
                            if (spanEntradas) {
                                spanEntradas.textContent = `${res.entradas_hoje} Processos`;
                            }

                            // 2. Renderizar botões de filtro de técnicos
                            renderizarFiltrosTecnicos(res.tecnicos_filtros);

                            // 3. Renderizar o Grid de Processos
                            renderizarGridProcessos(res.processos);

                            // 4. Renderizar a Paginação
                            renderizarPaginacao(res.paginacao);
                        } else {
                            console.error("Erro na API:", res.message);
                        }
                    } catch (error) {
                        console.error("Erro de rede ao buscar dados:", error);
                    }
                }

                function renderizarFiltrosTecnicos(tecnicos) {
                    const container = document.getElementById('containerFiltrosTecnicos');
                    if (!container) return;

                    let html = `
                        <button onclick="filtrarPorTecnico('')" 
                            class="${tecnicoSelecionadoId === '' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-gray-50 hover:bg-gray-100 text-gray-600'} px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                            Todos
                        </button>
                    `;

                    tecnicos.forEach(tec => {
                        const ativo = tecnicoSelecionadoId === tec.id.toString();
                        const classeBtn = ativo
                            ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100'
                            : 'bg-gray-50 hover:bg-gray-100 text-gray-600';

                        html += `
                            <button onclick="filtrarPorTecnico('${tec.id}')" 
                                class="${classeBtn} px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                                ${tec.nome} (${tec.total_processos})
                            </button>
                        `;
                    });

                    container.innerHTML = html;
                }

                function filtrarPorTecnico(idTecnico) {
                    tecnicoSelecionadoId = idTecnico;
                    paginaAtual = 1;
                    carregarDadosPainel();
                }

                function renderizarGridProcessos(processos) {
                    const grid = document.getElementById('gridProcessos');
                    if (!grid) return;

                    // 🔴 Correção 1: Corrigido de 99 para 0 (se a lista estiver vazia)
                    if (!processos || processos.length === 0) {
                        grid.innerHTML = `
                            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-gray-100">
                                <i class="fa-solid fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                <p class="text-sm font-bold text-gray-500">Nenhum processo encontrado.</p>
                            </div>
                        `;
                        return;
                    }

                    let html = '';
                    processos.forEach(p => {
                        // Formatar data amigável
                        let dataFormatada = 'Recentemente';
                        if (p.criado_em) {
                            const dataObj = new Date(p.criado_em);
                            dataFormatada = dataObj.toLocaleDateString('pt-PT', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
                        }

                        // Recuperar variáveis do PHP de forma segura para o JS
                        const perfilUtilizador = <?php echo isset($perfilId) ? intval($perfilId) : 0; ?>;
                        const userIdUtilizador = <?php echo isset($userId) ? intval($userId) : 0; ?>;

                        // 🔴 Correção 2: Validação limpa em JS (Permite perfil 2, perfil 99 ou Equipa Técnica -999)
                        let botaoApagarHtml = '';
                        if (perfilUtilizador === 2 || perfilUtilizador === 99 || userIdUtilizador === -999) {
                            botaoApagarHtml = `
                                <button onclick="apagarProcesso(${p.id}, '${escapeHtml(p.num_processo)}')" title="Apagar"
                                    class="w-9 h-9 flex items-center justify-center bg-red-500 hover:bg-red-600 rounded-xl transition cursor-pointer text-white">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            `;
                        }

                        html += `
                            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-xl hover:shadow-indigo-50/40 transition-all flex flex-col justify-between relative group">
                                <div>
                                    <div class="flex items-start justify-between gap-4 mb-4">
                                        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-xl flex-shrink-0">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </div>
                                        <span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-xl text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                                            <i class="fa-solid fa-clock mr-1"></i> ${p.estado_processo || 'Pendente'}
                                        </span>
                                    </div>

                                    <div class="space-y-2 mb-5">
                                        <div class="flex items-center gap-2">
                                            <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider">
                                                Auto de entrada
                                            </span>
                                            <span class="text-[11px] text-gray-400 font-medium">${dataFormatada}</span>
                                        </div>
                                        <div class="text-sm font-bold text-gray-800">Arguido: ${escapeHtml(p.nome_arguido)}</div>
                                        <h4 class="text-sm font-black text-gray-800 line-clamp-2 leading-snug">
                                            Processo Nº: ${escapeHtml(p.num_processo)}
                                        </h4>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                                    <span class="text-xs font-bold text-gray-500 truncate" title="${escapeHtml(p.nome_tecnico)}">
                                        <i class="fa-solid fa-user-tie text-gray-400 mr-1.5"></i>Técnico: ${escapeHtml(p.nome_tecnico || 'Não atribuído')}
                                    </span>
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <!-- BOTÃO FICHA -->
                                        <button onclick="abrirFichaProcesso(${p.id})" title="Ver Ficha do Processo"
                                            class="w-9 h-9 flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 rounded-xl transition cursor-pointer text-white shadow-sm shadow-indigo-100">
                                            <i class="fa-solid fa-file-lines text-sm"></i>
                                        </button>
                                        
                                        <!-- BOTÃO APAGAR CONDICIONAL -->
                                        ${botaoApagarHtml}
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    grid.innerHTML = html;
                }
                
                function renderizarPaginacao(pag) {
                    const navPaginacao = document.getElementById('containerPaginacao');
                    if (!navPaginacao) return;

                    if (pag.total_paginas <= 1) {
                        navPaginacao.innerHTML = '';
                        return;
                    }

                    let html = `
                        <button type="button" onclick="mudarPagina(${pag.pagina_atual - 1})" title="Página Anterior"
                            ${pag.pagina_atual <= 1 ? 'disabled' : ''}
                            class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100/80 transition-all active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                        <div class="w-px h-4 bg-zinc-200 my-auto"></div>
                        <div class="flex items-center gap-1 px-1">
                    `;

                    for (let i = 1; i <= pag.total_paginas; i++) {
                        if (i === 1 || i === pag.total_paginas || (i >= pag.pagina_atual - 1 && i <= pag.pagina_atual + 1)) {
                            const ativo = i === pag.pagina_atual;
                            const classeBtn = ativo
                                ? 'px-3.5 py-1.5 rounded-full text-xs font-black bg-zinc-950 text-white shadow-sm'
                                : 'px-3.5 py-1.5 rounded-full text-xs font-bold text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100/80';

                            html += `<button type="button" onclick="mudarPagina(${i})" class="${classeBtn} transition-all active:scale-95 cursor-pointer">${i}</button>`;
                        } else if (i === pag.pagina_atual - 2 || i === pag.pagina_atual + 2) {
                            html += `<span class="text-xs text-zinc-400 px-1 font-bold select-none">•••</span>`;
                        }
                    }

                    html += `
                        </div>
                        <div class="w-px h-4 bg-zinc-200 my-auto"></div>
                        <button type="button" onclick="mudarPagina(${pag.pagina_atual + 1})" title="Próxima Página"
                            ${pag.pagina_atual >= pag.total_paginas ? 'disabled' : ''}
                            class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100/80 transition-all active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    `;

                    navPaginacao.innerHTML = html;
                }

                function mudarPagina(novaPagina) {
                    paginaAtual = novaPagina;
                    carregarDadosPainel();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                // Função de segurança auxiliar contra XSS
                function escapeHtml(text) {
                    if (!text) return '';
                    return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                }

                // --- 1. AÇÃO DE ELIMINAR PROCESSO ---
                async function apagarProcesso(id, numProcesso) {
                    const isDark = document.documentElement.classList.contains('dark');

                    const result = await Swal.fire({
                        title: 'Tem a certeza?',
                        text: `Vai eliminar permanentemente o processo Nº ${numProcesso}. Esta ação não pode ser revertida!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#71717a',
                        confirmButtonText: 'Sim, eliminar!',
                        cancelButtonText: 'Cancelar',
                        background: isDark ? '#18181b' : '#ffffff',
                        color: isDark ? '#f4f4f5' : '#18181b'
                    });

                    if (result.isConfirmed) {
                        try {
                            const response = await fetch('../controller/processos/apagar_processo.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ id: id })
                            });
                            const res = await response.json();

                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false,
                                    background: isDark ? '#18181b' : '#ffffff',
                                    color: isDark ? '#f4f4f5' : '#18181b'
                                });
                                carregarDadosPainel(); // Atualiza a grid e os contadores dinamicamente
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Atenção',
                                    text: res.message,
                                    background: isDark ? '#18181b' : '#ffffff',
                                    color: isDark ? '#f4f4f5' : '#18181b'
                                });
                            }
                        } catch (error) {
                            console.error('Erro de rede:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro de Comunicação',
                                text: 'Não foi possível ligar ao servidor.',
                                background: isDark ? '#18181b' : '#ffffff',
                                color: isDark ? '#f4f4f5' : '#18181b'
                            });
                        }
                    }
                }

                // --- 2. AÇÃO DE VER FICHA DO PROCESSO (VIA SWEETALERT) ---
                async function abrirFichaProcesso(id) {
                    const isDark = document.documentElement.classList.contains('dark');

                    // Mostra indicador de carregamento
                    Swal.fire({
                        title: 'A carregar ficha...',
                        text: 'Por favor, aguarde.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                        background: isDark ? '#18181b' : '#ffffff',
                        color: isDark ? '#f4f4f5' : '#18181b'
                    });

                    try {
                        const response = await fetch(`../controller/processos/get_ficha_processo.php?id=${id}`);
                        const res = await response.json();

                        if (res.success) {
                            const p = res.processo;
                            const dataFormatada = p.criado_em ? new Date(p.criado_em).toLocaleString('pt-PT') : 'N/D';

                            // Estrutura HTML personalizada para a Ficha no SweetAlert
                            const htmlFicha = `
                                <div class="text-left space-y-4 text-xs font-medium ${isDark ? 'text-zinc-300' : 'text-gray-700'} max-h-[70vh] overflow-y-auto px-1">
                                    <div class="bg-indigo-50 dark:bg-indigo-950/40 p-4 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                                        <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block mb-1">Número do Processo</span>
                                        <h3 class="text-base font-black text-indigo-900 dark:text-indigo-200">${escapeHtml(p.num_processo)}</h3>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-gray-50 dark:bg-zinc-800/60 p-3 rounded-xl border border-gray-100 dark:border-zinc-800">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Arguido</span>
                                            <p class="font-bold text-sm ${isDark ? 'text-white' : 'text-gray-800'}">${escapeHtml(p.nome_arguido)}</p>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-zinc-800/60 p-3 rounded-xl border border-gray-100 dark:border-zinc-800">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Estado</span>
                                            <p class="font-bold text-amber-600 dark:text-amber-400">${escapeHtml(p.estado_processo || 'Pendente')}</p>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-zinc-800/60 p-3.5 rounded-xl border border-gray-100 dark:border-zinc-800 space-y-2">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Atribuição & Operacional</span>
                                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700/50 pb-2">
                                            <span class="text-gray-500">Técnico Responsável:</span>
                                            <span class="font-bold ${isDark ? 'text-white' : 'text-gray-800'}">${escapeHtml(p.nome_tecnico || 'Não atribuído')} ${p.nip_tecnico ? `(NIP: ${p.nip_tecnico})` : ''}</span>
                                        </div>
                                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700/50 pb-2">
                                            <span class="text-gray-500">Criado por:</span>
                                            <span class="font-bold ${isDark ? 'text-white' : 'text-gray-800'}">${escapeHtml(p.nome_criador || 'Sistema')}</span>
                                        </div>
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-gray-500">Data de Registo:</span>
                                            <span class="font-bold ${isDark ? 'text-zinc-300' : 'text-gray-700'}">${dataFormatada}</span>
                                        </div>
                                    </div>
                                </div>
                            `;

                            Swal.fire({
                                title: '<i class="fa-solid fa-file-lines text-indigo-600 mr-2"></i> Ficha do Processo',
                                html: htmlFicha,
                                width: '600px',
                                confirmButtonText: 'Fechar',
                                confirmButtonColor: '#4f46e5',
                                background: isDark ? '#18181b' : '#ffffff',
                                color: isDark ? '#f4f4f5' : '#18181b'
                            });

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Atenção',
                                text: res.message,
                                background: isDark ? '#18181b' : '#ffffff',
                                color: isDark ? '#f4f4f5' : '#18181b'
                            });
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro de Comunicação',
                            text: 'Não foi possível carregar os dados da ficha.',
                            background: isDark ? '#18181b' : '#ffffff',
                            color: isDark ? '#f4f4f5' : '#18181b'
                        });
                    }
                }
            </script>
        </div>
    </div>

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

    <!-- ======================================================= -->
    <!-- 1. MODAL DE CADASTRO / EDIÇÃO DA BANCA                  -->
    <!-- ======================================================= -->
    <div id="modalProcesso"
        class="hidden fixed inset-0 bg-zinc-950/60 dark:bg-black/80 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 z-50 animate-fade-in">

        <div
            class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl w-full max-w-xl max-h-[90vh] sm:max-h-[85vh] flex flex-col shadow-2xl overflow-hidden transform transition-all duration-300">

            <!-- Cabeçalho Fixo -->
            <div
                class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-white dark:bg-zinc-900 shrink-0">
                <div>
                    <h3 class="text-base font-black uppercase text-zinc-900 dark:text-white tracking-wide">
                        Atribuição de Processos
                    </h3>
                    <span
                        class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2.5 py-1 rounded-lg mt-1.5 w-fit font-medium">
                        <svg class="w-4 h-4 text-zinc-400 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Preencha os dados do processo e arguido
                    </span>
                </div>
                <!-- Botão de Fechar -->
                <button type="button" onclick="fecharModalOficial()"
                    class="p-2.5 -mr-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white rounded-xl cursor-pointer transition-colors hover:bg-zinc-100 dark:hover:bg-zinc-800"
                    aria-label="Fechar Modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Formulário Interno -->
            <form id="formAtribuicao" onsubmit="criarEAtribuir(event)"
                class="flex-1 p-6 space-y-5 text-left overflow-y-auto">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Nº do Processo -->
                    <div class="flex flex-col gap-1.5">
                        <label
                            class="block text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            Nº do Processo
                        </label>
                        <input type="text" name="numero_processo" id="inputNumeroProcesso" readonly required
                            class="w-full px-4 py-3 border border-zinc-200 dark:border-zinc-800 bg-zinc-100/70 dark:bg-zinc-950/60 text-zinc-600 dark:text-zinc-300 rounded-xl text-sm font-mono font-bold cursor-not-allowed focus:outline-none">
                    </div>

                    <!-- Nome do Arguido -->
                    <div class="flex flex-col gap-1.5">
                        <label
                            class="block text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            Nome do Arguido
                        </label>
                        <input type="text" name="nome_arguido" required placeholder="Ex: Nome Completo"
                            class="w-full px-4 py-3 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 focus:border-transparent focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Delegar Técnico -->
                <div class="flex flex-col gap-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        Delegar Técnico / Inspetor
                    </label>
                    <select name="tecnico_id" id="selectTecnicoDelegar" required
                        class="w-full px-4 py-3 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white rounded-xl text-sm cursor-pointer focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 focus:border-transparent focus:outline-none transition-all">
                        <option value="" disabled selected>A carregar técnicos disponíveis...</option>
                    </select>
                </div>

                <!-- Botão de Ação -->
                <button type="submit"
                    class="w-full py-3.5 mt-3 bg-zinc-950 dark:bg-white dark:text-zinc-950 text-white font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-all cursor-pointer shadow-md">
                    Criar e Delegar Processo
                </button>
            </form>
        </div>
    </div>

    <script>
        // Função para carregar o número de processo automático e os técnicos
        async function carregarDadosFormularioProcesso() {
            try {
                const response = await fetch('../controller/processos/get_dados_processo.php');
                const resultado = await response.json();

                if (resultado.success) {
                    // 1. Preenche o input do número do processo automático
                    const inputProcesso = document.getElementById('inputNumeroProcesso');
                    if (inputProcesso) {
                        inputProcesso.value = resultado.proximo_processo;
                    }

                    // 2. Preenche o select com os técnicos disponíveis
                    const selectTecnico = document.getElementById('selectTecnicoDelegar');
                    if (selectTecnico) {
                        selectTecnico.innerHTML = '<option value="" disabled selected>Selecione um técnico ou inspetor...</option>';

                        resultado.tecnicos.forEach(tec => {
                            const option = document.createElement('option');
                            option.value = tec.id;
                            option.textContent = `${tec.nome} (NIP: ${tec.nip})`;
                            selectTecnico.appendChild(option);
                        });
                    }
                } else {
                    console.error("Erro ao obter dados:", resultado.message);
                }
            } catch (error) {
                console.error("Erro de rede ao carregar formulário:", error);
            }
        }

        // Executa assim que o DOM estiver pronto (ou chame esta função ao abrir o modal)
        document.addEventListener('DOMContentLoaded', () => {
            carregarDadosFormularioProcesso();
        });
    </script>

    <!-- ======================================================= -->
    <!-- JAVASCRIPT MODAL                                        -->
    <!-- ======================================================= -->
    <script>
        // Lista simulada de técnicos/inspetores disponíveis no sistema
        const tecnicosDisponiveis = [
            { id: "1", nome: "Insp. Carlos Mendes - Brigada Homicídios" },
            { id: "2", nome: "Insp. Marcus Vinnicius - Crimes Económicos" },
            { id: "3", nome: "Dra. Sofia Castro - Perícia Forense" },
            { id: "4", nome: "Insp. André Ferreira - Narcóticos" }
        ];

        /**
         * Gera um número de processo automático no formato: AAAA/Nº-LETRA
         * Exemplo: 2026/3849-A
         */
        function gerarNumeroProcesso() {
            const anoAtual = new Date().getFullYear();
            const sequencial = Math.floor(1000 + Math.random() * 9000);
            const letras = ['A', 'B', 'C', 'D'];
            const letraSufixo = letras[Math.floor(Math.random() * letras.length)];

            return `${anoAtual}/${sequencial}-${letraSufixo}`;
        }

        /**
         * Abre o modal de Atribuição de Processos
         */
        function abrirModalNovoProcesso() {
            const modal = document.getElementById('modalProcesso');
            const inputNumero = document.getElementById('inputNumeroProcesso');

            if (!modal) return;

            // 1. Gera novo número de processo automático
            if (inputNumero) {
                inputNumero.value = gerarNumeroProcesso();
            }

            // 3. Exibe o modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Evita rolagem da página ao fundo

            // 2. Chama a função para buscar o próximo número e os técnicos frescos da base de dados
            carregarDadosFormularioProcesso();
        }

        /**
         * Fecha o modal de Atribuição de Processos e limpa o formulário
         */
        function fecharModalOficial() {
            const modal = document.getElementById('modalProcesso');
            const form = document.getElementById('formAtribuicao');

            if (!modal) return;

            // Oculta o modal
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Reabilita a rolagem da página

            // Reseta os campos do formulário
            if (form) {
                form.reset();
            }
        }

        /**
         * Manipula a submissão do formulário (Criar e Delegar)
         */
        async function criarEAtribuir(event) {
            event.preventDefault(); // Evita o recarregamento da página

            // Captura os valores do formulário do modal
            const form = document.getElementById('formAtribuicao');
            const numeroProcesso = document.getElementById('inputNumeroProcesso').value;
            const nomeArguido = form.querySelector('input[name="nome_arguido"]').value;
            const tecnicoId = document.getElementById('selectTecnicoDelegar').value;

            const btnSubmit = form.querySelector('button[type="submit"]');
            const textoOriginal = btnSubmit.innerHTML;

            // Altera o botão para estado de carregamento
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i> A processar...`;

            try {
                const response = await fetch('../controller/processos/salvar_processo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        numero_processo: numeroProcesso,
                        nome_arguido: nomeArguido,
                        tecnico_id: tecnicoId
                    })
                });

                const resultado = await response.json();

                if (resultado.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: resultado.message,
                        timer: 1800,
                        showConfirmButton: false,
                        background: document.documentElement.classList.contains('dark') ? '#18181b' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f4f4f5' : '#18181b'
                    }).then(() => {
                        // Fecha o modal e limpa o formulário
                        if (typeof fecharModalOficial === 'function') {
                            fecharModalOficial();
                        }
                        form.reset();

                        // Opcional: Atualizar a tabela de processos na página sem refresh
                        if (typeof carregarListaProcessos === 'function') {
                            carregarListaProcessos();
                        } else {
                            location.reload(); // Recarrega opcionalmente se não houver função de listagem dinâmica
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Atenção',
                        text: resultado.message,
                        confirmButtonColor: '#27272a'
                    });
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = textoOriginal;
                }

            } catch (error) {
                console.error('Erro na requisição:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de Comunicação',
                    text: 'Não foi possível ligar ao servidor. Tente novamente.',
                    confirmButtonColor: '#27272a'
                });
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = textoOriginal;
            }
        }

        // Fecha o modal ao pressionar a tecla "ESC"
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('modalProcesso');
                if (modal && !modal.classList.contains('hidden')) {
                    fecharModalOficial();
                }
            }
        });
    </script>

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