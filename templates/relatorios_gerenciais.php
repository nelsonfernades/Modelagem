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
                            class="relative flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600/10 text-white transition-all">
                            <div class="absolute left-0 w-1 h-6 bg-blue-400 rounded-r-full"></div>
                            <i class="fa-solid fa-chart-simple w-5 text-blue-400 transition-colors"></i>
                            <span class="text-sm font-medium text-blue-400">Relatórios Gerenciais</span>
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
            <header class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center relativez-40">
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
            <!-- TELA: GERAÇÃO DE RELATÓRIOS E COMPILADO ESTATÍSTICO     -->
            <!-- ======================================================= -->
            <main class="p-6 max-w-[1600px] w-full mx-auto flex-1 overflow-y-auto animate-fade-in">

                <!-- 1. TOPO DA PÁGINA -->
                <div class="mb-8">
                    <h2 class="text-2xl font-black text-gray-800 tracking-tight">Relatórios e Indicadores</h2>
                    <p class="text-sm font-medium text-gray-500 mt-1">Monitorize o desempenho global dos técnicos desde
                        processos pendentes e finalizados.</p>
                </div>

                <?php if ($perfilId === 2 || $perfilId === 1): ?>
                    <!-- 2. PAINEL DE MÉTRICAS ANALÍTICAS (KPIs) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                        <!-- KPI 1: Total de Processos -->
                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl flex-shrink-0">
                                <i class="fa-solid fa-folder-open"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Total de
                                    Processos</span>
                                <h3 id="kpi-processos" class="text-xl font-black text-gray-800 mt-0.5">--</h3>
                                <p class="text-[10px] text-emerald-600 font-semibold mt-0.5"><i
                                        class="fa-solid fa-arrow-trend-up mr-0.5"></i> Registados no sistema</p>
                            </div>
                        </div>

                        <!-- KPI 2: Autos de Interrogatório -->
                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl flex-shrink-0">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Autos de
                                    Interrogatório</span>
                                <h3 id="kpi-autos" class="text-xl font-black text-gray-800 mt-0.5">--</h3>
                                <p class="text-[10px] text-gray-400 font-semibold mt-0.5">Emitidos e guardados</p>
                            </div>
                        </div>

                        <!-- KPI 3: Arguidos Registados -->
                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 text-xl flex-shrink-0">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Arguidos
                                    Vinculados</span>
                                <h3 id="kpi-arguidos" class="text-xl font-black text-gray-800 mt-0.5">--</h3>
                                <p class="text-[10px] text-purple-600 font-semibold mt-0.5">Base de dados geral</p>
                            </div>
                        </div>

                        <!-- KPI 4: Bens Apreendidos -->
                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-xl flex-shrink-0">
                                <i class="fa-solid fa-box-archive"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Bens
                                    Apreendidos</span>
                                <h3 id="kpi-bens" class="text-xl font-black text-gray-800 mt-0.5">--</h3>
                                <p class="text-[10px] text-rose-600 font-semibold mt-0.5">Evidências registadas</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        carregarMetricasKpi();
                    });

                    function carregarMetricasKpi() {
                        fetch('../controller/relatorios/get_kpis.php') // Ajusta o caminho relativo conforme a localização da tua view
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Atualiza os elementos HTML com os valores vindos da BD
                                    document.getElementById('kpi-processos').textContent = data.processos;
                                    document.getElementById('kpi-autos').textContent = data.autos;
                                    document.getElementById('kpi-arguidos').textContent = data.arguidos;
                                    document.getElementById('kpi-bens').textContent = data.bens;
                                } else {
                                    console.error("Erro ao obter KPIs:", data.message);
                                }
                            })
                            .catch(error => {
                                console.error("Erro na requisição fetch dos KPIs:", error);
                            });
                    }
                </script>

                <!-- 3. CENTRAL DE EMISSÃO (FILTROS DE EXPORTAÇÃO) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                    <!-- COLUNA 1 & 2: CONFIGURAÇÃO DO RELATÓRIO POR DATAS E TIPOS -->
                    <div
                        class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-3xl border border-gray-100 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                        <div>
                            <h3 class="text-base font-black text-gray-800 dark:text-white">Extrair Relatório de
                                Instrução Criminal</h3>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 font-semibold mt-0.5">
                                Defina o tipo de documento e o intervalo de datas para gerar o relatório consolidação de
                                autos.
                            </p>
                        </div>

                        <form id="reportFilterForm" onsubmit="handleGenerateReport(event)" class="space-y-4">
                            <!-- LINHA 1: TIPO DE RELATÓRIO E STATUS -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Tipo de Relatório -->
                                <div class="space-y-1.5">
                                    <label
                                        class="block text-[11px] font-black text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                        Tipo de Relatório *
                                    </label>
                                    <select id="repo_tipo"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-zinc-700 text-xs font-semibold text-gray-800 dark:text-zinc-200 bg-white dark:bg-zinc-800 focus:outline-none focus:border-indigo-500 transition cursor-pointer">
                                        <option value="">Selecione o tipo de relatório</option>
                                        <option value="mapa_celas">Mapa de Controlo Diário das Celas (Não Ouvidos)
                                        </option>
                                        <option value="auto_interrogatorio">Mapa de Auto de Interrogatório</option>
                                    </select>
                                </div>

                                <!-- Status do Processo -->
                                <div class="space-y-1.5">
                                    <label
                                        class="block text-[11px] font-black text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                        Fase / Status do Processo
                                    </label>
                                    <select id="repo_status"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-zinc-700 text-xs font-semibold text-gray-800 dark:text-zinc-200 bg-white dark:bg-zinc-800 focus:outline-none focus:border-indigo-500 transition cursor-pointer">
                                        <option value="todos">Todos os Status</option>
                                        <option value="Em Andamento">Em Instrução Preparatória</option>
                                        <option value="Finalizado">Instrução Concluída (Relatório Final)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- LINHA 2: FILTRO POR DATAS (DATA INICIAL E DATA FINAL) -->
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/50 dark:bg-zinc-800/40 p-4 rounded-2xl border border-gray-100 dark:border-zinc-800/80">
                                <div class="space-y-1.5">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                        <i class="fa-regular fa-calendar mr-1 text-indigo-500"></i> Data Inicial *
                                    </label>
                                    <input type="date" id="repo_data_inicio" required
                                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-zinc-700 text-xs font-semibold text-gray-800 dark:text-zinc-200 bg-white dark:bg-zinc-800 focus:outline-none focus:border-indigo-500 transition">
                                </div>

                                <div class="space-y-1.5">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                        <i class="fa-regular fa-calendar-check mr-1 text-indigo-500"></i> Data Final *
                                    </label>
                                    <input type="date" id="repo_data_fim" required
                                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-zinc-700 text-xs font-semibold text-gray-800 dark:text-zinc-200 bg-white dark:bg-zinc-800 focus:outline-none focus:border-indigo-500 transition">
                                </div>
                            </div>

                            <!-- LINHA 3: FORMATO E BOTÃO DE IMPRESSÃO -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end pt-2">
                                <!-- Formato de Saída -->
                                <div class="sm:col-span-2 space-y-1.5">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                        Formato de Exportação *
                                    </label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <label
                                            class="flex items-center justify-center gap-2 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2.5 rounded-xl text-xs font-bold text-gray-700 dark:text-zinc-300 cursor-pointer hover:bg-indigo-50/20 dark:hover:bg-indigo-950/30 hover:border-indigo-200 transition">
                                            <input type="radio" name="formato" value="pdf" checked
                                                class="text-indigo-600 focus:ring-indigo-500">
                                            <i class="fa-solid fa-file-pdf text-rose-500"></i> Documento PDF
                                        </label>
                                        <label
                                            class="flex items-center justify-center gap-2 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2.5 rounded-xl text-xs font-bold text-gray-700 dark:text-zinc-300 cursor-pointer hover:bg-emerald-50/20 dark:hover:bg-emerald-950/30 hover:border-emerald-200 transition">
                                            <input type="radio" name="formato" value="excel"
                                                class="text-emerald-600 focus:ring-emerald-500">
                                            <i class="fa-solid fa-file-excel text-emerald-600"></i> Planilha Excel
                                        </label>
                                    </div>
                                </div>

                                <!-- Botão Imprimir / Gerar -->
                                <div>
                                    <button type="submit"
                                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold text-xs transition shadow-md shadow-indigo-100 dark:shadow-none flex items-center justify-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-print"></i> Imprimir Relatório
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- COLUNA 3: MINUTAS E MODELOS OFICIAIS -->
                    <div
                        class="bg-white dark:bg-zinc-900 rounded-3xl border border-gray-100 dark:border-zinc-800 shadow-sm p-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-base font-black text-gray-800 dark:text-white">Modelos e
                                    Minutas</h3>
                                <p class="text-xs text-gray-500 dark:text-zinc-400 font-semibold mt-0.5">
                                    Modelos normativos prontos para emissão rápida.
                                </p>
                            </div>

                            <div class="space-y-2.5">
                                <div class="space-y-3">
                                    <!-- Modelo 1: Mapa de Controlo de Celas -->
                                    <button type="button" onclick="template('celas')"
                                        class="w-full text-left p-3 rounded-xl border border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-800/40 hover:bg-indigo-50/40 hover:border-indigo-100 transition flex items-center justify-between group cursor-pointer">
                                        <div class="flex items-center gap-3">
                                            <div class="text-lg text-indigo-500"><i class="fa-regular fa-file-pdf"></i>
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-xs font-bold text-gray-800 dark:text-zinc-200 group-hover:text-indigo-600">
                                                    Mapa de Celas
                                                </h4>
                                                <p class="text-[10px] text-gray-400 font-semibold">Minuta oficial de controlo e mapeamento</p>
                                            </div>
                                        </div>
                                        <i
                                            class="fa-solid fa-download text-xs text-gray-400 group-hover:text-indigo-600"></i>
                                    </button>

                                    <!-- Modelo 2: Auto de Interrogatório -->
                                    <button type="button" onclick="template('interrogatorio')"
                                        class="w-full text-left p-3 rounded-xl border border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-800/40 hover:bg-indigo-50/40 hover:border-indigo-100 transition flex items-center justify-between group cursor-pointer">
                                        <div class="flex items-center gap-3">
                                            <div class="text-lg text-rose-500"><i class="fa-regular fa-file-pdf"></i>
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-xs font-bold text-gray-800 dark:text-zinc-200 group-hover:text-indigo-600">
                                                    Auto de Interrogatório
                                                </h4>
                                                <p class="text-[10px] text-gray-400 font-semibold">Modelo oficial de
                                                    interrogatório</p>
                                            </div>
                                        </div>
                                        <i
                                            class="fa-solid fa-download text-xs text-gray-400 group-hover:text-indigo-600"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div
                            class="mt-4 pt-4 border-t border-gray-100 dark:border-zinc-800 bg-indigo-50/20 dark:bg-indigo-950/20 p-3 rounded-xl border-indigo-100/30 text-center">
                            <p class="text-[10px] text-indigo-800 dark:text-indigo-300 font-semibold leading-relaxed">
                                <i class="fa-solid fa-shield-halved mr-1 text-indigo-500"></i> Todos os
                                relatórios
                                gerados incorporam assinatura digital e selo hash SHA-256 para garantia da
                                cadeia de custódia.
                        </div>
                    </div>

                </div>

                <script>
                    // Define a função globalmente para evitar erros de escopo
                    window.template = function (tipo) {
                        // Alerta visual de carregamento
                        Swal.fire({
                            title: 'A descarregar minuta...',
                            text: 'Por favor, aguarde enquanto o documento oficial é preparado.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const url = `../views/relatorios/baixar_template.php?tipo=${tipo}`;

                        fetch(url)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Erro ao gerar o documento no servidor.');
                                }
                                return response.blob();
                            })
                            .then(blob => {
                                const downloadUrl = window.URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.href = downloadUrl;

                                a.download = tipo === 'interrogatorio'
                                    ? 'Minuta_Auto_Interrogatorio_Vazio.pdf'
                                    : 'Minuta_Mapa_Celas_Vazio.pdf';

                                document.body.appendChild(a);
                                a.click();
                                a.remove();
                                window.URL.revokeObjectURL(downloadUrl);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Concluído!',
                                    text: 'A minuta foi descarregada com sucesso.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            })
                            .catch(error => {
                                console.error('Erro:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro no Download',
                                    text: 'Não foi possível gerar a minuta solicitada. Tente novamente.',
                                    confirmButtonText: 'OK'
                                });
                            });
                    };

                </script>
            </main>

            <!-- ======================================================= -->
            <!-- LOGICA DE PROCESSAMENTO E DOWNLOAD (JAVASCRIPT)         -->
            <!-- ======================================================= -->
            <script>
                function handleGenerateReport(event) {
                    // 1. Evita que a página seja recarregada
                    event.preventDefault();

                    // 2. Recolhe os valores do formulário
                    const tipo = document.getElementById('repo_tipo').value;
                    const status = document.getElementById('repo_status').value;
                    const dataInicio = document.getElementById('repo_data_inicio').value;
                    const dataFim = document.getElementById('repo_data_fim').value;
                    const formatoInput = document.querySelector('input[name="formato"]:checked');

                    // Validação: Verificar se o tipo de relatório foi selecionado
                    if (!tipo) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            text: 'Por favor, selecione um tipo de relatório válido.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Validação: Verificar se o formato foi selecionado
                    if (!formatoInput) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            text: 'Por favor, selecione um formato de exportação.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    const formato = formatoInput.value;

                    // 3. Validação simples de datas
                    if (new Date(dataInicio) > new Date(dataFim)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Datas inválidas',
                            text: 'A Data Inicial não pode ser superior à Data Final!',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // 4. Seleciona o ficheiro de destino com base no tipo de relatório escolhido
                    let arquivoDestino = '';

                    switch (tipo) {
                        case 'mapa_celas': // Ajuste conforme o valor option value no seu <select id="repo_tipo">
                            arquivoDestino = 'gerar-mapa-celas.php';
                            break;
                        case 'auto_interrogatorio':
                            Swal.fire({
                                title: 'Impressão Indisponível',
                                text: 'No momento, não se encontra disponível a impressão em massa. Por favor, imprima um de cada vez na tela de gestão de interrogatórios.',
                                icon: 'info',
                                confirmButtonText: 'Entendido',
                                confirmButtonColor: '#4f46e5',
                                background: '#ffffff',
                                color: '#1f2937'
                            });
                            return; // <-- ADICIONADO AQUI: Interrompe a execução para não tentar fazer o download
                        default:
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: 'Tipo de relatório desconhecido.',
                                confirmButtonText: 'OK'
                            });
                            return;
                    }

                    // 5. Alerta de processamento (Loading com SweetAlert2)
                    Swal.fire({
                        title: 'A gerar relatório...',
                        text: 'Por favor, aguarde enquanto o documento está a ser preparado.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // 6. Constrói a URL com os parâmetros (Query String)
                    const params = new URLSearchParams({
                        tipo: tipo,
                        status: status,
                        inicio: dataInicio,
                        fim: dataFim,
                        formato: formato
                    });

                    const url = `../views/relatorios/${arquivoDestino}?${params.toString()}`;

                    // Simula um tempo breve de carregamento e dispara o download
                    setTimeout(() => {
                        window.location.href = url;

                        Swal.fire({
                            icon: 'success',
                            title: 'Concluído!',
                            text: 'O seu relatório foi gerado com sucesso.',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }, 1500);
                }

                /**
                 * Gere o download dos modelos e minutas oficiais (Word/PDF)
                 */
                function baixarTemplatePadrao(tipo) {
                    const link = document.createElement('a');
                    link.href = `assets/minutas/${tipo}.docx`;
                    link.download = `Minuta_${tipo.charAt(0).toUpperCase() + tipo.slice(1)}.docx`;
                    link.click();
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