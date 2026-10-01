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
                                class="relative flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600/10 text-white transition-all">
                                <div class="absolute left-0 w-1 h-6 bg-blue-400 rounded-r-full"></div>
                                <i class="fa-solid fa-gear w-5 text-blue-400 transition-colors"></i>
                                <span class="text-sm font-medium text-blue-400">Configurações</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>

            <!-- AVATAR INFERIOR COM INICIAIS DINÂMICAS -->
            <div class="p-4 border-t border-white/5 bg-black/20">
                <div class="flex items-center gap-3 px-2 py-2">
                    <div
                        class="w-9 h-9 rounded-full bg-green-500/20 text-blue-400 border border-green-500/30 font-bold flex items-center justify-center text-xs shrink-0">
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
            <!-- TELA: CENTRAL DE CONFIGURAÇÕES AVANÇADAS (SISTEMA DE ABAS) -->
            <!-- ======================================================= -->
            <main class="p-6 max-w-[1600px] w-full mx-auto flex-1 overflow-y-auto animate-fade-in">

                <!-- 1. TOPO DA PÁGINA -->
                <div class="mb-8">
                    <h2 class="text-2xl font-black text-gray-800 dark:text-white tracking-tight">Painel de Controlo da
                        Unidade</h2>
                    <p class="text-sm font-medium text-gray-500 dark:text-zinc-400 mt-1">
                        Ajuste os parâmetros institucionais do órgão, níveis de permissão de utilizadores e políticas de
                        custódia de autos.
                    </p>
                </div>

                <!-- NAVEGAÇÃO POR ABAS RESPONSIVA -->
                <div class="border-b border-gray-100 dark:border-zinc-800 mb-8">
                    <div class="flex gap-2 overflow-x-auto pb-px scrollbar-none sm:scrollbar-auto -mb-px">

                        <!-- Aba 2: Permissões & Operadores -->
                        <button onclick="switchTab('tab-usuarios')" id="btn-tab-usuarios"
                            class="tab-btn px-4 sm:px-5 py-3 text-xs uppercase tracking-wider rounded-t-xl border-b-2 transition-all whitespace-nowrap flex items-center gap-2 cursor-pointer
                   border-indigo-600 text-indigo-600 dark:text-indigo-400 font-black bg-indigo-50/40 dark:bg-indigo-950/30">
                            <i class="fa-solid fa-user-shield text-sm"></i>
                            <span>Permissões & Operadores</span>
                        </button>

                        <!-- Aba 3: Políticas de Instrução -->
                        <button onclick="switchTab('tab-restricoes')" id="btn-tab-restricoes"
                            class="tab-btn px-4 sm:px-5 py-3 text-xs uppercase tracking-wider rounded-t-xl border-b-2 transition-all whitespace-nowrap flex items-center gap-2 cursor-pointer
                   border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-800 dark:hover:text-zinc-200 font-bold">
                            <i class="fa-solid fa-sliders text-sm"></i>
                            <span>Políticas de Instrução</span>
                        </button>

                        <!-- Aba 4: Salvaguarda & Audit Log -->
                        <button onclick="switchTab('tab-backup')" id="btn-tab-backup"
                            class="tab-btn px-4 sm:px-5 py-3 text-xs uppercase tracking-wider rounded-t-xl border-b-2 transition-all whitespace-nowrap flex items-center gap-2 cursor-pointer
                   border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-800 dark:hover:text-zinc-200 font-bold">
                            <i class="fa-solid fa-database text-sm"></i>
                            <span>Salvaguarda & Audit Log</span>
                        </button>

                        <!-- Aba 5: Suporte -->
                        <button onclick="switchTab('tab-auxiliares')" id="btn-tab-auxiliares"
                            class="tab-btn px-4 sm:px-5 py-3 text-xs uppercase tracking-wider rounded-t-xl border-b-2 transition-all whitespace-nowrap flex items-center gap-2 cursor-pointer
                   border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-800 dark:hover:text-zinc-200 font-bold">
                            <i class="fa-solid fa-headset text-sm"></i>
                            <span>Suporte</span>
                        </button>

                    </div>
                </div>

                <!-- CONTAINER DE CONTEÚDO DAS ABAS -->
                <div
                    class="bg-white dark:bg-zinc-900 rounded-3xl border border-gray-100 dark:border-zinc-800 shadow-sm p-6 lg:p-8">

                    <!-- ========================================== -->
                    <!-- ABA 2: GESTÃO DE OPERADORES & NÍVEIS DE ACESSO -->
                    <!-- ========================================== -->
                    <section id="tab-usuarios" class="tab-content animate-fade-in space-y-6">
                        <!-- Cabeçalho da Seção -->
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-zinc-800 pb-4">
                            <div>
                                <h3 class="text-base font-black text-gray-800 dark:text-white">Gestão de Operadores &
                                    Níveis de Acesso</h3>
                                <p class="text-xs text-gray-500 dark:text-zinc-400 font-semibold mt-0.5">
                                    Controlo de credenciais, investigadores, peritos e escrivães autorizados a atuar na
                                    instrução.
                                </p>
                            </div>
                        </div>

                        <!-- Métrica Rápida de Operadores -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 hidden">
                            <div
                                class="p-4 rounded-2xl bg-gray-50/50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800">
                                <span class="text-[10px] font-black text-gray-400 dark:text-zinc-500 uppercase">Total
                                    Credenciados</span>
                                <p id="metric-total" class="text-xl font-black text-gray-800 dark:text-white mt-1">--
                                </p>
                            </div>
                            <div
                                class="p-4 rounded-2xl bg-emerald-50/30 dark:bg-emerald-950/20 border border-emerald-100/50 dark:border-emerald-900/30">
                                <span
                                    class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase">Investigadores
                                    Ativos</span>
                                <p id="metric-investigadores"
                                    class="text-xl font-black text-emerald-700 dark:text-emerald-400 mt-1">--</p>
                            </div>
                            <div
                                class="p-4 rounded-2xl bg-indigo-50/30 dark:bg-indigo-950/20 border border-indigo-100/50 dark:border-indigo-900/30">
                                <span
                                    class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase">Peritos
                                    Forenses</span>
                                <p id="metric-peritos"
                                    class="text-xl font-black text-indigo-700 dark:text-indigo-400 mt-1">--</p>
                            </div>
                            <div
                                class="p-4 rounded-2xl bg-amber-50/30 dark:bg-amber-950/20 border border-amber-100/50 dark:border-amber-900/30">
                                <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase">Acesso
                                    Pendente / Restrito</span>
                                <p id="metric-pendentes"
                                    class="text-xl font-black text-amber-700 dark:text-amber-400 mt-1">--</p>
                            </div>
                        </div>

                        <!-- Tabela de Operadores -->
                        <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-zinc-800">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr
                                        class="bg-gray-50/80 dark:bg-zinc-800/60 border-b border-gray-100 dark:border-zinc-800 text-gray-500 dark:text-zinc-400 font-black uppercase text-[10px] tracking-wider">
                                        <th class="p-4">Operador / Identificação</th>
                                        <th class="p-4">Perfil de Acesso</th>
                                        <th class="p-4">Processos</th>
                                        <th class="p-4">Estado</th>
                                        <th class="p-4 text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-operadores"
                                    class="divide-y divide-gray-100 dark:divide-zinc-800 text-gray-700 dark:text-zinc-300 font-medium">
                                    <!-- Conteúdo dinâmico injetado via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <script>
                        // Estilização customizada para SweetAlert2 integrada com Tailwind
                        const SwalCustom = Swal.mixin({
                            customClass: {
                                popup: 'rounded-3xl border border-gray-100 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-gray-800 dark:text-white shadow-2xl',
                                title: 'text-lg font-black text-gray-800 dark:text-white',
                                confirmButton: 'bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl mx-1 transition cursor-pointer',
                                cancelButton: 'bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 text-gray-600 dark:text-zinc-300 font-bold text-xs px-5 py-2.5 rounded-xl mx-1 transition cursor-pointer'
                            },
                            buttonsStyling: false
                        });

                        // Inicialização ao carregar a página
                        document.addEventListener('DOMContentLoaded', () => {
                            carregarOperadores();
                        });

                        /**
                         * 1. Carrega a lista de operadores e atualiza a UI
                         */
                        async function carregarOperadores() {
                            const tbody = document.getElementById('tbody-operadores');
                            if (!tbody) return;

                            // Skeleton loader de carregamento
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-400">
                                        <i class="fa-solid fa-circle-notch fa-spin text-indigo-600 text-2xl"></i>
                                        <p class="mt-2 text-xs font-semibold">A carregar operadores credenciados...</p>
                                    </td>
                                </tr>
                            `;

                            try {
                                const response = await fetch('../controller/config/users.php');

                                if (!response.ok) throw new Error('Erro ao carregar dados da API');

                                const operadores = await response.json();

                                // Atualizar Métricas Dinâmicas
                                atualizarMetricas(operadores);

                                // Se não existirem operadores
                                if (!Array.isArray(operadores) || operadores.length === 0) {
                                    tbody.innerHTML = `
                                        <tr>
                                            <td colspan="6" class="p-8 text-center text-gray-400 font-medium">
                                                Nenhum operador cadastrado até ao momento.
                                            </td>
                                        </tr>
                                    `;
                                    return;
                                }

                                // Renderizar Linhas da Tabela
                                tbody.innerHTML = operadores.map(op => renderLinhaOperador(op)).join('');

                            } catch (error) {
                                console.error('Erro ao carregar operadores:', error);
                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-rose-500 font-semibold">
                                            <i class="fa-solid fa-triangle-exclamation text-xl mb-1"></i>
                                            <p>Falha na ligação com o servidor de instrução.</p>
                                        </td>
                                    </tr>
                                `;
                            }
                        }

                        /**
                         * 2. Atualiza os cards superiores de métricas
                         */
                        function atualizarMetricas(operadores) {
                            if (!Array.isArray(operadores)) return;

                            const elTotal = document.getElementById('metric-total');
                            const elInvest = document.getElementById('metric-investigadores');
                            const elPeritos = document.getElementById('metric-peritos');
                            const elPendentes = document.getElementById('metric-pendentes');

                            if (elTotal) elTotal.innerText = operadores.length;
                            if (elInvest) elInvest.innerText = operadores.filter(o => (o.perfil_nome === 'Investigador' || o.cargo === 'Investigador') && o.estado === 'ativo').length;
                            if (elPeritos) elPeritos.innerText = operadores.filter(o => o.perfil_nome === 'Perito' || o.cargo === 'Perito').length;
                            if (elPendentes) elPendentes.innerText = operadores.filter(o => o.estado === 'pendente' || o.estado === 'bloqueado').length;
                        }

                        /**
                         * 3. Renderiza a linha HTML de um operador
                         */
                        function renderLinhaOperador(op) {
                            const nomes = (op.nome || 'Operador').trim().split(' ');
                            const iniciais = (nomes[0][0] + (nomes.length > 1 ? nomes[nomes.length - 1][0] : '')).toUpperCase();

                            // Mapeamento de cores e labels do estado
                            const statusMap = {
                                ativo: { color: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400', dot: 'bg-emerald-500', label: 'Ativo' },
                                pendente: { color: 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400', dot: 'bg-amber-500', label: 'Pendente 2FA' },
                                bloqueado: { color: 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400', dot: 'bg-rose-500', label: 'Bloqueado' }
                            };

                            const st = statusMap[op.estado] || statusMap['pendente'];

                            return `
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition border-b border-gray-100 dark:border-zinc-800/50">
                                    <td class="p-4 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400 font-black flex items-center justify-center text-xs shrink-0">
                                            ${iniciais}
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-800 dark:text-white block text-sm">${op.nome}</span>
                                            <span class="text-[10px] text-gray-400 font-mono">NIP: ${op.nip || 'N/A'}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-md bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-bold text-[10px]">
                                            ${op.perfil_nome || 'Nível Operacional'}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex flex-col text-[11px] font-mono">
                                            <span class="text-gray-700 dark:text-zinc-200 font-bold">
                                                <i class="fa-solid fa-folder-open text-indigo-500 mr-1"></i> ${op.processos_hoje || 0} hoje
                                            </span>
                                            <span class="text-gray-400 text-[10px]">
                                                Total: ${op.total_processos || 0} proc.
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-black ${st.color}">
                                            <span class="w-1.5 h-1.5 rounded-full ${st.dot}"></span> ${st.label}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right space-x-1">
                                        <button onclick="revogarAcesso(${op.id}, '${op.nome}', '${op.estado}')" title="${op.estado === 'bloqueado' ? 'Desbloquear Acesso' : 'Revogar / Bloquear Acesso'}"
                                            class="p-1.5 text-gray-400 hover:text-rose-600 transition cursor-pointer">
                                            <i class="fa-solid ${op.estado === 'bloqueado' ? 'fa-user-check text-emerald-500' : 'fa-user-xmark'} text-sm"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        }

                        /**
                         * 5. Bloquear / Desbloquear Acesso do Operador (PATCH)
                         */
                        async function revogarAcesso(id, nome, estadoAtual) {
                            const novoEstado = estadoAtual === 'bloqueado' ? 'ativo' : 'bloqueado';
                            const acaoTexto = novoEstado === 'bloqueado' ? 'bloquear' : 'desbloquear';

                            const confirm = await SwalCustom.fire({
                                title: `Deseja ${acaoTexto} o acesso?`,
                                text: `Esta ação irá alterar o estado do operador ${nome}.`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: `Sim, ${acaoTexto}!`,
                                cancelButtonText: 'Cancelar'
                            });

                            if (confirm.isConfirmed) {
                                try {
                                    const resp = await fetch(`../controller/config/acessos.php?id=${id}`, {
                                        method: 'PATCH',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ estado: novoEstado })
                                    });

                                    const resData = await resp.json();

                                    if (resp.ok) {
                                        SwalCustom.fire('Concluído!', resData.mensagem || `Operador ${novoEstado} com sucesso.`, 'success');
                                        carregarOperadores();
                                    } else {
                                        throw new Error(resData.erro || 'Falha ao alterar estado.');
                                    }
                                } catch (err) {
                                    SwalCustom.fire('Erro!', err.message, 'error');
                                }
                            }
                        }

                    </script>

                    <!-- ========================================== -->
                    <!-- ABA 3: POLÍTICAS DE INSTRUÇÃO -->
                    <!-- ========================================== -->
                    <section id="tab-restricoes" class="tab-content hidden animate-fade-in space-y-6">
                        <div class="border-b border-gray-100 dark:border-zinc-800 pb-4">
                            <h3 class="text-base font-black text-gray-800 dark:text-white">Regras e Políticas de
                                Custódia</h3>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 font-semibold mt-0.5">
                                Configuração de prazos legais de instrução preparatória, retenção de provas e termos de
                                segredo de justiça.
                            </p>
                        </div>

                        <form id="form-politicas" onsubmit="salvarPoliticas(event);" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <!-- Card 1: Prazos e Alertas da Instrução -->
                                <div
                                    class="p-6 rounded-2xl bg-gray-50/50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400">
                                            <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                                        </div>
                                        <div>
                                            <h4
                                                class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">
                                                Prazos Processuais & Alertas
                                            </h4>
                                            <p class="text-[11px] text-gray-400 font-medium">Controlo automatizado de
                                                caducidade de prazos</p>
                                        </div>
                                    </div>

                                    <div class="space-y-3 pt-2">
                                        <div class="space-y-1">
                                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300">
                                                Prazo Máximo do Relatório Final de Instrução (Dias)
                                            </label>
                                            <input type="number" id="prazo_max_instrucao" value="90" min="1" required
                                                class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs font-semibold text-gray-800 dark:text-zinc-200 focus:outline-none focus:border-indigo-500">
                                        </div>

                                        <div class="space-y-1">
                                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300">
                                                Antecedência do Alerta de Expiração de Prazos (Dias)
                                            </label>
                                            <input type="number" id="antecedencia_alerta" value="15" min="1" required
                                                class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs font-semibold text-gray-800 dark:text-zinc-200 focus:outline-none focus:border-indigo-500">
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Prova Digital & Limites de Depósito -->
                                <div
                                    class="p-6 rounded-2xl bg-gray-50/50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="p-2.5 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                                            <i class="fa-solid fa-hard-drive text-lg"></i>
                                        </div>
                                        <div>
                                            <h4
                                                class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">
                                                Prova Digital & Arquivo
                                            </h4>
                                            <p class="text-[11px] text-gray-400 font-medium">Restrições de volume de
                                                upload e integridade</p>
                                        </div>
                                    </div>

                                    <div class="space-y-3 pt-2">
                                        <div class="space-y-1">
                                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300">
                                                Tamanho Máximo por Anexo Forense (MB / GB)
                                            </label>
                                            <select id="tamanho_max_anexo"
                                                class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs font-semibold text-gray-800 dark:text-zinc-200 focus:outline-none focus:border-indigo-500">
                                                <option value="500">500 MB (Documental / Imagens)</option>
                                                <option value="2000">2 GB (Análise Áudio / Vídeo)</option>
                                                <option value="10000">10 GB (Imagens Forenses Brutas)</option>
                                            </select>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300">
                                                Algoritmo de Hashing de Segurança
                                            </label>
                                            <input type="text" id="algoritmo_hash"
                                                value="SHA-256 (Padrão de Cadeia de Custódia)" readonly
                                                class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800/80 text-xs font-mono font-bold text-gray-500 dark:text-zinc-400 cursor-not-allowed">
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Opções de Segurança / Toggles -->
                            <div
                                class="p-6 rounded-2xl bg-gray-50/50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800 space-y-4">
                                <h4 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">
                                    Políticas de Segredo de Justiça & Autenticação
                                </h4>

                                <div class="space-y-3">
                                    <label
                                        class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-zinc-800 border border-gray-100 dark:border-zinc-700/60 cursor-pointer">
                                        <div>
                                            <span class="text-xs font-bold text-gray-800 dark:text-white block">
                                                Exigir Duplo Fator (2FA) para Investigadores
                                            </span>
                                            <span class="text-[10px] text-gray-400">
                                                Obrigatório para acesso a autos classificados sob Segredo de Justiça.
                                            </span>
                                        </div>
                                        <input type="checkbox" id="exigir_2fa"
                                            class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                    </label>

                                    <label
                                        class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-zinc-800 border border-gray-100 dark:border-zinc-700/60 cursor-pointer">
                                        <div>
                                            <span class="text-xs font-bold text-gray-800 dark:text-white block">
                                                Restringir Acesso Exclusivo à Rede Interna da Polícia (VPN/LAN)
                                            </span>
                                            <span class="text-[10px] text-gray-400">
                                                Bloqueia tentativas de login fora do perímetro seguro da unidade.
                                            </span>
                                        </div>
                                        <input type="checkbox" id="restringir_vpn"
                                            class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" id="btn-salvar-politicas"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold text-xs transition shadow-md shadow-indigo-100 dark:shadow-none cursor-pointer">
                                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Guardar Políticas de Instrução
                                </button>
                            </div>
                        </form>
                    </section>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            // Carrega as políticas salvas ao abrir a página
                            carregarPoliticas();
                        });

                        /**
                         * Busca as configurações da API e preenche os campos da aba
                         */
                        async function carregarPoliticas() {
                            try {
                                const resp = await fetch('../controller/config/politicas.php');
                                if (!resp.ok) throw new Error('Erro ao carregar dados do servidor');

                                const dados = await resp.json();

                                if (dados) {
                                    if (document.getElementById('prazo_max_instrucao')) {
                                        document.getElementById('prazo_max_instrucao').value = dados.prazo_max_instrucao || 90;
                                    }
                                    if (document.getElementById('antecedencia_alerta')) {
                                        document.getElementById('antecedencia_alerta').value = dados.antecedencia_alerta || 15;
                                    }
                                    if (document.getElementById('tamanho_max_anexo')) {
                                        document.getElementById('tamanho_max_anexo').value = dados.tamanho_max_anexo || 2000;
                                    }
                                    if (document.getElementById('exigir_2fa')) {
                                        document.getElementById('exigir_2fa').checked = parseInt(dados.exigir_2fa) === 1;
                                    }
                                    if (document.getElementById('restringir_vpn')) {
                                        document.getElementById('restringir_vpn').checked = parseInt(dados.restringir_vpn) === 1;
                                    }
                                }
                            } catch (err) {
                                console.error('Falha ao carregar políticas:', err);
                            }
                        }

                        /**
                         * Envia as políticas alteradas para a API
                         */
                        async function salvarPoliticas(e) {
                            e.preventDefault();

                            const btn = document.getElementById('btn-salvar-politicas');
                            const textoOriginal = btn.innerHTML;
                            btn.disabled = true;
                            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> a guardar...';

                            const payload = {
                                prazo_max_instrucao: document.getElementById('prazo_max_instrucao').value,
                                antecedencia_alerta: document.getElementById('antecedencia_alerta').value,
                                tamanho_max_anexo: document.getElementById('tamanho_max_anexo').value,
                                exigir_2fa: document.getElementById('exigir_2fa').checked ? 1 : 0,
                                restringir_vpn: document.getElementById('restringir_vpn').checked ? 1 : 0
                            };

                            try {
                                const resp = await fetch('../controller/config/politicas.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify(payload)
                                });

                                const resData = await resp.json();

                                if (resp.ok && resData.sucesso) {
                                    SwalCustom.fire({
                                        title: 'Sucesso!',
                                        text: resData.mensagem || 'Políticas de instrução salvas com sucesso.',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    throw new Error(resData.erro || 'Não foi possível salvar as configurações.');
                                }
                            } catch (err) {
                                SwalCustom.fire('Erro!', err.message, 'error');
                            } finally {
                                btn.disabled = false;
                                btn.innerHTML = textoOriginal;
                            }
                        }
                    </script>

                    <!-- ========================================== -->
                    <!-- ABA 4: SALVAGUARDA & AUDIT LOG -->
                    <!-- ========================================== -->
                    <section id="tab-backup" class="tab-content hidden animate-fade-in space-y-6">
                        <div class="border-b border-gray-100 dark:border-zinc-800 pb-4">
                            <h3 class="text-base font-black text-gray-800 dark:text-white">Cópias de Segurança & Registo
                                de Auditoria</h3>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 font-semibold mt-0.5">
                                Cópia integral do banco de dados para recuperação de desastres e registo imutável de
                                operações.
                            </p>
                        </div>

                        <!-- Status do Servidor de Backup -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                class="p-5 rounded-2xl bg-indigo-50/40 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/40 flex items-center gap-4">
                                <div class="p-3 rounded-xl bg-indigo-600 text-white shrink-0">
                                    <i class="fa-solid fa-server text-xl"></i>
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block">Servidor
                                        de Salvaguarda</span>
                                    <p id="txt-status-servidor" class="text-xs font-bold text-gray-800 dark:text-white">
                                        Ativo (Sincronizado)</p>
                                    <span id="txt-ultimo-backup" class="text-[10px] text-gray-400">Última cópia: A
                                        carregar...</span>
                                </div>
                            </div>

                            <div
                                class="p-5 rounded-2xl bg-emerald-50/40 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 flex items-center gap-4">
                                <div class="p-3 rounded-xl bg-emerald-600 text-white shrink-0">
                                    <i class="fa-solid fa-shield-check text-xl"></i>
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Integridade
                                        dos Autos</span>
                                    <p class="text-xs font-bold text-gray-800 dark:text-white">100% Verificado</p>
                                    <span class="text-[10px] text-gray-400">Pronto para Recuperação Total</span>
                                </div>
                            </div>

                            <div
                                class="p-5 rounded-2xl bg-gray-50/50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                                <div>
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Recuperação
                                        Total</span>
                                    <p class="text-xs font-bold text-gray-800 dark:text-white">Backup Geral do Banco</p>
                                    <span class="text-[10px] text-gray-400">Gerar `.sql` & Descarregar</span>
                                </div>
                                <button id="btn-executar-backup" onclick="solicitarBackupManual(event)"
                                    class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-indigo-100 dark:shadow-none cursor-pointer flex items-center gap-1.5">
                                    <i class="fa-solid fa-download"></i> Executar
                                </button>
                            </div>
                        </div>

                        <!-- Tabela de Audit Log -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">
                                    Registo de Auditoria de Acessos e Alterações (Audit Log)
                                </h4>
                                <span class="text-[10px] font-mono text-gray-400">
                                    <i class="fa-solid fa-lock mr-1"></i> Registo Imutável SHA-256
                                </span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-zinc-800">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-gray-50/80 dark:bg-zinc-800/60 border-b border-gray-100 dark:border-zinc-800 text-gray-500 dark:text-zinc-400 font-black uppercase text-[10px] tracking-wider">
                                            <th class="p-3.5">Data / Hora</th>
                                            <th class="p-3.5">Operador</th>
                                            <th class="p-3.5">Ação Realizada</th>
                                            <th class="p-3.5">Endereço IP</th>
                                            <th class="p-3.5 text-right">Resultado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-audit-logs"
                                        class="divide-y divide-gray-100 dark:divide-zinc-800 font-mono text-[11px] text-gray-700 dark:text-zinc-300">
                                        <tr>
                                            <td colspan="5" class="p-4 text-center text-gray-400 font-sans">A carregar
                                                registos de auditoria...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            carregarDadosSalvaguarda();
                        });

                        /**
                         * Procura os dados atuais do painel de salvaguarda e a tabela de auditoria
                         */
                        async function carregarDadosSalvaguarda() {
                            try {
                                const resp = await fetch('../controller/config/salvaguarda.php');
                                if (!resp.ok) throw new Error('Erro ao ligar ao servidor');

                                const dados = await resp.json();

                                if (document.getElementById('txt-ultimo-backup')) {
                                    document.getElementById('txt-ultimo-backup').textContent = 'Última cópia: ' + (dados.ultimo_backup || 'N/A');
                                }
                                if (document.getElementById('txt-status-servidor')) {
                                    document.getElementById('txt-status-servidor').textContent = dados.status_servidor || 'Ativo';
                                }

                                renderizarTabelaAuditLogs(dados.logs || []);

                            } catch (err) {
                                console.error('Falha ao carregar salvaguarda:', err);
                                const tbody = document.getElementById('tbody-audit-logs');
                                if (tbody) {
                                    tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-red-500 font-sans">Erro ao carregar o histórico de auditoria.</td></tr>`;
                                }
                            }
                        }

                        /**
                         * Renderiza as linhas do Audit Log
                         */
                        function renderizarTabelaAuditLogs(logs) {
                            const tbody = document.getElementById('tbody-audit-logs');
                            if (!tbody) return;

                            if (logs.length === 0) {
                                tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-gray-400 font-sans">Nenhum registo de auditoria encontrado.</td></tr>`;
                                return;
                            }

                            tbody.innerHTML = logs.map(log => {
                                let badgeResultado = log.resultado === 'SUCESSO'
                                    ? `<span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">SUCESSO</span>`
                                    : `<span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400">ERRO</span>`;

                                return `
            <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition">
                <td class="p-3.5 text-gray-400">${log.data_formatada}</td>
                <td class="p-3.5 font-sans font-bold text-gray-800 dark:text-zinc-200">${escapeHtml(log.operador)}</td>
                <td class="p-3.5 font-sans">${escapeHtml(log.acao)}</td>
                <td class="p-3.5 text-gray-400">${log.ip}</td>
                <td class="p-3.5 text-right">${badgeResultado}</td>
            </tr>
        `;
                            }).join('');
                        }

                        /**
                         * Confirmação antes de iniciar o Backup Geral
                         */
                        function solicitarBackupManual(e) {
                            e.preventDefault();
                            const SwalApi = typeof SwalCustom !== 'undefined' ? SwalCustom : Swal;

                            SwalApi.fire({
                                title: 'Gerar Backup Completo do Banco?',
                                text: 'Será criada uma cópia integral de TODAS as tabelas e dados para recuperação de desastres. O ficheiro .sql será transferido automaticamente.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Sim, Exportar e Descarregar',
                                cancelButtonText: 'Cancelar'
                            }).then(async (result) => {
                                if (result.isConfirmed) {
                                    await executarBackupBD();
                                }
                            });
                        }

                        /**
                         * Chama o backend para criar o dump .sql e força o download no PC do utilizador
                         */
                        async function executarBackupBD() {
                            const btn = document.getElementById('btn-executar-backup');
                            const textoOriginal = btn.innerHTML;
                            btn.disabled = true;
                            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> A processar dump...';

                            const SwalApi = typeof SwalCustom !== 'undefined' ? SwalCustom : Swal;

                            try {
                                const resp = await fetch('../controller/config/salvaguarda.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' }
                                });

                                const resData = await resp.json();

                                if (resp.ok && resData.sucesso) {
                                    SwalApi.fire({
                                        title: 'Backup Gerado!',
                                        text: `O ficheiro (${resData.tamanho}) foi guardado no servidor e o download no seu computador começará de imediato.`,
                                        icon: 'success',
                                        timer: 3500,
                                        showConfirmButton: false
                                    });

                                    // 🚀 DISPARA O DOWNLOAD AUTOMÁTICO DO FICHEIRO .SQL NO BROWSER
                                    if (resData.ficheiro) {
                                        const downloadUrl = '../controller/config/salvaguarda.php?download=' + encodeURIComponent(resData.ficheiro);
                                        window.location.href = downloadUrl;
                                    }

                                    // Recarrega os dados do painel e a tabela de logs
                                    await carregarDadosSalvaguarda();
                                } else {
                                    throw new Error(resData.erro || 'Falha ao processar o dump do banco.');
                                }
                            } catch (err) {
                                SwalApi.fire('Erro!', err.message, 'error');
                            } finally {
                                btn.disabled = false;
                                btn.innerHTML = textoOriginal;
                            }
                        }

                        function escapeHtml(text) {
                            if (!text) return '';
                            return text
                                .replace(/&/g, "&amp;")
                                .replace(/</g, "&lt;")
                                .replace(/>/g, "&gt;")
                                .replace(/"/g, "&quot;")
                                .replace(/'/g, "&#039;");
                        }

                    </script>

                    <!-- ========================================== -->
                    <!-- ABA 5: GESTÃO DE TABELAS AUXILIARES -->
                    <!-- ========================================== -->
                    <section id="tab-auxiliares" class="tab-content hidden animate-fade-in space-y-6">
                        <div
                            class="border-b border-gray-100 dark:border-zinc-800 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h3 class="text-base font-black text-gray-800 dark:text-white">Gestão de Tabelas
                                    Auxiliares</h3>
                                <p class="text-xs text-gray-500 dark:text-zinc-400 font-semibold mt-0.5">
                                    Parâmetros mestres do sistema para padronização de inquéritos, registos de
                                    indivíduos e autos.
                                </p>
                            </div>
                            <button onclick="abrirModalNovaTabelaAuxiliar()"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-bold text-xs transition shadow-md shadow-indigo-100 dark:shadow-none cursor-pointer flex items-center gap-2 self-start">
                                <i class="fa-solid fa-plus"></i> Novo Registo Auxiliar
                            </button>
                        </div>

                        <!-- Grelha de Cartões das Tabelas Auxiliares -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Card 1: Profissões -->
                            <div
                                class="p-6 rounded-2xl bg-gray-50/50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800 space-y-4 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                                                <i class="fa-solid fa-briefcase text-lg"></i>
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">
                                                    Profissões & Ocupações</h4>
                                                <p class="text-[11px] text-gray-400 font-medium">Catálogo ativo de
                                                    atividades profissionais</p>
                                            </div>
                                        </div>
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">                                            Registados</span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-zinc-300">
                                        Utilizado na qualificação de arguidos, testemunhas e declarantes nos autos de
                                        notícia e interrogatórios.
                                    </p>
                                </div>
                                <div
                                    class="pt-4 border-t border-gray-100 dark:border-zinc-700/60 flex items-center justify-between">
                                    <span class="text-[10px] text-gray-400 font-mono">Última atualização: Ontem</span>
                                    <button onclick="gerirTabelaAuxiliar('profissoes')"
                                        class="px-3 py-1.5 rounded-xl bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-xs font-bold text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-700 transition cursor-pointer">
                                        <i class="fa-solid fa-gear mr-1"></i> Gerir Dados
                                    </button>
                                </div>
                            </div>

                            <!-- Card 2: Nacionalidades -->
                            <div
                                class="p-6 rounded-2xl bg-gray-50/50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800 space-y-4 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                                                <i class="fa-solid fa-earth-americas text-lg"></i>
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">
                                                    Nacionalidades & Países</h4>
                                                <p class="text-[11px] text-gray-400 font-medium">Listagem oficial de
                                                    estados e passaportes</p>
                                            </div>
                                        </div>
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                                            Países</span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-zinc-300">
                                        Parâmetro essencial para controlo migratório, cooperação policial internacional
                                        e registo de estrangeiros.
                                    </p>
                                </div>
                                <div
                                    class="pt-4 border-t border-gray-100 dark:border-zinc-700/60 flex items-center justify-between">
                                    <span class="text-[10px] text-gray-400 font-mono">Última atualização:
                                        10/08/2026</span>
                                    <button onclick="gerirTabelaAuxiliar('nacionalidades')"
                                        class="px-3 py-1.5 rounded-xl bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-xs font-bold text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-700 transition cursor-pointer">
                                        <i class="fa-solid fa-gear mr-1"></i> Gerir Dados
                                    </button>
                                </div>
                            </div>

                            <!-- Card 3: Tipos de Crime -->
                            <div
                                class="p-6 rounded-2xl bg-gray-50/50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800 space-y-4 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                                                <i class="fa-solid fa-scale-balanced text-lg"></i>
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">
                                                    Tipos de Crime & Incidências</h4>
                                                <p class="text-[11px] text-gray-400 font-medium">Tipificações legais e
                                                    artigos do código</p>
                                            </div>
                                        </div>
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                                            Tipos</span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-zinc-300">
                                        Classificação jurídica dos ilícitos penais para geração automática de
                                        estatísticas criminais e relatórios à PGR.
                                    </p>
                                </div>
                                <div
                                    class="pt-4 border-t border-gray-100 dark:border-zinc-700/60 flex items-center justify-between">
                                    <span class="text-[10px] text-gray-400 font-mono">Última atualização:
                                        05/08/2026</span>
                                    <button onclick="gerirTabelaAuxiliar('crimes')"
                                        class="px-3 py-1.5 rounded-xl bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-xs font-bold text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-700 transition cursor-pointer">
                                        <i class="fa-solid fa-gear mr-1"></i> Gerir Dados
                                    </button>
                                </div>
                            </div>

                            <!-- Card 4: Cadeias / Estabelecimentos Prisionais -->
                            <div
                                class="p-6 rounded-2xl bg-gray-50/50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800 space-y-4 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400">
                                                <i class="fa-solid fa-building-shield text-lg"></i>
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">
                                                    Estabelecimentos Prisionais</h4>
                                                <p class="text-[11px] text-gray-400 font-medium">Cadeias e centros de
                                                    detenção vinculados</p>
                                            </div>
                                        </div>
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400">
                                            Unidades</span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-zinc-300">
                                        Gestão dos locais de encaminhamento de detidos, mandados de condução e ordens de
                                        soltura ou reclusão preventiva.
                                    </p>
                                </div>
                                <div
                                    class="pt-4 border-t border-gray-100 dark:border-zinc-700/60 flex items-center justify-between">
                                    <span class="text-[10px] text-gray-400 font-mono">Última atualização:
                                        20/07/2026</span>
                                    <button onclick="gerirTabelaAuxiliar('cadeias')"
                                        class="px-3 py-1.5 rounded-xl bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-xs font-bold text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-700 transition cursor-pointer">
                                        <i class="fa-solid fa-gear mr-1"></i> Gerir Dados
                                    </button>
                                </div>
                            </div>

                        </div>
                    </section>

                </div>
            </main>

            <script>
                /**
                 * Alterna entre as abas ativas do Painel de Controlo
                 * @param {string} tabId - O ID da seção a ser exibida (ex: 'tab-instituicao')
                 */
                function switchTab(tabId) {
                    // 1. Esconder todas as seções de conteúdo
                    const contents = document.querySelectorAll('.tab-content');
                    contents.forEach(content => {
                        content.classList.add('hidden');
                        content.classList.remove('block');
                    });

                    // 2. Resetar estilos de todos os botões de aba para o estado INATIVO
                    const buttons = document.querySelectorAll('.tab-btn');
                    buttons.forEach(btn => {
                        // Remove classes ativas
                        btn.classList.remove(
                            'border-indigo-600',
                            'text-indigo-600',
                            'dark:text-indigo-400',
                            'font-black',
                            'bg-indigo-50/40',
                            'dark:bg-indigo-950/30'
                        );
                        // Adiciona classes inativas
                        btn.classList.add(
                            'border-transparent',
                            'text-gray-500',
                            'dark:text-zinc-400',
                            'font-bold'
                        );
                    });

                    // 3. Exibir o conteúdo da aba selecionada
                    const targetContent = document.getElementById(tabId);
                    if (targetContent) {
                        targetContent.classList.remove('hidden');
                        targetContent.classList.add('block');
                    }

                    // 4. Aplicar estilo ATIVO no botão correspondente
                    const targetButton = document.getElementById(`btn-${tabId}`);
                    if (targetButton) {
                        // Remove classes inativas
                        targetButton.classList.remove(
                            'border-transparent',
                            'text-gray-500',
                            'dark:text-zinc-400',
                            'font-bold'
                        );
                        // Adiciona classes ativas
                        targetButton.classList.add(
                            'border-indigo-600',
                            'text-indigo-600',
                            'dark:text-indigo-400',
                            'font-black',
                            'bg-indigo-50/40',
                            'dark:bg-indigo-950/30'
                        );

                        // Centraliza suavemente o botão na tela caso esteja num dispositivo móvel
                        targetButton.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    }
                }

                // ==========================================
                // GESTÃO DE TABELAS AUXILIARES VIA SWEETALERT2 (API REAL)
                // ==========================================

                // Cache em memória apenas para a sessão ativa no modal
                let dadosCacheLocal = [];

                // Dicionário de títulos para os modais
                const titulosTabelas = {
                    profissoes: 'Profissões & Ocupações',
                    nacionalidades: 'Nacionalidades & Países',
                    crimes: 'Tipos de Crime & Incidências',
                    cadeias: 'Estabelecimentos Prisionais'
                };

                // Mapeamento caso os nomes no frontend difiram das tabelas no MySQL
                function obterNomeTabelaBD(tipo) {
                    const mapa = {
                        profissoes: 'profissoes',
                        nacionalidades: 'nacionalidades',
                        crimes: 'tipos_crime',
                        cadeias: 'cadeias'
                    };
                    return mapa[tipo] || tipo;
                }

                // Helper para notificações Toast
                function mostrarToastSucesso(mensagem) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: mensagem,
                        showConfirmButton: false,
                        timer: 2000,
                        customClass: { popup: 'rounded-2xl dark:bg-zinc-800 dark:text-white text-xs font-bold' }
                    });
                }

                // 1. Função principal disparada pelo botão "Gerir Dados"
                async function gerirTabelaAuxiliar(tipo) {
                    const tabelaBD = obterNomeTabelaBD(tipo);
                    const titulo = titulosTabelas[tipo] || 'Gestão de Registos';

                    try {
                        // Carrega dados em tempo real da base de dados PHP/MySQL
                        const res = await fetch(`../controller/config/api_auxiliares.php?acao=listar&tabela=${tabelaBD}`);
                        const data = await res.json();

                        if (!data.sucesso) {
                            Swal.fire('Erro', data.erro || 'Não foi possível carregar os dados.', 'error');
                            return;
                        }

                        dadosCacheLocal = data.registos; // Atualiza a cache com a resposta do servidor

                        let htmlConteudo = `
            <div class="text-left space-y-4">
                <!-- Barra de Ações Superior (Pesquisa + Novo) -->
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" id="swal-input-pesquisa" onkeyup="filtrarTabelaSwal('${tipo}')" 
                            placeholder="Pesquisar registo..." 
                            class="w-full pl-9 pr-4 py-2 text-xs bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <button onclick="adicionarRegistoAuxiliar('${tipo}')" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                        <i class="fa-solid fa-plus"></i> Novo
                    </button>
                </div>

                <!-- Listagem em Cartões Compactos -->
                <div id="swal-lista-container" class="max-h-64 overflow-y-auto space-y-2 pr-1">
                    ${gerarHtmlLista(dadosCacheLocal, tipo)}
                </div>
            </div>
        `;

                        await Swal.fire({
                            title: `<span class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-wider">${titulo}</span>`,
                            html: htmlConteudo,
                            showConfirmButton: false,
                            showCloseButton: true,
                            customClass: {
                                popup: 'rounded-3xl dark:bg-zinc-900 border dark:border-zinc-800 p-6 shadow-2xl',
                                closeButton: 'text-gray-400 hover:text-gray-600 dark:hover:text-white focus:outline-none'
                            },
                            width: '550px'
                        });

                    } catch (e) {
                        Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
                    }
                }

                // 2. Gera o HTML interno da lista para o SweetAlert
                function gerarHtmlLista(itens, tipo) {
                    if (!itens || itens.length === 0) {
                        return `
            <div class="text-center py-8 text-gray-400 dark:text-zinc-500 text-xs font-medium">
                <i class="fa-solid fa-folder-open text-2xl mb-2 block opacity-40"></i>
                Nenhum registo encontrado nesta tabela.
            </div>
        `;
                    }

                    return itens.map(item => `
        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-700/60 hover:border-indigo-200 dark:hover:border-zinc-600 transition">
            <span class="text-xs font-bold text-gray-700 dark:text-zinc-200 truncate pr-2">${item.nome}</span>
            <div class="flex items-center gap-1 shrink-0">
                <button onclick="editarRegistoAuxiliar('${tipo}', ${item.id})" 
                    class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 hover:bg-blue-100 transition cursor-pointer" title="Editar">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                </button>
                <button onclick="eliminarRegistoAuxiliar('${tipo}', ${item.id})" 
                    class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition cursor-pointer" title="Eliminar">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </div>
        </div>
    `).join('');
                }

                // 3. Filtro dinâmico na barra de pesquisa do SweetAlert
                function filtrarTabelaSwal(tipo) {
                    const termo = document.getElementById('swal-input-pesquisa').value.toLowerCase();
                    const itensFiltrados = dadosCacheLocal.filter(item => item.nome.toLowerCase().includes(termo));
                    document.getElementById('swal-lista-container').innerHTML = gerarHtmlLista(itensFiltrados, tipo);
                }

                // 4. Adicionar Novo Registo no Banco de Dados
                async function adicionarRegistoAuxiliar(tipo) {
                    const tabelaBD = obterNomeTabelaBD(tipo);

                    const { value: novoNome } = await Swal.fire({
                        title: '<span class="text-xs font-black uppercase tracking-wider">Adicionar Novo Registo</span>',
                        input: 'text',
                        inputPlaceholder: 'Digite o nome do registo...',
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            popup: 'rounded-3xl dark:bg-zinc-900 border dark:border-zinc-800 p-6',
                            confirmButton: 'bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-none',
                            cancelButton: 'bg-gray-200 text-gray-700 dark:bg-zinc-800 dark:text-zinc-300 px-4 py-2 rounded-xl text-xs font-bold shadow-none',
                            input: 'text-xs rounded-xl dark:bg-zinc-800 dark:text-white border-gray-200 dark:border-zinc-700'
                        },
                        inputValidator: (value) => {
                            if (!value.trim()) {
                                return 'O campo não pode estar vazio!';
                            }
                        }
                    });

                    if (novoNome) {
                        const formData = new FormData();
                        formData.append('tabela', tabelaBD);
                        formData.append('nome', novoNome.trim());

                        try {
                            const res = await fetch('../controller/config/api_auxiliares.php?acao=salvar', {
                                method: 'POST',
                                body: formData
                            });
                            const data = await res.json();

                            if (data.sucesso) {
                                mostrarToastSucesso('Registo adicionado com sucesso!');
                                gerirTabelaAuxiliar(tipo); // Reabre e recarrega a modal atualizada
                            } else {
                                Swal.fire('Erro', data.erro || 'Erro ao salvar no servidor.', 'error');
                            }
                        } catch (e) {
                            Swal.fire('Erro', 'Falha ao conectar com o banco de dados.', 'error');
                        }
                    }
                }

                // 5. Editar Registo Existente no Banco de Dados
                async function editarRegistoAuxiliar(tipo, id) {
                    const tabelaBD = obterNomeTabelaBD(tipo);
                    const item = dadosCacheLocal.find(i => i.id == id);
                    if (!item) return;

                    const { value: nomeAtualizado } = await Swal.fire({
                        title: '<span class="text-xs font-black uppercase tracking-wider">Editar Registo</span>',
                        input: 'text',
                        inputValue: item.nome,
                        showCancelButton: true,
                        confirmButtonText: 'Atualizar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            popup: 'rounded-3xl dark:bg-zinc-900 border dark:border-zinc-800 p-6',
                            confirmButton: 'bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-none',
                            cancelButton: 'bg-gray-200 text-gray-700 dark:bg-zinc-800 dark:text-zinc-300 px-4 py-2 rounded-xl text-xs font-bold shadow-none',
                            input: 'text-xs rounded-xl dark:bg-zinc-800 dark:text-white border-gray-200 dark:border-zinc-700'
                        },
                        inputValidator: (value) => {
                            if (!value.trim()) {
                                return 'O campo não pode estar vazio!';
                            }
                        }
                    });

                    if (nomeAtualizado) {
                        const formData = new FormData();
                        formData.append('tabela', tabelaBD);
                        formData.append('id', id);
                        formData.append('nome', nomeAtualizado.trim());

                        try {
                            const res = await fetch('../controller/config/api_auxiliares.php?acao=salvar', {
                                method: 'POST',
                                body: formData
                            });
                            const data = await res.json();

                            if (data.sucesso) {
                                mostrarToastSucesso('Registo atualizado com sucesso!');
                                gerirTabelaAuxiliar(tipo);
                            } else {
                                Swal.fire('Erro', data.erro || 'Erro ao atualizar.', 'error');
                            }
                        } catch (e) {
                            Swal.fire('Erro', 'Falha de comunicação ao atualizar.', 'error');
                        }
                    }
                }

                // 6. Eliminar Registo no Banco de Dados
                async function eliminarRegistoAuxiliar(tipo, id) {
                    const tabelaBD = obterNomeTabelaBD(tipo);

                    const confirmacao = await Swal.fire({
                        title: 'Tem a certeza?',
                        text: "Esta ação removerá o registo permanentemente da tabela auxiliar.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, eliminar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            popup: 'rounded-3xl dark:bg-zinc-900 border dark:border-zinc-800 p-6',
                            confirmButton: 'bg-rose-600 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-none',
                            cancelButton: 'bg-gray-200 text-gray-700 dark:bg-zinc-800 dark:text-zinc-300 px-4 py-2 rounded-xl text-xs font-bold shadow-none'
                        }
                    });

                    if (confirmacao.isConfirmed) {
                        const formData = new FormData();
                        formData.append('tabela', tabelaBD);
                        formData.append('id', id);

                        try {
                            const res = await fetch('../controller/config/api_auxiliares.php?acao=eliminar', {
                                method: 'POST',
                                body: formData
                            });
                            const data = await res.json();

                            if (data.sucesso) {
                                mostrarToastSucesso('Registo eliminado com sucesso!');
                                gerirTabelaAuxiliar(tipo);
                            } else {
                                Swal.fire('Erro', data.erro || 'Não foi possível eliminar o registo.', 'error');
                            }
                        } catch (e) {
                            Swal.fire('Erro', 'Erro ao processar remoção.', 'error');
                        }
                    }
                }

            </script>

            <!-- ======================================================= -->
            <!-- MODAL DE EDIÇÃO: DADOS DA INSTITUIÇÃO                  -->
            <!-- ======================================================= -->
            <div id="modal-instituicao"
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden animate-fade-in">
                <div
                    class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden max-h-[90vh]">

                    <!-- Cabeçalho do Modal -->
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-university text-indigo-600 text-sm"></i>
                            <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider">Editar Perfil
                                Institucional</h4>
                        </div>
                        <button onclick="closeModal('modal-instituicao')"
                            class="text-gray-400 hover:text-gray-600 transition text-lg"><i
                                class="fa-solid fa-xmark"></i></button>
                    </div>

                    <!-- Formulário Interno com Scroll Autônomo se necessário -->
                    <form onsubmit="saveInstituicaoModal(event)" class="p-6 space-y-5 overflow-y-auto flex-1">

                        <!-- Upload de Logótipo -->
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Logótipo
                                da Instituição (Formatos: PNG, SVG)</label>
                            <div
                                class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-indigo-400 transition cursor-pointer bg-gray-50/30 flex items-center justify-center gap-3">
                                <i class="fa-solid fa-cloud-arrow-up text-gray-400 text-lg"></i>
                                <div class="text-left">
                                    <span class="text-xs font-bold text-gray-700 block">Clique para carregar ou
                                        arraste</span>
                                    <span class="text-[10px] text-gray-400 font-medium">Tamanho máximo recomendado:
                                        2MB</span>
                                </div>
                                <input type="file" class="hidden">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="space-y-1.5 sm:col-span-2">
                                <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Nome
                                    da Instituição *</label>
                                <input type="text" id="input-nome" value="Universidade Europeia de Tecnologia" required
                                    class="w-full px-3 py-2.5 bg-gray-50/50 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 focus:outline-none focus:border-indigo-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Sigla
                                    *</label>
                                <input type="text" id="input-sigla" value="UETEC" required
                                    class="w-full px-3 py-2.5 bg-gray-50/50 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 focus:outline-none focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">NIF /
                                    Número Fiscal *</label>
                                <input type="text" id="input-nif" value="500123456" required
                                    class="w-full px-3 py-2.5 bg-gray-50/50 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 focus:outline-none focus:border-indigo-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label
                                    class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">E-mail
                                    da Secretaria *</label>
                                <input type="email" id="input-email" value="secretaria.tcc@uetec.edu" required
                                    class="w-full px-3 py-2.5 bg-gray-50/50 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 focus:outline-none focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Faculdade
                                / Departamento Responsável *</label>
                            <input type="text" id="input-depto"
                                value="Faculdade de Ciências Engenharia e Sistemas Computacionais" required
                                class="w-full px-3 py-2.5 bg-gray-50/50 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="space-y-1.5 sm:col-span-2">
                                <label
                                    class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Endereço</label>
                                <input type="text" id="input-endereco"
                                    value="Avenida do Conhecimento, Bloco C, Campus Central"
                                    class="w-full px-3 py-2.5 bg-gray-50/50 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 focus:outline-none focus:border-indigo-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label
                                    class="block text-[11px] font-black text-gray-500 uppercase tracking-wider">Telefone</label>
                                <input type="text" id="input-telefone" value="+351 210 000 000"
                                    class="w-full px-3 py-2.5 bg-gray-50/50 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 focus:outline-none focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <!-- Rodapé do Modal (Ações) -->
                        <div class="pt-4 border-t border-gray-100 flex justify-end gap-2.5">
                            <button type="button" onclick="closeModal('modal-instituicao')"
                                class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-500 hover:text-gray-700 text-xs font-bold transition">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs transition shadow-md shadow-indigo-100">
                                Guardar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ======================================================= -->
            <!-- SCRIPTS ADICIONAIS DE CONTROLO DO MODAL                 -->
            <!-- ======================================================= -->
            <script>
                // Controladores de Estado do Modal
                function openModal(modalId) {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden'); // Trava scroll do fundo
                    }
                }

                function closeModal(modalId) {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    }
                }

                // Salvar Dados do Modal e Atualizar o Painel de Visualização
                function saveInstituicaoModal(event) {
                    event.preventDefault();

                    // 1. Captura os novos valores digitados
                    const nome = document.getElementById('input-nome').value;
                    const sigla = document.getElementById('input-sigla').value;
                    const nif = document.getElementById('input-nif').value;
                    const depto = document.getElementById('input-depto').value;
                    const email = document.getElementById('input-email').value;
                    const endereco = document.getElementById('input-endereco').value;
                    const telefone = document.getElementById('input-telefone').value;

                    // 2. Injeta de volta nas tags de visualização da tela base
                    document.getElementById('view-nome').innerText = nome;
                    document.getElementById('view-sigla').innerText = sigla;
                    document.getElementById('view-nif').innerText = nif;
                    document.getElementById('view-depto').innerText = depto;
                    document.getElementById('view-email').innerText = email;
                    document.getElementById('view-endereco').innerText = endereco;
                    document.getElementById('view-telefone').innerText = telefone;

                    // 3. Fecha o modal de forma limpa
                    closeModal('modal-instituicao');
                    alert('Perfil institucional atualizado com sucesso no painel administrativo!');
                }

                // [Mantenha a sua função switchTab() original aqui abaixo]
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