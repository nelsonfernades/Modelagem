<!-- #region -->
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
<!-- #endregion -->
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

    <div class="flex">
        <!-- MENU LATERAL (SIDEBAR) ADAPTADO PARA TCC -->
        <aside id="sidebar"
            class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-[#0f172a] text-slate-300 transform -translate-x-full md:translate-x-0 sidebar-transition flex flex-col border-r border-white/5 overflow-y-auto">

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
                            class="relative flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600/10 text-white transition-all">
                            <div class="absolute left-0 w-1 h-6 bg-blue-400 rounded-r-full"></div>
                            <i class="fa-solid fa-user-check w-5 text-blue-400 transition-colors"></i>
                            <span class="text-sm font-medium text-blue-400">Validacao</span>
                        </a>
                        <a href="novo_processo.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:text-white hover:bg-white/5 transition-all group">
                            <i class="fa-solid fa-users-rectangle w-5 group-hover:text-blue-400 transition-colors"></i>
                            <span class="text-sm font-medium ">Entrada de processos</span>
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

            <!-- ======================================================= -->
            <!-- ABA: VALIDAÇÃO DE REINCIDÊNCIA E CONSULTA BIOMÉTRICA    -->
            <!-- ======================================================= -->
            <main class="p-6 max-w-[1600px] w-full mx-auto flex-1 overflow-y-auto animate-fade-in space-y-8 scroll">

                <!-- 1. TOPO DA PÁGINA & MÉTRICAS RÁPIDAS -->
                <div
                    class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-zinc-200 dark:border-zinc-800">
                    <div>
                        <div
                            class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold text-xs uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Sistema de Identificação Criminal</span>
                        </div>
                        <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Validação de
                            Reincidência</h2>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Verifique os antecedentes do arguido via Biometria Facial, Impressão Digital (AFIS) ou
                            Documento antes do registo de entrada.
                        </p>
                    </div>

                    <!-- Indicadores Rápidos do Dia -->
                    <div class="flex flex-wrap items-center gap-4">
                        <div
                            class="flex items-center gap-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 px-4 py-2.5 rounded-2xl">
                            <div
                                class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm font-black">
                                <i class="fa-solid fa-fingerprint"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Consultas Hoje
                                </p>
                                <h3 class="text-sm font-black text-zinc-800 dark:text-white">18 Validações</h3>
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-3 bg-rose-500/5 border border-rose-500/20 px-4 py-2.5 rounded-2xl">
                            <div
                                class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-sm font-black">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <p
                                    class="text-[10px] font-bold text-rose-600/80 dark:text-rose-400 uppercase tracking-wider">
                                    Reincidentes</p>
                                <h3 class="text-sm font-black text-rose-700 dark:text-rose-300">5 Detetados</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PAINEL PRINCIPAL DE CAPTURA E PESQUISA BIOMÉTRICA -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    <!-- COLUNA DA ESQUERDA (8 Cols): TERMINAL DE CAPTURA BIOMÉTRICA -->
                    <div class="lg:col-span-7 xl:col-span-8 space-y-6">

                        <div
                            class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 shadow-xs space-y-6">

                            <!-- Seleção de Método de Validação -->
                            <div
                                class="flex items-center justify-between border-b border-zinc-150 dark:border-zinc-800 pb-4">
                                <h3 class="text-xs font-black uppercase tracking-wider text-zinc-400">Método de Captura
                                    & Pesquisa</h3>
                                <span
                                    class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold uppercase border border-emerald-500/20">
                                    ● Hardware Conectado
                                </span>
                            </div>

                            <!-- CARDS DE SELEÇÃO DE MODO DE SCANNER -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <button type="button" onclick="selecionarMetodo('facial')" id="btnMetodoFacial"
                                    class="p-4 rounded-xl border-2 border-indigo-600 bg-indigo-50/30 dark:bg-indigo-950/20 flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition-all">
                                    <i class="fa-solid fa-camera text-xl text-indigo-600 dark:text-indigo-400"></i>
                                    <span class="text-xs font-bold text-zinc-800 dark:text-white">Reconhecimento
                                        Facial</span>
                                </button>

                                <button type="button" onclick="selecionarMetodo('digital')" id="btnMetodoDigital"
                                    class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition-all">
                                    <i class="fa-solid fa-fingerprint text-xl text-zinc-400"></i>
                                    <span class="text-xs font-bold text-zinc-600 dark:text-zinc-300">Dactiloscopia
                                        (AFIS)</span>
                                </button>

                                <button type="button" onclick="selecionarMetodo('manual')" id="btnMetodoManual"
                                    class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition-all">
                                    <i class="fa-solid fa-magnifying-glass text-xl text-zinc-400"></i>
                                    <span class="text-xs font-bold text-zinc-600 dark:text-zinc-300">Nº BI /
                                        Documento</span>
                                </button>
                            </div>

                            <!-- CONTEÚDO DINÂMICO 01: CAPTURA FACIAL -->
                            <div id="boxCapturaFacial" class="space-y-4">
                                <div
                                    class="relative w-full aspect-16/9 bg-black rounded-2xl overflow-hidden border border-zinc-800 flex items-center justify-center shadow-inner group">
                                    <video id="videoScan" autoplay playsinline muted
                                        class="w-full h-[450px] object-cover"></video>

                                    <!-- Overlay/Grid de Ajuste Facial (HUD) -->
                                    <div
                                        class="absolute inset-0 border-2 border-dashed border-indigo-500/40 rounded-2xl pointer-events-none flex items-center justify-center">
                                        <div
                                            class="w-48 h-64 border-2 border-indigo-400/70 rounded-full flex items-center justify-center">
                                            <span
                                                class="text-[10px] uppercase tracking-widest text-indigo-300/80 font-mono">Alinhe
                                                o Rosto</span>
                                        </div>
                                    </div>

                                    <div
                                        class="absolute bottom-3 left-3 bg-zinc-950/80 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10 text-[10px] text-zinc-300 font-mono">
                                        ICAO Doc 9303 Compliant
                                    </div>
                                </div>

                                <button type="button" onclick="executarValidacao()"
                                    class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-expand text-sm"></i>
                                    <span>Capturar & Validar Reincidência</span>
                                </button>
                            </div>

                            <!-- CONTEÚDO DINÂMICO 02: LEITOR DACTILOSCÓPICO (ESCONDIDO POR PADRÃO) -->
                            <div id="boxCapturaDigital" class="hidden space-y-4">
                                <div
                                    class="p-8 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex flex-col items-center justify-center text-center gap-4">
                                    <div class="w-24 h-28 border-2 border-dashed border-indigo-500/60 rounded-xl flex flex-col items-center justify-center bg-white dark:bg-zinc-900 shadow-sm relative overflow-hidden group cursor-pointer"
                                        onclick="executarValidacao()">
                                        <i class="fa-solid fa-fingerprint text-4xl text-indigo-600 animate-pulse"></i>
                                        <div
                                            class="absolute inset-x-0 h-1 bg-indigo-500 top-0 shadow-sm animate-bounce">
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-zinc-800 dark:text-white uppercase">Aguardando
                                            Impressão Digital</h4>
                                        <p class="text-[11px] text-zinc-500 mt-0.5">Posicione o indicador ou polegar
                                            direito sobre o sensor biológico.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- CONTEÚDO DINÂMICO 03: PESQUISA MANUAL (ESCONDIDO POR PADRÃO) -->
                            <div id="boxCapturaManual" class="hidden space-y-4">
                                <div class="flex gap-2">
                                    <input type="text" id="inputBuscaManual"
                                        placeholder="Digite o Nº do BI, Passaporte ou Nome..."
                                        class="flex-1 px-4 py-3 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 uppercase">
                                    <button type="button" onclick="executarValidacao()"
                                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase rounded-xl transition-all">
                                        Pesquisar
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- COLUNA DA DIREITA (4 Cols): PAINEL DE RESULTADO DA CONSUTLA -->
                    <div class="lg:col-span-5 xl:col-span-4 space-y-6">

                        <!-- ESTADO A: NENHUMA PESQUISA FEITA (PLACEHOLDER) -->
                        <div id="resultadoVazio"
                            class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-8 text-center flex flex-col items-center justify-center min-h-[380px] space-y-3">
                            <div
                                class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 text-2xl">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">Aguardando Verificação
                            </h4>
                            <p class="text-xs text-zinc-500 max-w-xs leading-relaxed">
                                Execute a captura facial, biométrica ou pesquisa por documento para consultar a base de
                                dados centralizada de arguidos.
                            </p>
                        </div>

                        <!-- ESTADO B: REINCIDENTE DETETADO (RESULTADO DA CONSULTA) -->
                        <div id="resultadoEncontrado"
                            class="bg-white dark:bg-zinc-900 rounded-2xl border-2 border-rose-500/30 dark:border-rose-500/20 p-6 shadow-xl space-y-6 animate-fade-in hidden">

                            <!-- Status Banner -->
                            <div
                                class="p-3 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-center gap-3 text-rose-600 dark:text-rose-400">
                                <i class="fa-solid fa-triangle-exclamation text-lg shrink-0"></i>
                                <div>
                                    <h4 class="text-xs font-black uppercase tracking-wider">REINCIDENTE DETETADO</h4>
                                    <p class="text-[10px] opacity-90 font-medium">Constam 2 processos anteriores
                                        registados.</p>
                                </div>
                            </div>

                            <!-- Resumo do Perfil -->
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-16 h-20 bg-zinc-200 dark:bg-zinc-800 rounded-xl border border-zinc-300 dark:border-zinc-700 overflow-hidden shrink-0">
                                    <img src="https://via.placeholder.com/150" alt="Foto do Arguido"
                                        class="w-full h-full object-cover">
                                </div>
                                <div class="space-y-1">
                                    <h3
                                        class="text-sm font-black text-zinc-900 dark:text-white uppercase leading-tight">
                                        Manuel Domingos Eduardo</h3>
                                    <p class="text-xs text-zinc-500 font-medium">Alcunha: <span
                                            class="text-zinc-800 dark:text-zinc-200 font-bold">"Kito"</span></p>
                                    <p class="text-xs font-mono text-zinc-500">BI: <span
                                            class="text-zinc-800 dark:text-zinc-200 font-bold">004829102LA042</span></p>
                                </div>
                            </div>

                            <hr class="border-zinc-150 dark:border-zinc-800" />

                            <!-- Detalhes do Histórico -->
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between py-1 border-b border-zinc-100 dark:border-zinc-800/50">
                                    <span class="text-zinc-400 font-medium uppercase text-[10px]">Última
                                        Ocorrência:</span>
                                    <span class="font-bold text-zinc-800 dark:text-zinc-200">14/02/2024</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-zinc-100 dark:border-zinc-800/50">
                                    <span class="text-zinc-400 font-medium uppercase text-[10px]">Crime Anterior:</span>
                                    <span class="font-bold text-rose-600 dark:text-rose-400">Roubo Qualificado</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-zinc-100 dark:border-zinc-800/50">
                                    <span class="text-zinc-400 font-medium uppercase text-[10px]">Situação
                                        Anterior:</span>
                                    <span class="font-bold text-zinc-800 dark:text-zinc-200">Pena Cumpida (Comarca
                                        Viana)</span>
                                </div>
                            </div>

                            <!-- Ações do Painel -->
                            <div class="space-y-2 pt-2">
                                <!-- Botão no Painel de Resultado da Consulta -->
                                <button type="button" onclick="abrirFichaComDados({
                                        numProcesso: '#2026/0481',
                                        nome: 'Manuel Domingos Eduardo',
                                        alcunha: 'Kito',
                                        bi: '004829102LA042',
                                        status: 'Reincidente (Detido)',
                                        dataNasc: '12/05/1994 (31 anos)',
                                        nacionalidade: 'Angolana',
                                        naturalidade: 'Luanda',
                                        estadoCivil: 'Solteiro(a)',
                                        profissao: 'Motorista',
                                        numFilhos: '2',
                                        filiacao: 'António Eduardo & Maria Domingos',
                                        residencia: 'Bairro Benfica, Rua da Horta, Casa Nº 45, Luanda',
                                        crime: 'Roubo Qualificado à Mão Armada',
                                        prazo: '48 Horas (Detenção Provisória)',
                                        bens: '1x Telefone iPhone 13 Pro (Preto), 1x Carteira em pele contendo 15.000 Kz e cartões pessoais.'
                                    })"
                                    class="w-full py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white text-xs font-bold uppercase rounded-xl transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                                    <i class="fa-solid fa-id-card"></i>
                                    <span>Abrir Ficha Geral do Arguido</span>
                                </button>

                                <!-- Botão no Painel de Resultado da Validação -->
                                <button type="button" onclick="abrirEntradaOcorrencia({
                                            idArguido: 'ARG-2026-091',
                                            nome: 'Manuel Domingos Eduardo',
                                            alcunha: 'Kito',
                                            bi: '004829102LA042',
                                            foto: 'https://via.placeholder.com/150'
                                        })"
                                    class="w-full py-2.5 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-200 text-xs font-bold uppercase rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-2xs">
                                    <i class="fa-solid fa-folder-plus text-indigo-600 dark:text-indigo-400"></i>
                                    <span>Dar Entrada de Nova Ocorrência</span>
                                </button>
                            </div>

                        </div>

                    </div>

                </div>
            </main>

            <!-- ======================================================= -->
            <!-- MODAL DE NOVA OCORRÊNCIA PARA ARGUIDO EXISTENTE    -->
            <!-- ======================================================= -->
            <div id="modalNovaOcorrencia"
                class="fixed inset-0 z-50 hidden bg-zinc-950/60 backdrop-blur-xs flex items-center justify-center p-4">
                <div
                    class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl w-full max-w-2xl flex flex-col shadow-2xl overflow-hidden animate-fade-in">

                    <!-- CABEÇALHO -->
                    <div
                        class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-950/50">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black">
                                <i class="fa-solid fa-file-circle-plus text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black uppercase text-zinc-900 dark:text-white tracking-wider">
                                    Anexar Nova Ocorrência</h3>
                                <p class="text-[11px] text-zinc-500">Registo de novo processo para indivíduo já
                                    cadastrado.</p>
                            </div>
                        </div>
                        <button type="button" onclick="fecharModalNovaOcorrencia()"
                            class="p-2 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg transition-colors cursor-pointer">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <form id="formNovaOcorrencia" onsubmit="guardarOcorrencia(event)"
                        class="p-6 space-y-5 overflow-y-auto max-h-[80vh]">

                        <!-- CARTÃO DE RESUMO DO ARGUIDO (CAMPOS INATIVOS / READONLY) -->
                        <div
                            class="p-3.5 bg-zinc-100/70 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center gap-4 opacity-90 select-none">
                            <img id="ocorrenciaFoto" src="https://via.placeholder.com/150" alt="Foto Arguido"
                                class="w-12 h-14 rounded-lg object-cover border border-zinc-300 dark:border-zinc-700">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-2 py-0.5 text-[9px] font-black uppercase rounded bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700">
                                        <i class="fa-solid fa-lock text-[8px] mr-1"></i>Registo Bloqueado
                                    </span>
                                    <span id="ocorrenciaBI"
                                        class="text-[11px] font-mono font-bold text-zinc-500">---</span>
                                </div>
                                <h4 id="ocorrenciaNome"
                                    class="text-sm font-black text-zinc-900 dark:text-white truncate pt-0.5">---</h4>
                                <p class="text-xs text-zinc-500">Alcunha: <span id="ocorrenciaAlcunha"
                                        class="font-semibold text-zinc-700 dark:text-zinc-300">---</span></p>
                            </div>
                        </div>

                        <hr class="border-zinc-150 dark:border-zinc-800" />

                        <!-- SEÇÃO DE PREENCHIMENTO ATIVO -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4
                                    class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider">
                                    <i class="fa-solid fa-[#fa-pen] mr-1"></i> Dados do Novo Incidente
                                </h4>
                                <span class="text-[10px] text-zinc-400 font-mono">* Campos obrigatórios</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Campo 1: Novo Número de Processo (Ativo) -->
                                <div>
                                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">
                                        Nº do Novo Processo / Auto *
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-400">
                                            <i class="fa-solid fa-hashtag text-xs"></i>
                                        </span>
                                        <input type="text" id="novoNumProcesso" required placeholder="Ex: 2026/0892"
                                            class="w-full pl-8 pr-3 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono font-bold">
                                    </div>
                                </div>

                                <!-- Campo 2: Crime / Tipificação (Ativo) -->
                                <div>
                                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">
                                        Crime / Infração Imputada *
                                    </label>
                                    <input type="text" id="novoCrime" required placeholder="Ex: Furto Qualificado"
                                        class="w-full px-3 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold">
                                </div>
                            </div>

                            <!-- Campo 3: Descrição dos Bens Apreendidos (Ativo) -->
                            <div>
                                <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">
                                    Descrição dos Bens Apreendidos / Acompanhantes
                                </label>
                                <textarea id="novosBens" rows="3"
                                    placeholder="Descreva objetos, valores ou viaturas apreendidos nesta ocorrência específica..."
                                    class="w-full p-3 text-xs bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>
                        </div>

                        <!-- RODAPÉ DE AÇÕES -->
                        <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-2">
                            <button type="button" onclick="fecharModalNovaOcorrencia()"
                                class="px-4 py-2 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 text-xs font-bold uppercase rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-5 py-2 bg-indigo-600 text-white text-xs font-black uppercase rounded-xl hover:bg-indigo-700 transition-all flex items-center gap-2 cursor-pointer shadow-sm">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Registar Ocorrência</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <script>
                // Variável global temporária para guardar o ID do arguido em edição
                let arguidoSelecionadoId = null;

                // Função para abrir o modal focado apenas na nova ocorrência
                function abrirEntradaOcorrencia(arguido) {
                    if (!arguido) return;

                    // Guardar ID interno do arguido
                    arguidoSelecionadoId = arguido.idArguido || null;

                    // Preencher o cabeçalho inativo (Apenas leitura)
                    document.getElementById('ocorrenciaNome').innerText = arguido.nome || 'Não Informado';
                    document.getElementById('ocorrenciaAlcunha').innerText = arguido.alcunha ? `"${arguido.alcunha}"` : 'N/A';
                    document.getElementById('ocorrenciaBI').innerText = `BI: ${arguido.bi || 'N/A'}`;
                    if (arguido.foto) {
                        document.getElementById('ocorrenciaFoto').src = arguido.foto;
                    }

                    // Limpar os campos ativos para a nova entrada
                    document.getElementById('novoNumProcesso').value = '';
                    document.getElementById('novoCrime').value = '';
                    document.getElementById('novosBens').value = '';

                    // Sugerir um número de processo automático (opcional)
                    const anoAtual = new Date().getFullYear();
                    const numAleatorio = Math.floor(1000 + Math.random() * 9000);
                    document.getElementById('novoNumProcesso').value = `#${anoAtual}/${numAleatorio}`;

                    // Exibir o Modal
                    const modal = document.getElementById('modalNovaOcorrencia');
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';

                    // Colocar o cursor diretamente no primeiro campo ativo
                    setTimeout(() => {
                        document.getElementById('novoNumProcesso').focus();
                    }, 100);
                }

                // Fechar o Modal
                function fecharModalNovaOcorrencia() {
                    const modal = document.getElementById('modalNovaOcorrencia');
                    if (modal) {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                }

                // Submissão do Formulário
                function guardarOcorrencia(event) {
                    event.preventDefault();

                    const payload = {
                        arguidoId: arguidoSelecionadoId,
                        numProcesso: document.getElementById('novoNumProcesso').value,
                        crime: document.getElementById('novoCrime').value,
                        bens: document.getElementById('novosBens').value,
                        dataRegisto: new Date().toISOString()
                    };

                    console.log("Nova Ocorrência Registada:", payload);

                    // Fechar modal
                    fecharModalNovaOcorrencia();

                    // Notificação rápida (pode ser adaptada para a sua biblioteca de toast)
                    alert(`Ocorrência ${payload.numProcesso} anexada com sucesso ao arguido!`);
                }
            </script>

            <!-- ======================================================= -->
            <!-- OVERLAY E PAINEL DA FICHA DO ARGUIDO    -->
            <!-- ======================================================= -->
            <div id="drawerFicha" class="fixed inset-0 z-50 hidden" aria-labelledby="slide-over-title" role="dialog"
                aria-modal="true">
                <!-- Fundo Escuro com Blur -->
                <div id="drawerOverlay" onclick="fecharFichaArguido()"
                    class="fixed inset-0 bg-zinc-950/60 backdrop-blur-xs transition-opacity duration-300 opacity-0">
                </div>

                <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                    <!-- Container Principal -->
                    <div id="drawerContent"
                        class="w-screen max-w-2xl bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 ease-in-out">

                        <!-- CABEÇALHO DA FICHA -->
                        <div
                            class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-950/50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 flex items-center justify-center font-black">
                                    <i class="fa-solid fa-id-card text-lg"></i>
                                </div>
                                <div>
                                    <h3
                                        class="text-sm font-black uppercase text-zinc-900 dark:text-white tracking-wider">
                                        Ficha Geral do Arguido</h3>
                                    <p class="text-[11px] text-zinc-500 font-mono">PROCESSO Nº <span
                                            id="fichaNumProcesso">#2026/0000</span></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" onclick="window.print()"
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

                        <!-- CORPO SCROLLÁVEL -->
                        <div class="flex-1 overflow-y-auto p-6 space-y-6">

                            <!-- CARTÃO BIOMÉTRICO PRINCIPAL -->
                            <div
                                class="p-4 bg-zinc-50 dark:bg-zinc-950/40 rounded-2xl border border-zinc-200 dark:border-zinc-800 flex items-start gap-4">
                                <div
                                    class="w-24 h-28 bg-zinc-200 dark:bg-zinc-800 rounded-xl border border-zinc-300 dark:border-zinc-700 overflow-hidden shrink-0 flex items-center justify-center">
                                    <img id="fichaFoto" src="https://via.placeholder.com/150" alt="Foto do Arguido"
                                        class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span id="fichaStatus"
                                            class="px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                            Reincidente
                                        </span>
                                        <span
                                            class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">●
                                            AFIS Synced</span>
                                    </div>
                                    <h2 id="fichaNome"
                                        class="text-base font-black text-zinc-900 dark:text-white uppercase leading-tight pt-1">
                                        ---
                                    </h2>
                                    <p class="text-xs text-zinc-500 font-medium">Alcunha: <span id="fichaAlcunha"
                                            class="text-zinc-800 dark:text-zinc-200 font-semibold">---</span></p>
                                    <p class="text-xs text-zinc-500 font-mono pt-1">BI / Doc: <span id="fichaBI"
                                            class="text-zinc-800 dark:text-zinc-200 font-bold">---</span></p>
                                </div>
                            </div>

                            <hr class="border-zinc-150 dark:border-zinc-800" />

                            <!-- IDENTIDADE CIVIL -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">01 - Identidade
                                    Civil & Contactos</h4>
                                <div
                                    class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-zinc-50/50 dark:bg-zinc-950/20 p-4 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 text-xs">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-zinc-400 block">Data
                                            Nasc.</span>
                                        <span id="fichaDataNasc"
                                            class="font-semibold text-zinc-800 dark:text-zinc-200">---</span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-[10px] uppercase font-bold text-zinc-400 block">Nacionalidade</span>
                                        <span id="fichaNacionalidade"
                                            class="font-semibold text-zinc-800 dark:text-zinc-200">---</span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-[10px] uppercase font-bold text-zinc-400 block">Naturalidade</span>
                                        <span id="fichaNaturalidade"
                                            class="font-semibold text-zinc-800 dark:text-zinc-200">---</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-zinc-400 block">Estado
                                            Civil</span>
                                        <span id="fichaEstadoCivil"
                                            class="font-semibold text-zinc-800 dark:text-zinc-200">---</span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-[10px] uppercase font-bold text-zinc-400 block">Profissão</span>
                                        <span id="fichaProfissao"
                                            class="font-semibold text-zinc-800 dark:text-zinc-200">---</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-zinc-400 block">Nº de
                                            Filhos</span>
                                        <span id="fichaNumFilhos"
                                            class="font-semibold text-zinc-800 dark:text-zinc-200">---</span>
                                    </div>
                                    <div class="col-span-2 sm:col-span-3">
                                        <span
                                            class="text-[10px] uppercase font-bold text-zinc-400 block">Filiação</span>
                                        <span id="fichaFiliacao"
                                            class="font-semibold text-zinc-800 dark:text-zinc-200">---</span>
                                    </div>
                                    <div class="col-span-2 sm:col-span-3">
                                        <span class="text-[10px] uppercase font-bold text-zinc-400 block">Residência
                                            Habitual</span>
                                        <span id="fichaResidencia"
                                            class="font-semibold text-zinc-800 dark:text-zinc-200">---</span>
                                    </div>
                                </div>
                            </div>

                            <!-- MEDIDA COERCITIVA E HISTÓRICO -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">02 - Situação
                                    Processual & Medida</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="p-3 bg-rose-500/5 border border-rose-500/20 rounded-xl space-y-1">
                                        <span class="text-[10px] uppercase font-bold text-rose-500 block">Crime
                                            Imputado</span>
                                        <p id="fichaCrime" class="text-xs font-bold text-zinc-800 dark:text-zinc-100">
                                            ---</p>
                                    </div>
                                    <div
                                        class="p-3 bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-200 dark:border-zinc-800 rounded-xl space-y-1">
                                        <span class="text-[10px] uppercase font-bold text-zinc-400 block">Prazo
                                            Coactivo</span>
                                        <p id="fichaPrazo" class="text-xs font-bold text-zinc-800 dark:text-zinc-100">
                                            ---</p>
                                    </div>
                                </div>
                            </div>

                            <!-- BENS APREENDIDOS -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-black uppercase text-zinc-400 tracking-wider">03 - Registo de
                                    Bens Acompanhantes</h4>
                                <div
                                    class="p-3 bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs space-y-1">
                                    <p id="fichaBens" class="text-zinc-700 dark:text-zinc-300 font-medium">---</p>
                                </div>
                            </div>

                        </div>

                        <!-- RODAPÉ DO PAINEL -->
                        <div
                            class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50 flex justify-between items-center">
                            <button type="button" onclick="window.print()"
                                class="px-4 py-2 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 text-xs font-bold uppercase rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">
                                Exportar PDF / Imprimir
                            </button>
                            <button type="button" onclick="fecharFichaArguido()"
                                class="px-5 py-2 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 text-xs font-black uppercase rounded-xl hover:opacity-90 transition-all">
                                Fechar Ficha
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ======================================================= -->
            <!-- SCRIPT AUXILIAR DE INTERAÇÃO    -->
            <!-- ======================================================= -->
            <script>
                // Função para popular e abrir o drawer
                function abrirFichaComDados(dados) {
                    if (!dados) return;

                    // Injeção dinâmica dos campos
                    document.getElementById('fichaNumProcesso').innerText = dados.numProcesso || '#2026/0000';
                    document.getElementById('fichaNome').innerText = dados.nome || 'Não Informado';
                    document.getElementById('fichaAlcunha').innerText = dados.alcunha ? `"${dados.alcunha}"` : 'N/A';
                    document.getElementById('fichaBI').innerText = dados.bi || 'N/A';
                    document.getElementById('fichaStatus').innerText = dados.status || 'Em Validação';
                    document.getElementById('fichaDataNasc').innerText = dados.dataNasc || 'N/A';
                    document.getElementById('fichaNacionalidade').innerText = dados.nacionalidade || 'N/A';
                    document.getElementById('fichaNaturalidade').innerText = dados.naturalidade || 'N/A';
                    document.getElementById('fichaEstadoCivil').innerText = dados.estadoCivil || 'N/A';
                    document.getElementById('fichaProfissao').innerText = dados.profissao || 'N/A';
                    document.getElementById('fichaNumFilhos').innerText = dados.numFilhos || '0';
                    document.getElementById('fichaFiliacao').innerText = dados.filiacao || 'N/A';
                    document.getElementById('fichaResidencia').innerText = dados.residencia || 'N/A';
                    document.getElementById('fichaCrime').innerText = dados.crime || 'N/A';
                    document.getElementById('fichaPrazo').innerText = dados.prazo || 'N/A';
                    document.getElementById('fichaBens').innerText = dados.bens || 'Nenhum bem registado.';

                    // Exibir Drawer com Animação
                    const drawer = document.getElementById('drawerFicha');
                    const overlay = document.getElementById('drawerOverlay');
                    const content = document.getElementById('drawerContent');

                    drawer.classList.remove('hidden');
                    setTimeout(() => {
                        overlay.classList.remove('opacity-0');
                        content.classList.remove('translate-x-full');
                    }, 10);

                    document.body.style.overflow = 'hidden';
                }

                // Fechar o Drawer
                function fecharFichaArguido() {
                    const overlay = document.getElementById('drawerOverlay');
                    const content = document.getElementById('drawerContent');

                    overlay.classList.add('opacity-0');
                    content.classList.add('translate-x-full');

                    setTimeout(() => {
                        document.getElementById('drawerFicha').classList.add('hidden');
                        document.body.style.overflow = '';
                    }, 300);
                }

                // Suporte à tecla ESC para fechar
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        const drawer = document.getElementById('drawerFicha');
                        if (drawer && !drawer.classList.contains('hidden')) {
                            fecharFichaArguido();
                        }
                    }
                });
            </script>

            <!-- ======================================================= -->
            <!-- SCRIPT AUXILIAR DE INTERAÇÃO    -->
            <!-- ======================================================= -->
            <script>
                function selecionarMetodo(metodo) {
                    // Oculta todas as caixas de captura
                    document.getElementById('boxCapturaFacial').classList.add('hidden');
                    document.getElementById('boxCapturaDigital').classList.add('hidden');
                    document.getElementById('boxCapturaManual').classList.add('hidden');

                    // Reset estilos dos botões
                    ['btnMetodoFacial', 'btnMetodoDigital', 'btnMetodoManual'].forEach(id => {
                        const btn = document.getElementById(id);
                        btn.className = "p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition-all";
                        btn.querySelector('i').className = btn.querySelector('i').className.replace('text-indigo-600 dark:text-indigo-400', 'text-zinc-400');
                    });

                    // Ativa a aba selecionada
                    if (metodo === 'facial') {
                        document.getElementById('boxCapturaFacial').classList.remove('hidden');
                        const btn = document.getElementById('btnMetodoFacial');
                        btn.className = "p-4 rounded-xl border-2 border-indigo-600 bg-indigo-50/30 dark:bg-indigo-950/20 flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition-all";
                        btn.querySelector('i').classList.add('text-indigo-600', 'dark:text-indigo-400');
                    } else if (metodo === 'digital') {
                        document.getElementById('boxCapturaDigital').classList.remove('hidden');
                        const btn = document.getElementById('btnMetodoDigital');
                        btn.className = "p-4 rounded-xl border-2 border-indigo-600 bg-indigo-50/30 dark:bg-indigo-950/20 flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition-all";
                        btn.querySelector('i').classList.add('text-indigo-600', 'dark:text-indigo-400');
                    } else {
                        document.getElementById('boxCapturaManual').classList.remove('hidden');
                        const btn = document.getElementById('btnMetodoManual');
                        btn.className = "p-4 rounded-xl border-2 border-indigo-600 bg-indigo-50/30 dark:bg-indigo-950/20 flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition-all";
                        btn.querySelector('i').classList.add('text-indigo-600', 'dark:text-indigo-400');
                    }
                }

                // Simulação da validação ao clicar em validar
                function executarValidacao() {
                    document.getElementById('resultadoVazio').classList.add('hidden');
                    document.getElementById('resultadoEncontrado').classList.remove('hidden');
                }

                let streamAtual = null;

                let streamAtual = null;

                async function iniciarCameraTraseira() {
                    const video = document.getElementById('videoScan') || document.getElementById('video');

                    if (!video) {
                        alert("Elemento de vídeo não encontrado no HTML!");
                        return;
                    }

                    // 1. Verificação de suporte e HTTPS
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        alert("O seu navegador bloqueou a câmera. Verifique se está usando HTTPS e deu permissões ao site.");
                        return;
                    }

                    // Parar qualquer stream anterior
                    pararCamera();

                    // 2. Garantir atributos obrigatórios para mobile via JS
                    video.setAttribute('playsinline', 'true');
                    video.setAttribute('autoplay', 'true');
                    video.muted = true;

                    try {
                        // 3. Configuração de câmera ideal para leitores/scanner em mobile
                        const constraints = {
                            video: {
                                facingMode: { ideal: "environment" }, // Prioriza câmara traseira
                                width: { ideal: 1280 },
                                height: { ideal: 720 }
                            },
                            audio: false
                        };

                        streamAtual = await navigator.mediaDevices.getUserMedia(constraints);
                        video.srcObject = streamAtual;

                        // Em mobile é essencial tratar o retorno do play()
                        await video.play();

                    } catch (erro) {
                        console.warn("Falha ao abrir com restrições avançadas. Tentando modo básico...", erro);

                        // Fallback: Tenta abrir qualquer câmera com configurações genéricas
                        try {
                            streamAtual = await navigator.mediaDevices.getUserMedia({
                                video: { facingMode: "environment" },
                                audio: false
                            });
                            video.srcObject = streamAtual;
                            await video.play();
                        } catch (erroFallback) {
                            console.error("Não foi possível aceder à câmara:", erroFallback);
                            alert("Erro ao aceder à câmara. Verifique se concedeu permissão no navegador do celular.");
                        }
                    }
                }

                function pararCamera() {
                    if (streamAtual) {
                        streamAtual.getTracks().forEach(track => track.stop());
                        streamAtual = null;
                    }
                }

                iniciarCameraTraseira();
            </script>

            <!-- ======================================================= -->
            <!-- MODAL: AGENDAR / EDITAR BANCA                           -->
            <!-- ======================================================= -->
            <div id="bancaModal"
                class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm transition-all duration-300">
                <div class="bg-white w-full max-w-2xl rounded-[32px] border border-gray-100 shadow-2xl overflow-hidden flex flex-col transform transition-all scale-95 opacity-0 duration-300"
                    id="bancaContainer">

                    <!-- Cabeçalho -->
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                        <div>
                            <h3 class="text-lg font-black text-gray-800">Constituição e Agendamento de Banca</h3>
                            <p class="text-xs text-gray-500 font-semibold mt-0.5">Defina os membros examinadores, data
                                de arguição e local.</p>
                        </div>
                        <button onclick="closeModal('bancaModal', 'bancaContainer')"
                            class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Formulário -->
                    <form id="bancaForm" onsubmit="handleBancaSubmit(event)" class="p-6 space-y-4">

                        <!-- Seleção do Estudante -->
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Trabalho
                                do Estudante *</label>
                            <select name="estudante_id" required
                                class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm font-semibold text-gray-800 bg-white focus:outline-none focus:border-indigo-500 transition">
                                <option value="1">Afonso Henriques — Micro-Frontends</option>
                                <option value="2">Dinis Martins — Braços Robóticos</option>
                            </select>
                        </div>

                        <!-- Calendário e Local -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Data
                                    e Hora da Defesa *</label>
                                <input type="datetime-local" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm font-semibold text-gray-800 focus:outline-none focus:border-indigo-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Local
                                    / Link Virtual *</label>
                                <input type="text" placeholder="Ex: Sala 4.2 ou Link do Teams" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm font-semibold text-gray-800 focus:outline-none focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <!-- Corpo de Júris -->
                        <div class="bg-gray-50/50 p-4 border border-gray-100 rounded-2xl space-y-3">
                            <p class="text-[11px] font-black text-indigo-700 uppercase tracking-wider"><i
                                    class="fa-solid fa-users mr-1"></i> Nomeação de Examinadores</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 block mb-1">Arguente
                                        Interno</label>
                                    <input type="text" placeholder="Nome do Professor"
                                        class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs font-semibold focus:outline-none focus:border-indigo-500 bg-white transition">
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 block mb-1">Arguente Externo
                                        (Instituição)</label>
                                    <input type="text" placeholder="Nome do Professor (Instituição)"
                                        class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs font-semibold focus:outline-none focus:border-indigo-500 bg-white transition">
                                </div>
                            </div>
                        </div>

                        <!-- Rodapé / Ações -->
                        <div class="pt-4 border-t border-gray-50 flex items-center justify-end gap-3">
                            <button type="button" onclick="closeModal('bancaModal', 'bancaContainer')"
                                class="px-5 py-3 rounded-2xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition">Cancelar</button>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold text-xs transition shadow-lg shadow-indigo-100">Disparar
                                Convites e Agendar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ======================================================= -->
            <!-- ENCAIXE DE CONTROLO JAVASCRIPT                          -->
            <!-- ======================================================= -->
            <script>
                // =======================================================
                // NÚCLEO DE CONTROLO DOS MODAIS (CORREÇÃO DE ERRO)
                // =======================================================

                // Abre qualquer um dos modais aplicando animação de Fade-In e Zoom
                function openModal(modalId, containerId) {
                    const modal = document.getElementById(modalId);
                    const container = document.getElementById(containerId);

                    if (!modal || !container) {
                        console.error(`Erro: Não foi possível encontrar os elementos ${modalId} ou ${containerId}.`);
                        return;
                    }

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

                    if (!modal || !container) return;

                    container.classList.remove('scale-100', 'opacity-100');
                    container.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 250);
                }

                // =======================================================
                // GATILHOS DA INTERFACE (BANCAS EXAMINADORAS)
                // =======================================================

                // Gatilho para criar nova banca (Limpa o formulário e abre)
                function openBancaModal() {
                    const form = document.getElementById('bancaFormReal');
                    if (form) form.reset();

                    const bancaIdInput = document.getElementById('banca_id');
                    if (bancaIdInput) bancaIdInput.value = "";

                    const title = document.getElementById('bancaFormTitle');
                    if (title) title.innerText = "Agendar e Agregar Banca Examinadora";

                    openModal('bancaFormModal', 'bancaFormContainer');
                }

                // Gatilho para abrir o formulário já no modo de Edição Direta
                function abrirEdicaoDireta(id) {
                    const form = document.getElementById('bancaFormReal');
                    if (form) form.reset();

                    const bancaIdInput = document.getElementById('banca_id');
                    if (bancaIdInput) bancaIdInput.value = id;

                    const title = document.getElementById('bancaFormTitle');
                    if (title) title.innerText = "Editar Parâmetros da Banca";

                    // [Simulação]: Define o estudante com base no ID recebido
                    const formEstudante = document.getElementById('form_estudante');
                    if (formEstudante) formEstudante.value = id;

                    openModal('bancaFormModal', 'bancaFormContainer');
                }

                // Gatilho de visualização detalhada
                function verBancaDetalhes(id) {
                    // Exemplo de preenchimento dinâmico mockado baseado no ID
                    const alunoNome = document.getElementById('view_aluno_nome');
                    const trabalhoTitulo = document.getElementById('view_trabalho_titulo');
                    const badgeStatus = document.getElementById('view_banca_status_badge');

                    if (id === '1') {
                        if (alunoNome) alunoNome.innerText = "Afonso Henriques";
                        if (trabalhoTitulo) trabalhoTitulo.innerText = "Implementação de Arquiteturas Micro-Frontends em Portais Acadêmicos";
                        if (badgeStatus) {
                            badgeStatus.className = "bg-emerald-50 text-emerald-700 px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider";
                            badgeStatus.innerText = "Confirmada";
                        }
                    }

                    openModal('bancaViewModal', 'bancaViewContainer');
                }

                // Atalho interno para abrir a edição direto a partir da visualização
                function transformarParaEdicao() {
                    closeModal('bancaViewModal', 'bancaViewContainer');

                    const title = document.getElementById('bancaFormTitle');
                    if (title) title.innerText = "Editar Parâmetros da Banca";

                    const formEstudante = document.getElementById('form_estudante');
                    if (formEstudante) formEstudante.value = "1";

                    setTimeout(() => {
                        openModal('bancaFormModal', 'bancaFormContainer');
                    }, 200);
                }

                // Submissão do Formulário
                function handleBancaFormSubmit(event) {
                    event.preventDefault();
                    // Sua lógica de salvamento AJAX/Fetch entra aqui
                    closeModal('bancaFormModal', 'bancaFormContainer');
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
         * Carrega a lista de técnicos/inspetores no elemento <select>
         */
        function carregarTecnicosSelect() {
            const select = document.getElementById('selectTecnicoDelegar');
            if (!select) return;

            select.innerHTML = '<option value="" disabled selected>Selecione o técnico responsável...</option>';

            tecnicosDisponiveis.forEach(tecnico => {
                const option = document.createElement('option');
                option.value = tecnico.id;
                option.textContent = tecnico.nome;
                select.appendChild(option);
            });
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

            // 2. Popula os técnicos no select
            carregarTecnicosSelect();

            // 3. Exibe o modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Evita rolagem da página ao fundo
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
        function criarEAtribuir(event) {
            event.preventDefault(); // Impede o recarregamento da página

            const form = event.target;
            const formData = new FormData(form);

            // Extrai os dados do formulário
            const dadosProcesso = {
                numeroProcesso: formData.get('numero_processo'),
                nomeArguido: formData.get('nome_arguido'),
                tecnicoId: formData.get('tecnico_id'),
                dataCriacao: new Date().toISOString()
            };

            // Encontra o nome do técnico selecionado para exibição
            const tecnicoSelecionado = tecnicosDisponiveis.find(t => t.id === dadosProcesso.tecnicoId);

            // Exemplo de log para inspeção/integração com API
            console.log(" Processo Criado com Sucesso:", dadosProcesso);

            // Notificação simples (Pode substituir por um Toast ou modal de confirmação)
            alert(`Processo ${dadosProcesso.numeroProcesso} atribuído a ${tecnicoSelecionado ? tecnicoSelecionado.nome : 'Técnico'} com sucesso!`);

            // Fecha o modal e limpa os campos
            fecharModalOficial();
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