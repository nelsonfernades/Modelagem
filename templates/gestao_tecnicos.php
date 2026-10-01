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
                                class="relative flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600/10 text-white transition-all">
                                <div class="absolute left-0 w-1 h-6 bg-blue-400 rounded-r-full"></div>
                                <i class="fa-solid fa-user-group w-5  text-blue-400 transition-colors"></i>
                                <span class="text-sm font-semibold tracking-wide text-blue-400">Gestão de técnicos</span>
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

            <main class="p-6">
                <!-- TÍTULO DA PÁGINA -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Vincular Usuários</h2>
                        <p class="text-sm text-gray-500">Gerencie as permissões, atribuições de papéis e conexões entre
                            os utilizadores do sistema.</p>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="openEstudanteModal()"
                            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-2xl transition shadow-lg shadow-indigo-100 font-bold text-sm">
                            <i class="fa-solid fa-user-plus"></i> Novo Vínculo de Usuário
                        </button>
                    </div>
                </div>

                <!-- FILTROS -->
                <div
                    class="bg-white dark:bg-zinc-900 p-4 rounded-3xl border border-gray-100 dark:border-zinc-800 shadow-sm mb-6 grid grid-cols-1 lg:grid-cols-4 gap-4">
                    <!-- Campo de Pesquisa -->
                    <div class="relative lg:col-span-2">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" id="filtroBusca" placeholder="Pesquisar por nome, e-mail, NIP ou perfil..."
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-zinc-800/60 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition text-gray-800 dark:text-zinc-100">
                    </div>

                    <!-- Select de Perfis -->
                    <select id="filtroPerfil"
                        class="bg-gray-50 dark:bg-zinc-800/60 border-none rounded-2xl px-4 py-3 text-sm font-bold text-gray-500 dark:text-zinc-400 outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos os Perfis</option>
                        <?php
                        // Preenchimento dinâmico dos perfis via PHP
                        if (isset($resPerfis) && $resPerfis->num_rows > 0) {
                            while ($perfil = $resPerfis->fetch_assoc()) {
                                echo '<option value="' . $perfil['id'] . '">' . htmlspecialchars($perfil['nome']) . '</option>';
                            }
                        }
                        ?>
                    </select>

                    <!-- Select de Status -->
                    <select id="filtroStatus"
                        class="bg-gray-50 dark:bg-zinc-800/60 border-none rounded-2xl px-4 py-3 text-sm font-bold text-gray-500 dark:text-zinc-400 outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos os Status</option>
                        <option value="ativo">Ativo</option>
                        <option value="pendente">Pendente de Confirmação</option>
                        <option value="bloqueado">Suspenso / Bloqueado</option>
                    </select>
                </div>

                <!-- CONTAINER DOS CARDS DINÂMICOS -->
                <div id="cardsContainer" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    <!-- Os cards serão injetados aqui via JavaScript -->
                </div>

                <!-- CONTAINER DA PAGINAÇÃO ESTILO iOS -->
                <div class="mt-8 flex items-center justify-center">
                    <nav id="paginacaoContainer" aria-label="Navegação de Páginas"
                        class="inline-flex items-center gap-1.5 p-1.5 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 rounded-full shadow-xl shadow-zinc-950/5 transition-all">
                        <!-- A paginação será gerada dinamicamente aqui -->
                    </nav>
                </div>

            </main>
        </div>
    </div>

    <script>
        // Estado global da busca e paginação
        let paginaAtual = 1;
        let debounceTimer = null;

        // Inicializa no carregamento do DOM
        document.addEventListener('DOMContentLoaded', () => {
            carregarUtilizadores();

            // Event Listener para a busca com Debounce (aguarda 400ms após digitação)
            document.getElementById('filtroBusca').addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    paginaAtual = 1; // Reseta para a primeira página
                    carregarUtilizadores();
                }, 400);
            });

            // Event Listeners nos Selects
            document.getElementById('filtroPerfil').addEventListener('change', () => {
                paginaAtual = 1;
                carregarUtilizadores();
            });

            document.getElementById('filtroStatus').addEventListener('change', () => {
                paginaAtual = 1;
                carregarUtilizadores();
            });

            // Evento global para fechar menus dropdown ao clicar fora
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.group')) {
                    document.querySelectorAll('[id^="menu-est-"]').forEach(el => el.classList.add('hidden'));
                }
            });
        });

        /**
         * Busca os dados via fetch e renderiza a interface
         */
        async function carregarUtilizadores(pagina = paginaAtual) {
            paginaAtual = pagina;
            const busca = document.getElementById('filtroBusca').value;
            const perfil = document.getElementById('filtroPerfil').value;
            const estado = document.getElementById('filtroStatus').value;

            const cardsContainer = document.getElementById('cardsContainer');

            // Skeleton / State de Carregamento
            cardsContainer.innerHTML = `
                <div class="col-span-full flex flex-col items-center justify-center py-12 text-gray-400">
                    <i class="fa-solid fa-spinner fa-spin text-3xl text-indigo-500 mb-3"></i>
                    <p class="text-xs font-bold">A carregar utilizadores...</p>
                </div>
            `;

            try {
                const url = `../controller/users/listar_utilizadores.php?pagina=${pagina}&busca=${encodeURIComponent(busca)}&perfil=${encodeURIComponent(perfil)}&estado=${encodeURIComponent(estado)}`;
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    listaUtilizadoresAtuais = result.dados;

                    renderizarCards(result.dados);
                    renderizarPaginacao(result.paginacao);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: result.message,
                        confirmColor: '#4f46e5'
                    });
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                cardsContainer.innerHTML = `
                    <div class="col-span-full text-center py-12 text-red-500 font-bold text-sm">
                        Não foi possível carregar os dados. Verifique a conexão com o servidor.
                    </div>
                `;
            }
        }

        /**
         * Renderiza os Cards dos Utilizadores
         */
        function renderizarCards(dados) {
            const cardsContainer = document.getElementById('cardsContainer');
            cardsContainer.innerHTML = '';

            if (dados.length === 99) {
                cardsContainer.innerHTML = `
                    <div class="col-span-full text-center py-12 bg-white dark:bg-zinc-900 rounded-[32px] border border-gray-100 dark:border-zinc-800">
                        <i class="fa-solid fa-user-slash text-4xl text-gray-300 dark:text-zinc-600 mb-3"></i>
                        <p class="text-sm font-bold text-gray-600 dark:text-zinc-400">Nenhum utilizador encontrado com os filtros aplicados.</p>
                    </div>
                `;
                return;
            }

            dados.forEach(user => {
                let statusBadge = '';
                if (user.estado === 'ativo') {
                    statusBadge = `<span class="bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 px-2 py-1 rounded-lg text-[9px] font-black uppercase">Ativo</span>`;
                } else if (user.estado === 'pendente') {
                    statusBadge = `<span class="bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 px-2 py-1 rounded-lg text-[9px] font-black uppercase">Pendente</span>`;
                } else {
                    statusBadge = `<span class="bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 px-2 py-1 rounded-lg text-[9px] font-black uppercase">Bloqueado</span>`;
                }

                const foto = user.foto_url ? user.foto_url : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.nome)}&background=6366f1&color=fff`;
                const userJson = JSON.stringify(user).replace(/'/g, "&apos;");

                // --- BOTÃO DINÂMICO DE AÇÃO (REVOGAR OU ATIVAR) ---
                let botaoAcaoStatus = '';
                if (user.estado === 'bloqueado') {
                    // Se estiver bloqueado -> Mostra "Ativar Acesso"
                    botaoAcaoStatus = `
                    <button onclick="confirmarAlteracaoStatus(${user.id}, '${user.nome}', 'ativar')"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition">
                        <i class="fa-solid fa-user-check w-4"></i> Ativar Acesso
                    </button>
                `;
                } else {
                    // Se estiver ativo ou pendente -> Mostra "Revogar Permissões"
                    botaoAcaoStatus = `
                        <button onclick="confirmarAlteracaoStatus(${user.id}, '${user.nome}', 'bloquear')"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition">
                            <i class="fa-solid fa-user-slash w-4"></i> Revogar Permissões
                        </button>
                    `;
                }

                const cardHtml = `
                    <div class="bg-white dark:bg-zinc-900 rounded-[32px] border border-gray-100 dark:border-zinc-800/80 p-5 shadow-sm hover:shadow-xl hover:shadow-indigo-50/40 dark:hover:shadow-none transition-all group relative overflow-visible">
                        
                        <!-- DROPDOWN DE AÇÕES -->
                        <div class="absolute top-5 right-5 z-20">
                            <button onclick="toggleEstudanteMenu(event, 'menu-est-${user.id}')"
                                class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-400 hover:text-indigo-600 transition">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>

                            <div id="menu-est-${user.id}"
                                class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-zinc-700/60 py-2 animate-fade-in z-30">
                                <button onclick="editarUtilizadorPorId(${user.id})"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-gray-600 dark:text-zinc-300 hover:bg-indigo-50 dark:hover:bg-zinc-700/50 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                    <i class="fa-solid fa-user-gear w-4"></i> Editar Vínculo
                                </button>
                                <hr class="my-1 border-gray-100 dark:border-zinc-700/50">

                                <!-- INJEÇÃO DO BOTÃO DINÂMICO AQUI -->
                                ${botaoAcaoStatus}
                            </div>
                        </div>

                        <!-- DADOS PRINCIPAIS -->
                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 rounded-2xl overflow-hidden shadow-inner bg-indigo-500 p-0.5 flex-shrink-0">
                                <img src="../controller/users/${foto}" class="w-full h-full object-cover rounded-[14px] bg-white">
                            </div>
                            <div class="flex-1 min-w-0 pr-8">
                                <h4 class="text-sm font-black text-gray-800 dark:text-white truncate">${user.nome}</h4>
                                <p class="text-[11px] text-gray-400 font-medium mb-2">NIP: ${user.nip}</p>

                                <div class="space-y-2 bg-gray-50 dark:bg-zinc-800/50 p-2.5 rounded-xl border border-gray-100/60 dark:border-zinc-700/40">
                                    <div class="text-gray-600 dark:text-zinc-400 text-[10px] leading-tight">
                                        <p class="font-bold text-indigo-950 dark:text-indigo-300">E-mail de Acesso:</p>
                                        <p class="italic truncate">${user.email}</p>
                                    </div>
                                    <div class="text-gray-600 dark:text-zinc-400 text-[10px] leading-tight">
                                        <p class="font-bold text-green-800 dark:text-emerald-400">Contacto:</p>
                                        <p class="truncate">${user.telefone}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RODAPÉ DO CARD -->
                        <div class="mt-4 pt-4 border-t border-gray-50 dark:border-zinc-800/80 flex items-center justify-between">
                            <span class="text-[10px] font-black text-indigo-500 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2.5 py-1 rounded-lg">
                                ${user.perfil_nome}
                            </span>
                            ${statusBadge}
                        </div>
                    </div>
                `;
                cardsContainer.insertAdjacentHTML('beforeend', cardHtml);
            });
        }

        /**
         * Função Unificada para Ativar ou Bloquear Utilizador com SweetAlert2
         */
        function confirmarAlteracaoStatus(id, nome, acao) {
            const isAtivar = acao === 'ativar';

            const titulo = isAtivar ? 'Ativar Acesso?' : 'Revogar Permissões?';
            const texto = isAtivar
                ? `Tem certeza que deseja reativar o acesso de ${nome}?`
                : `Tem certeza que deseja bloquear a conta de ${nome}?`;

            const corBotao = isAtivar ? '#10b981' : '#ef4444'; // Verde para ativar, Vermelho para revogar
            const textoBotao = isAtivar ? 'Sim, Ativar' : 'Sim, Bloquear';

            Swal.fire({
                title: titulo,
                text: texto,
                icon: isAtivar ? 'question' : 'warning',
                showCancelButton: true,
                confirmColor: corBotao,
                cancelColor: '#6b7280',
                confirmButtonText: textoBotao,
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'A processar...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    try {
                        const formData = new FormData();
                        formData.append('id', id);
                        formData.append('acao', acao); // Envia 'ativar' ou 'bloquear'

                        const response = await fetch('../controller/users/revogar_acesso.php', {
                            method: 'POST',
                            body: formData
                        });
                        const res = await response.json();

                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: isAtivar ? 'Ativado!' : 'Bloqueado!',
                                text: res.message,
                                confirmColor: '#4f46e5',
                                timer: 2000
                            });

                            // Recarrega os dados dinamicamente mantendo a página e os filtros atuais
                            carregarUtilizadores(paginaAtual);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: res.message,
                                confirmColor: '#4f46e5'
                            });
                        }
                    } catch (err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro de Conexão',
                            text: 'Não foi possível completar a ação.',
                            confirmColor: '#4f46e5'
                        });
                    }
                }
            });
        }

        /**
         * Renderiza a Paginação Flutuante no estilo iOS
         */
        function renderizarPaginacao(paginacao) {
            const nav = document.getElementById('paginacaoContainer');
            const { pagina_atual, total_paginas } = paginacao;

            if (total_paginas <= 1) {
                nav.innerHTML = ''; // Esconde a paginação se houver apenas 1 página
                return;
            }

            let html = '';

            // Botão Anterior
            const disablePrev = pagina_atual === 1 ? 'disabled' : '';
            html += `
        <button type="button" title="Página Anterior" ${disablePrev} onclick="carregarUtilizadores(${pagina_atual - 1})"
            class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 transition-all active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
            <i class="fa-solid fa-chevron-left text-xs"></i>
        </button>
        <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-800 my-auto"></div>
        <div class="flex items-center gap-1 px-1">
    `;

            // Lógica dos Números da Paginação
            for (let i = 1; i <= total_paginas; i++) {
                // Mostra a primeira, a última e as páginas próximas à página atual
                if (i === 1 || i === total_paginas || (i >= pagina_atual - 1 && i <= pagina_atual + 1)) {
                    if (i === pagina_atual) {
                        html += `
                    <button type="button" class="px-3.5 py-1.5 rounded-full text-xs font-black bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 shadow-sm transition-all active:scale-95 cursor-pointer">
                        ${i}
                    </button>
                `;
                    } else {
                        html += `
                    <button type="button" onclick="carregarUtilizadores(${i})" class="px-3.5 py-1.5 rounded-full text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/80 dark:hover:bg-zinc-800/60 transition-all active:scale-95 cursor-pointer">
                        ${i}
                    </button>
                `;
                    }
                } else if (i === pagina_atual - 2 || i === pagina_atual + 2) {
                    html += `<span class="text-xs text-zinc-400 dark:text-zinc-600 px-1 font-bold select-none">•••</span>`;
                }
            }

            // Botão Próximo
            const disableNext = pagina_atual === total_paginas ? 'disabled' : '';
            html += `
        </div>
        <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-800 my-auto"></div>
        <button type="button" title="Próxima Página" ${disableNext} onclick="carregarUtilizadores(${pagina_atual + 1})"
            class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 transition-all active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
            <i class="fa-solid fa-chevron-right text-xs"></i>
        </button>
    `;

            nav.innerHTML = html;
        }

        /**
         * Controla a exibição do Menu Dropdown dos Cards
         */
        function toggleEstudanteMenu(event, menuId) {
            event.stopPropagation();
            document.querySelectorAll('[id^="menu-est-"]').forEach(el => {
                if (el.id !== menuId) el.classList.add('hidden');
            });
            const menu = document.getElementById(menuId);
            if (menu) menu.classList.toggle('hidden');
        }

        // Array global com os utilizadores (se ainda não declarou)
        let listaUtilizadoresAtuais = [];

        window.editarUtilizadorPorId = function (userId) {
            // Fecha os menus abertos
            document.querySelectorAll('[id^="menu-est-"]').forEach(el => el.classList.add('hidden'));

            console.log("ID procurado:", userId);
            console.log("Conteúdo atual de listaUtilizadoresAtuais:", listaUtilizadoresAtuais);

            if (!listaUtilizadoresAtuais || listaUtilizadoresAtuais.length === 99) {
                console.error("Atenção: A lista de utilizadores na memória está vazia. Os cards foram carregados corretamente?");
                return;
            }

            // Procura o utilizador testando várias opções comuns para o nome da chave do ID
            const user = listaUtilizadoresAtuais.find(u =>
                u.id == userId || u.user_id == userId || u.codigo == userId || u.id_utilizador == userId
            );

            if (!user) {
                console.error(`Utilizador com ID [${userId}] não foi encontrado na memória. Verifique a consola acima para ver os dados disponíveis.`);
                return;
            }

            // Se encontrou, chama a função de edição
            window.editarUtilizador(user);
        };

        // Quando clica para EDITAR um utilizador
        window.editarUtilizador = async function (user) {
            if (!user) return;

            // 1. Preenche os campos básicos
            if (document.getElementById('tecId')) document.getElementById('tecId').value = user.id;
            if (document.getElementById('tecNome')) document.getElementById('tecNome').value = user.nome || '';
            if (document.getElementById('tecEmail')) document.getElementById('tecEmail').value = user.email || '';
            if (document.getElementById('tecNip')) document.getElementById('tecNip').value = user.nip || '';
            if (document.getElementById('tecTelefone')) document.getElementById('tecTelefone').value = user.telefone || '';
            if (document.getElementById('tecStatus')) document.getElementById('tecStatus').value = user.estado || 'ativo';
            if (document.getElementById('tecQrCode')) document.getElementById('tecQrCode').value = user.qr_code_passe || '';

            // 2. CARREGA OS PERFIS VIA API E JÁ SELECIONA O DO UTILIZADOR
            const perfilId = user.perfil_id || user.perfil || '';
            await carregarPerfisNoSelect(perfilId);

            // 3. Trata a Senha (inativa na edição)
            const inputSenha = document.getElementById('tecSenha');
            if (inputSenha) {
                inputSenha.value = '';
                inputSenha.disabled = true;
                inputSenha.removeAttribute('required');
                inputSenha.placeholder = "Senha protegida (Inativa na edição)";
                inputSenha.classList.add('bg-gray-100', 'cursor-not-allowed', 'dark:bg-zinc-800');
            }

            // 4. Textos do Modal
            const titleModal = document.getElementById('estModalTitle');
            if (titleModal) titleModal.innerText = "Editar Vínculo do Usuário";

            const btnSubmit = document.getElementById('tecBtnSubmit');
            if (btnSubmit) btnSubmit.innerText = "Atualizar Dados do Técnico";

            // 5. Exibe o Modal
            const modal = document.getElementById('estudanteModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        };

        // Quando clica para NOVO CADASTRO
        window.abrirModalNovoTecnico = async function () {
            const form = document.getElementById('tecnicoForm');
            if (form) form.reset();

            if (document.getElementById('tecId')) document.getElementById('tecId').value = '';

            // Carrega os perfis via API sem selecionar nenhum pré-definido
            await carregarPerfisNoSelect();

            // Reativa a senha para novo cadastro
            const inputSenha = document.getElementById('tecSenha');
            if (inputSenha) {
                inputSenha.disabled = false;
                inputSenha.setAttribute('required', 'required');
                inputSenha.placeholder = "••••••••";
                inputSenha.classList.remove('bg-gray-100', 'cursor-not-allowed', 'dark:bg-zinc-800');
            }

            // Títulos padrão
            const titleModal = document.getElementById('estModalTitle');
            if (titleModal) titleModal.innerText = "Novo Vínculo de Usuário";

            const btnSubmit = document.getElementById('tecBtnSubmit');
            if (btnSubmit) btnSubmit.innerText = "Confirmar e Guardar Técnico";

            // Abre o Modal
            const modal = document.getElementById('estudanteModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        };

        // Funções para Fechar o Modal
        window.closeEstudanteModal = window.fecharTecnicoModal = function () {
            const modal = document.getElementById('estudanteModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        };

        /**
         * Revoga Permissão / Bloqueia o Utilizador com confirmação via SweetAlert2
         */
        function confirmarRevogacao(id, nome) {
            Swal.fire({
                title: 'Revogar Permissões?',
                text: `Tem certeza que deseja bloquear a conta de ${nome}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmColor: '#ef4444',
                cancelColor: '#6b7280',
                confirmButtonText: 'Sim, Bloquear',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'A processar...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    try {
                        const formData = new FormData();
                        formData.append('id', id);

                        const response = await fetch('../controller/users/revogar_acesso.php', {
                            method: 'POST',
                            body: formData
                        });
                        const res = await response.json();

                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Revogado!',
                                text: res.message,
                                confirmColor: '#4f46e5',
                                timer: 2000
                            });
                            carregarUtilizadores(); // Recarrega a listagem dinamicamente
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: res.message,
                                confirmColor: '#4f46e5'
                            });
                        }
                    } catch (err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Não foi possível completar a ação.',
                            confirmColor: '#4f46e5'
                        });
                    }
                }
            });
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

    <!-- MODAL DE CADASTRO E EDIÇÃO DE VÍNCULO DE USUÁRIO ( NOVO / EDITAR ) -->
    <div id="estudanteModal"
        class="hidden fixed inset-0 z-[100] items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
        <div
            class="bg-white w-full max-w-xl rounded-[32px] shadow-2xl overflow-hidden transform transition-all max-h-[90vh] flex flex-col">
            <!-- Cabeçalho do Modal -->
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-indigo-50/50">
                <div>
                    <h3 id="estModalTitle" class="text-lg font-bold text-gray-800">Novo Vínculo de Usuário</h3>
                    <p class="text-xs text-gray-500">Defina as credenciais, o perfil de acesso e o departamento do
                        usuário.</p>
                </div>
                <button onclick="closeEstudanteModal()"
                    class="w-8 h-8 rounded-full hover:bg-gray-200/80 flex items-center justify-center text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- FORMULÁRIO DE CADASTRO / EDIÇÃO DE TÉCNICO -->
            <form id="tecnicoForm"
                class="p-6 md:p-8 overflow-y-auto flex-1 space-y-5 custom-scrollbar bg-white dark:bg-zinc-900"
                onsubmit="event.preventDefault(); salvarTecnico();">
                <input type="hidden" id="tecId" value="">

                <!-- ZONA DE CARREGAMENTO DE FOTO DE PERFIL -->
                <div
                    class="flex items-center gap-5 bg-gray-50 dark:bg-zinc-800/50 p-4 rounded-2xl border border-dashed border-gray-200 dark:border-zinc-700/60">
                    <div
                        class="relative w-20 h-20 rounded-2xl overflow-hidden shadow-inner bg-indigo-100 dark:bg-indigo-950/60 flex-shrink-0 border-2 border-white dark:border-zinc-800">
                        <img id="tecAvatarPreview" src="https://i.pravatar.cc/150?u=placeholder"
                            class="w-full h-full object-cover hidden">
                        <div id="tecAvatarFallback"
                            class="w-full h-full flex items-center justify-center text-indigo-500 dark:text-indigo-400">
                            <i class="fa-solid fa-user-shield text-2xl"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-700 dark:text-zinc-200 mb-1">Fotografia do Técnico</p>
                        <p class="text-[11px] text-gray-400 dark:text-zinc-400 mb-2">Formatos aceites: JPG ou PNG.
                            Máximo 2MB.</p>
                        <div class="flex gap-2">
                            <label for="tecAvatarInput"
                                class="cursor-pointer inline-flex items-center gap-2 bg-white dark:bg-zinc-800 hover:bg-indigo-50 dark:hover:bg-zinc-700 border border-gray-200 dark:border-zinc-700 text-gray-600 dark:text-zinc-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-2 rounded-xl text-xs font-bold transition shadow-sm">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Carregar Foto
                            </label>
                            <input type="file" id="tecAvatarInput" accept="image/png, image/jpeg, image/jpg"
                                class="hidden" onchange="previewTecnicoAvatar(this)">

                            <button type="button" id="btnRemoveTecAvatar" onclick="removeTecnicoAvatar()"
                                class="hidden items-center gap-2 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 px-3 py-2 rounded-xl text-xs font-bold transition">
                                <i class="fa-solid fa-trash-can"></i> Remover
                            </button>
                        </div>
                    </div>
                </div>

                <!-- NOME COMPLETO & E-MAIL -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Nome
                            Completo</label>
                        <input type="text" id="tecNome" required placeholder="Ex: Carlos Alberto Mendes"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-700/60 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-zinc-800 outline-none text-sm font-medium text-gray-900 dark:text-white transition">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">E-mail
                            Corporativo</label>
                        <input type="email" id="tecEmail" required placeholder="cmendes@pgr.ao"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-700/60 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-zinc-800 outline-none text-sm font-medium text-gray-900 dark:text-white transition">
                    </div>
                </div>

                <!-- NIP & TELEFONE -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">NIP
                            (Nº Identificação Policial / Serviço)</label>
                        <input type="text" id="tecNip" required placeholder="Ex: 8912/24"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-700/60 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-zinc-800 outline-none text-sm font-bold text-gray-900 dark:text-white transition">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Contacto
                            Telefónico</label>
                        <input type="tel" id="tecTelefone" required placeholder="+244 923 000 111"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-700/60 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-zinc-800 outline-none text-sm font-medium text-gray-900 dark:text-white transition">
                    </div>
                </div>

                <!-- PERFIL DE ACESSO & STATUS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- CAMPO SELECT DOS PERFIS -->
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                            Perfil de Acesso (Cargo)
                        </label>
                        <select id="tecPerfil" name="perfil_id" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-700/60 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-zinc-800 outline-none text-sm font-medium text-gray-700 dark:text-zinc-200 transition">
                            <option value="">Selecione o Perfil</option>
                            <?php
                            // Assume que a variável global de ligação à base de dados se chama $conn (ajusta se necessário)
                            if (isset($conn)) {
                                $sqlPerfis = "SELECT id, nome FROM perfis ORDER BY nome ASC";
                                $resultadoPerfis = $conn->query($sqlPerfis);

                                if ($resultadoPerfis && $resultadoPerfis->num_rows > 0) {
                                    while ($perfil = $resultadoPerfis->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($perfil['id']) . '">' . htmlspecialchars($perfil['nome']) . '</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Status
                            da Conta</label>
                        <select id="tecStatus" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-700/60 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-zinc-800 outline-none text-sm font-medium text-gray-700 dark:text-zinc-200 transition">
                            <option value="ativo">Ativo (Acesso Liberado)</option>
                            <option value="pendente">Pendente (Aguardando Ativação)</option>
                            <option value="bloqueado">Inativo / Bloqueado</option>
                        </select>
                    </div>
                </div>

                <!-- CAPTURA QR CODE DO PASSE DE SERVIÇO & SENHA -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <!-- CAPTURA DE QR CODE -->
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-qrcode text-indigo-500 mr-1"></i> QR Code do Passe de Serviço
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="tecQrCode" placeholder="Código do passe ou leitura rápida" readonly
                                class="flex-1 px-4 py-3 bg-gray-100 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700/60 rounded-xl outline-none text-xs font-mono font-bold text-gray-800 dark:text-zinc-200">
                            <button type="button" onclick="iniciarLeituraQR()"
                                class="px-4 py-3 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold rounded-xl border border-indigo-200 dark:border-indigo-900 transition flex items-center gap-2 text-xs flex-shrink-0 cursor-pointer">
                                <i class="fa-solid fa-camera"></i>QR
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-1">Permite a entrada / acesso
                            espontâneo via leitura do passe.</p>
                    </div>

                    <!-- SENHA DE ACESSO -->
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Senha
                            de Acesso</label>
                        <div class="relative">
                            <input type="password" id="tecSenha" required placeholder="••••••••"
                                class="w-full px-4 py-3 pr-10 bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-700/60 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-zinc-800 outline-none text-sm font-medium text-gray-900 dark:text-white transition">
                            <button type="button" onclick="toggleSenhaVisibilidade()"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-200">
                                <i id="iconSenha" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- RODAPÉ DE AÇÕES -->
                <div class="pt-6 border-t border-gray-100 dark:border-zinc-800 flex justify-end gap-3">
                    <button type="button" onclick="fecharTecnicoModal()"
                        class="px-5 py-3 text-gray-500 dark:text-zinc-400 font-bold hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-all text-sm">
                        Cancelar
                    </button>
                    <button type="submit" id="tecBtnSubmit"
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/20 transition-all text-sm cursor-pointer">
                        Confirmar e Guardar Técnico
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Variável de controlo para saber se o utilizador clicou em remover a foto
        let fotoFoiRemovida = false;

        /**
         * Envia os dados do formulário via AJAX/Fetch para a API PHP
         */
        async function salvarTecnico() {
            const idInput = document.getElementById('tecId').value;
            const nomeInput = document.getElementById('tecNome').value.trim();
            const emailInput = document.getElementById('tecEmail').value.trim();
            const nipInput = document.getElementById('tecNip').value.trim();
            const telefoneInput = document.getElementById('tecTelefone').value.trim();
            const perfilInput = document.getElementById('tecPerfil').value;
            const statusInput = document.getElementById('tecStatus').value;
            const qrCodeInput = document.getElementById('tecQrCode').value.trim();
            const senhaInput = document.getElementById('tecSenha').value;
            const fotoFileInput = document.getElementById('tecAvatarInput').files[0];

            // Validações rápidas no Frontend
            if (!nomeInput || !emailInput || !nipInput || !telefoneInput || !perfilInput) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos Pendentes',
                    text: 'Por favor, preencha todos os campos obrigatórios.',
                    confirmColor: '#4f46e5'
                });
                return;
            }

            if (!idInput && !senhaInput) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Senha Obrigatória',
                    text: 'Defina uma senha de acesso para o novo técnico.',
                    confirmColor: '#4f46e5'
                });
                return;
            }

            // Prepara os dados multipart/form-data (suporta envio de ficheiros)
            const formData = new FormData();
            if (idInput) formData.append('id', idInput);
            formData.append('nome', nomeInput);
            formData.append('email', emailInput);
            formData.append('nip', nipInput);
            formData.append('telefone', telefoneInput);
            formData.append('perfil_id', perfilInput);
            formData.append('status', statusInput);
            formData.append('qr_code', qrCodeInput);
            formData.append('senha', senhaInput);
            formData.append('remover_foto', fotoFoiRemovida ? 'true' : 'false');

            if (fotoFileInput) {
                formData.append('foto', fotoFileInput);
            }

            // Notificação de carregamento com SweetAlert2
            Swal.fire({
                title: 'A guardar dados...',
                text: 'Por favor aguarde enquanto processamos o pedido.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch('../controller/users/cadastro_edicao.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: result.message,
                        confirmColor: '#4f46e5',
                        timer: 2000,
                        showConfirmButton: true
                    }).then(() => {
                        fecharTecnicoModal();
                        // Recarrega a tabela de técnicos se existir uma função para tal
                        if (typeof recarregarTabelaTecnicos === 'function') {
                            recarregarTabelaTecnicos();
                        } else {
                            location.reload(); // Recarrega a página caso não use carregamento dinâmico da tabela
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Atenção',
                        text: result.message,
                        confirmColor: '#4f46e5'
                    });
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de Conexão',
                    text: 'Não foi possível comunicar com o servidor. Tente novamente.',
                    confirmColor: '#4f46e5'
                });
            }
        }

        // ------------------------------------------------------------------
        // FUNÇÕES AUXILIARES DO FORMULÁRIO
        // ------------------------------------------------------------------

        // Preview da Foto selecionada no Input File
        function previewTecnicoAvatar(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validação de tamanho no client-side (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ficheiro muito grande',
                        text: 'A fotografia deve ter no máximo 2MB.',
                        confirmColor: '#4f46e5'
                    });
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('tecAvatarPreview');
                    const fallback = document.getElementById('tecAvatarFallback');
                    const btnRemove = document.getElementById('btnRemoveTecAvatar');

                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    fallback.classList.add('hidden');
                    btnRemove.classList.remove('hidden');
                    btnRemove.classList.add('inline-flex');
                    fotoFoiRemovida = false;
                };
                reader.readAsDataURL(file);
            }
        }

        // Remover foto selecionada / existente
        function removeTecnicoAvatar() {
            const preview = document.getElementById('tecAvatarPreview');
            const fallback = document.getElementById('tecAvatarFallback');
            const btnRemove = document.getElementById('btnRemoveTecAvatar');
            const input = document.getElementById('tecAvatarInput');

            input.value = '';
            preview.src = 'https://i.pravatar.cc/150?u=placeholder';
            preview.classList.add('hidden');
            fallback.classList.remove('hidden');
            btnRemove.classList.add('hidden');
            btnRemove.classList.remove('inline-flex');

            fotoFoiRemovida = true; // Sinaliza para a API remover do servidor se for edição
        }

        // Mostrar / Ocultar Senha
        function toggleSenhaVisibilidade() {
            const inputSenha = document.getElementById('tecSenha');
            const iconSenha = document.getElementById('iconSenha');

            if (inputSenha.type === 'password') {
                inputSenha.type = 'text';
                iconSenha.classList.remove('fa-eye');
                iconSenha.classList.add('fa-eye-slash');
            } else {
                inputSenha.type = 'password';
                iconSenha.classList.remove('fa-eye-slash');
                iconSenha.classList.add('fa-eye');
            }
        }

        // Fechar / Limpar Modal
        function fecharTecnicoModal() {
            document.getElementById('tecnicoForm').reset();
            document.getElementById('tecId').value = '';
            removeTecnicoAvatar();
            fotoFoiRemovida = false;

            // Esconder modal (ajuste a classe/ID conforme o seu modal)
            const modal = document.getElementById('tecnicoModal');
            if (modal) modal.classList.add('hidden');
        }

        /**
         * Busca os perfis na API e preenche o select #tecPerfil
         * @param {string|number} perfilIdParaSelecionar - ID do perfil a ser selecionado automaticamente (opcional)
         */
        async function carregarPerfisNoSelect(perfilIdParaSelecionar = '') {
            const selectPerfil = document.getElementById('tecPerfil');
            if (!selectPerfil) return;

            try {
                // Altere o caminho abaixo para o endpoint real da sua API de perfis em JSON
                const response = await fetch('../controller/users/listar_perfis.php');
                const result = await response.json();

                // Reseta o select mantendo apenas a primeira opção padrão
                selectPerfil.innerHTML = '<option value="">Selecione o Perfil</option>';

                // Verifica se a API retornou os dados com sucesso
                const perfis = result.dados || result; // Ajuste conforme a estrutura do seu JSON

                if (Array.isArray(perfis)) {
                    perfis.forEach(perfil => {
                        const option = document.createElement('option');
                        option.value = perfil.id;
                        option.textContent = perfil.nome;
                        selectPerfil.appendChild(option);
                    });

                    // Se foi passado um ID (modo edição), seleciona ele agora que as opções existem
                    if (perfilIdParaSelecionar) {
                        selectPerfil.value = perfilIdParaSelecionar;
                    }
                } else {
                    console.error('Formato de dados de perfis inválido:', result);
                }
            } catch (error) {
                console.error('Erro ao carregar os perfis via API:', error);
            }
        }
    </script>

    <!-- JAVASCRIPT ATUALIZADO PARA SUPORTAR OS NOVOS CAMPOS -->
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
            window.location.href = 'logout.php';
        }

        // Função para ABRIR o Modal (Novo Registo)
        function openEstudanteModal() {
            const modal = document.getElementById('estudanteModal');
            const form = document.getElementById('tecnicoForm');
            const title = document.getElementById('estModalTitle');

            // 1. Limpa o formulário e o ID oculto
            if (form) form.reset();
            document.getElementById('tecId').value = '';

            // 2. Reseta a fotografia para o estado inicial
            removeTecnicoAvatar();

            // 3. Ajusta o título do Modal
            if (title) title.textContent = 'Novo Vínculo de Usuário';

            // 4. Exibe o Modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // Função para FECHAR o Modal
        function closeEstudanteModal() {
            const modal = document.getElementById('estudanteModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        // Alias para o botão "Cancelar" (que chama fecharTecnicoModal no HTML)
        function fecharTecnicoModal() {
            closeEstudanteModal();
        }

        // Função para Resetar a Fotografia/Avatar
        function removeTecnicoAvatar() {
            const preview = document.getElementById('tecAvatarPreview');
            const fallback = document.getElementById('tecAvatarFallback');
            const btnRemove = document.getElementById('btnRemoveTecAvatar');
            const input = document.getElementById('tecAvatarInput');

            if (preview) preview.classList.add('hidden');
            if (fallback) fallback.classList.remove('hidden');
            if (btnRemove) {
                btnRemove.classList.add('hidden');
                btnRemove.classList.remove('inline-flex');
            }
            if (input) input.value = '';
        }
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