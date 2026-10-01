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
                                class="relative flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600/10 text-white transition-all">
                                <div class="absolute left-0 w-1 h-6 bg-blue-400 rounded-r-full"></div>
                                <i class="fa-solid fa-calendar-days w-5 text-blue-400 transition-colors"></i>
                                <span class="text-sm font-medium  text-blue-400">Deliberação (Despachos)</span>
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

            <main class="p-6">
                <!-- TÍTULO DA PÁGINA -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Despachos (Procurador Dr. Aguinaldo Baptista)</h2>
                        <p class="text-sm text-gray-500">Configure e acompanhe os marcos temporais e datas de entrega
                            dos processos.</p>
                    </div>
                </div>

                <!-- CARDS METRICS DE PRAZOS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

                    <!-- Card 1: Total de Processos -->
                    <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-lg font-black">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total de Processos
                            </p>
                            <h3 id="metric-total-processos" class="text-xl font-black text-gray-800">0</h3>
                        </div>
                    </div>

                    <!-- Card 2: Aguardando despachos -->
                    <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 text-lg font-black">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Aguardando despachos
                            </p>
                            <h3 id="metric-aguardando-despachos" class="text-xl font-black text-gray-800">0</h3>
                        </div>
                    </div>

                    <!-- Card 3: Entregas Concluídas -->
                    <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-lg font-black">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Entregas Concluídas
                            </p>
                            <h3 id="metric-entregas-concluidas" class="text-xl font-black text-gray-800">0</h3>
                        </div>
                    </div>

                    <!-- Card 4: Restituídos ao estado -->
                    <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center text-red-600 text-lg font-black">
                            <i class="fa-solid fa-user-clock"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Restituídos ao
                                estado</p>
                            <h3 id="metric-restituidos-estado" class="text-xl font-black text-gray-800">0</h3>
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
                            <input type="text" id="inputBusca" placeholder="Buscar processo arguido..."
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 rounded-xl border border-gray-100 text-xs font-semibold text-gray-700 focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <?php if ($perfilId === 2): ?>
                                <button onclick="abrirModalNovoProcesso()"
                                    class="w-full sm:w-auto flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl transition font-bold text-xs shadow-md shadow-indigo-100 cursor-pointer">
                                    <i class="fa-solid fa-users-gear"></i> Novo processo
                                </button>
                        <?php endif; ?>
                    </div>
                </div>

                <script>
                    async function carregarMetricasProcessos() {
                        try {
                            // Ajuste o caminho do fetch de acordo com a localização do seu ficheiro principal em relação à pasta api
                            const response = await fetch('../controller/despacho/obter_metricas.php');
                            const data = await response.json();

                            if (data.success) {
                                // Atualiza os valores nos elementos HTML correspondentes
                                document.getElementById('metric-total-processos').textContent = data.total_processos;
                                document.getElementById('metric-aguardando-despachos').textContent = data.aguardando_despachos;
                                document.getElementById('metric-entregas-concluidas').textContent = data.entregas_concluidas;
                                document.getElementById('metric-restituidos-estado').textContent = data.restituidos_estado;
                            } else {
                                console.error('Erro retornado pela API:', data.message);
                            }
                        } catch (error) {
                            console.error('Erro na requisição das métricas:', error);
                        }
                    }

                    // Executa a função assim que a página carregar
                    document.addEventListener('DOMContentLoaded', () => {
                        carregarMetricasProcessos();
                    });
                </script>

                <!-- GRID DE PROCESSOS (Adicionado ID) -->
                <div id="gridProcessos"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    <!-- Os cartões serão injetados aqui dinamicamente via JS -->
                </div>

                <!-- PAGINAÇÃO FLUTUANTE (Adicionado ID) -->
                <div id="containerPaginacao" class="mt-8 flex items-center justify-center">
                    <!-- Os controlos de paginação serão injetados aqui via JS -->
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputBusca = document.getElementById('inputBusca');
            const gridProcessos = document.getElementById('gridProcessos');
            const containerPaginacao = document.getElementById('containerPaginacao');

            let paginaAtual = 1;
            let filtroAtual = 'todos'; // 'todos', 'interrogatorio', 'restituicao'
            let timeoutId = null;
            let listaTarefasCache = []; // Guarda as tarefas para uso no modal

            // Função principal para carregar os dados via Fetch com Filtro
            window.carregarProcessos = function (pagina = 1) {
                paginaAtual = pagina;
                const termoBusca = inputBusca ? inputBusca.value.trim() : '';

                // Estado visual de carregamento
                gridProcessos.style.opacity = '0.5';

                fetch(`../controller/despacho/listar_processos.php?busca=${encodeURIComponent(termoBusca)}&filtro=${filtroAtual}&pagina=${paginaAtual}`)
                    .then(response => response.json())
                    .then(data => {
                        gridProcessos.style.opacity = '1';
                        if (data.success) {
                            listaTarefasCache = data.tarefas || [];
                            renderizarCards(data.tarefas);
                            renderizarPaginacao(data.pagina_atual || paginaAtual, data.total_paginas || 1);
                        } else {
                            console.error('Erro ao carregar dados:', data.message);
                            gridProcessos.innerHTML = `<div class="col-span-full py-12 text-center text-rose-500 text-xs font-semibold">${data.message}</div>`;
                        }
                    })
                    .catch(error => {
                        gridProcessos.style.opacity = '1';
                        console.error('Erro na requisição:', error);
                    });
            }

            // Função para alterar o filtro ativo via botões
            window.setFiltro = function (tipo) {
                filtroAtual = tipo;

                // Atualiza a aparência visual dos botões de filtro
                document.querySelectorAll('.btn-filtro').forEach(btn => {
                    const isActive = btn.dataset.tipo === tipo;
                    btn.className = isActive
                        ? 'btn-filtro active px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm bg-zinc-900 text-white dark:bg-white dark:text-zinc-950'
                        : 'btn-filtro px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-800';
                });

                carregarProcessos(1); // Recarrega sempre a partir da primeira página
            }

            function renderizarCards(tarefas) {
                if (!tarefas || tarefas.length === 99) {
                    gridProcessos.innerHTML = `
                        <div class="col-span-full py-12 text-center text-gray-400 text-xs font-semibold">
                            <i class="fa-solid fa-folder-open text-3xl mb-2"></i>
                            <p>Nenhuma tarefa ou processo encontrado para esta categoria.</p>
                        </div>
                    `;
                    return;
                }

                gridProcessos.innerHTML = tarefas.map((t, index) => {
                    const isAuto = t.tipo_tarefa === 'auto';
                    const isDeliberado = t.deliberado; // Booleano vindo do PHP

                    // Renderização de Bens
                    let htmlBens = '';
                    if (!isAuto && t.bens && t.bens.length > 0) {
                        const itensBens = t.bens.map(b => `
                            <span class="bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 text-[10px] font-bold px-2 py-0.5 rounded-lg border border-amber-200/50 dark:border-amber-900/30 flex items-center gap-1">
                                <i class="fa-solid fa-box text-amber-500 text-[9px]"></i> ${b}
                            </span>
                        `).join('');

                        htmlBens = `
                            <div class="bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/40 rounded-2xl p-2.5 mb-4">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[10px] font-black uppercase text-amber-800 dark:text-amber-400 flex items-center gap-1.5">
                                        <i class="fa-solid fa-boxes-stacked text-amber-600"></i> Bens Apreendidos (${t.total_bens || t.bens.length})
                                    </span>
                                    ${isDeliberado ? `<span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400"><i class="fa-solid fa-check-circle"></i> Destino: ${t.destino_bem_efetuado === 'dono' ? 'Restituído ao Dono' : 'Perdido ao Estado'}</span>` : ''}
                                </div>
                                <div class="flex flex-wrap gap-1">${itensBens}</div>
                            </div>
                        `;
                    }

                    // Contador de dias / Info para Autos Encaminhados
                    let htmlContadorAuto = '';
                    if (isAuto && isDeliberado && t.prazo_submissao) {
                        const hoje = new Date();
                        const prazo = new Date(t.prazo_submissao);
                        const diffTime = prazo - hoje;
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        let badgeCor = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                        let textoPrazo = `Prazo: ${diffDays} dias restantes`;
                        if (diffDays < 0) {
                            badgeCor = 'bg-rose-50 text-rose-600 border-rose-200';
                            textoPrazo = `Prazo Expirado (${Math.abs(diffDays)}d)`;
                        }

                        htmlContadorAuto = `
                            <div class="mb-3 px-3 py-1.5 rounded-xl border ${badgeCor} text-[10px] font-black flex items-center justify-between">
                                <span><i class="fa-regular fa-clock"></i> ${textoPrazo}</span>
                                <span class="font-mono text-[9px]">Lim: ${t.prazo_submissao}</span>
                            </div>
                        `;
                    }

                    return `
                        <div class="bg-white dark:bg-zinc-900 rounded-[32px] border ${isDeliberado ? 'border-emerald-500/30 dark:border-emerald-500/20' : 'border-gray-100 dark:border-zinc-800'} p-5 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between relative overflow-visible group">
                            <div>
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div class="w-12 h-12 rounded-2xl ${isAuto ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400'} flex flex-col items-center justify-center flex-shrink-0">
                                        <i class="fa-solid ${isAuto ? 'fa-file-signature' : 'fa-boxes-packing'} text-base"></i>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="text-[9px] font-black uppercase tracking-wider px-2 py-1 rounded-md ${isAuto ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400'}">
                                            ${isAuto ? 'Auto Finalizado' : 'Bens a Deliberar'}
                                        </span>
                                        ${isDeliberado ? '<span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400"><i class="fa-solid fa-check"></i> Processado</span>' : ''}
                                    </div>
                                </div>

                                <div class="space-y-1 mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-mono font-bold text-gray-400">Proc. nº ${t.num_processo}</span>
                                    </div>
                                    <h4 class="text-sm font-black text-gray-900 dark:text-white line-clamp-1">
                                        Arguido: ${t.nome_arguido || 'Não Registado'}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium line-clamp-1">
                                        Crime: ${t.tipo_crime || 'Não especificado'}
                                    </p>
                                </div>

                                ${htmlContadorAuto}
                                ${htmlBens}
                            </div>

                            <div class="pt-3 border-t border-gray-100 dark:border-zinc-800 space-y-2.5">
                                <div class="flex items-center justify-between text-[11px] font-bold text-gray-500 dark:text-zinc-400">
                                    <span class="truncate"><i class="fa-solid fa-user-shield text-gray-400 mr-1"></i> ${t.tecnico_nome || 'Não Atribuído'}</span>
                                </div>
                                <button onclick="abrirModalDespachoPorIndice(${index})"
                                    class="w-full text-center block ${isDeliberado ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : (isAuto ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-amber-600 hover:bg-amber-700 text-white')} py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-colors shadow-sm cursor-pointer">
                                    <i class="fa-solid ${isDeliberado ? 'fa-check' : 'fa-gavel'} mr-1"></i> 
                                    ${isDeliberado ? 'Despacho Emitido' : 'Emitir Despacho'}
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            window.abrirModalDespachoPorIndice = function (index) {
                const t = listaTarefasCache[index];
                if (!t) return;

                // Se o despacho já foi emitido, abre a Ficha Informativa via SweetAlert2
                if (t.deliberado) {
                    const isAuto = t.tipo_tarefa === 'auto';
                    const dataFormatada = t.data_deliberacao
                        ? new Date(t.data_deliberacao).toLocaleDateString('pt-PT', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
                        : 'Data não registada';

                    Swal.fire({
                        title: `<span class="text-sm font-black text-gray-800 dark:text-zinc-100 uppercase tracking-wide">Ficha de Registo do Despacho</span>`,
                        html: `
                <div class="text-left space-y-3 text-xs text-gray-600 dark:text-zinc-300 mt-3">
                    <!-- Bloco de Informações do Processo -->
                    <div class="bg-gray-50 dark:bg-zinc-800/50 p-3.5 rounded-2xl border border-gray-100 dark:border-zinc-700/50 space-y-2">
                        <div class="flex justify-between items-center border-b border-gray-200/60 dark:border-zinc-700 pb-1.5">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Nº do Processo</span>
                            <span class="font-black text-gray-800 dark:text-zinc-200">${t.num_processo}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-200/60 dark:border-zinc-700 pb-1.5">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Arguido</span>
                            <span class="font-bold text-gray-800 dark:text-zinc-200">${t.nome_arguido}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Técnico Responsável</span>
                            <span class="font-medium text-gray-700 dark:text-zinc-300">${t.tecnico_nome || 'Não Atribuído'}</span>
                        </div>
                    </div>

                    <!-- Bloco de Detalhes da Deliberação -->
                    <div class="bg-emerald-50/60 dark:bg-emerald-950/20 p-3.5 rounded-2xl border border-emerald-100 dark:border-emerald-900/30 space-y-2">
                        <div class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400 font-black uppercase text-[10px] tracking-wider mb-1">
                            <i class="fa-solid fa-circle-check"></i> Despacho Emitido com Sucesso
                        </div>
                        <div class="flex justify-between items-center border-b border-emerald-200/40 dark:border-emerald-900/40 pb-1.5">
                            <span class="text-emerald-800/70 dark:text-emerald-400/70 font-semibold text-[10px]">Tipo de Tarefa</span>
                            <span class="font-bold text-emerald-900 dark:text-emerald-300">${isAuto ? 'Auto de Interrogatório' : 'Gestão de Bens Apreendidos'}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-emerald-200/40 dark:border-emerald-900/40 pb-1.5">
                            <span class="text-emerald-800/70 dark:text-emerald-400/70 font-semibold text-[10px]">Data da Emissão</span>
                            <span class="font-medium text-emerald-900 dark:text-emerald-300">${dataFormatada}</span>
                        </div>
                        
                        <!-- Condicional baseada no tipo de tarefa -->
                        ${isAuto
                                ? `<div class="flex justify-between items-center">
                                 <span class="text-emerald-800/70 dark:text-emerald-400/70 font-semibold text-[10px]">Prazo de Submissão</span>
                                 <span class="font-bold text-emerald-900 dark:text-emerald-300">${t.prazo_submissao || 'N/D'}</span>
                               </div>`
                                : `<div class="flex justify-between items-center">
                                 <span class="text-emerald-800/70 dark:text-emerald-400/70 font-semibold text-[10px]">Destino do Bem</span>
                                 <span class="font-bold text-emerald-900 dark:text-emerald-300">${t.destino_bem_efetuado === 'dono' ? 'Restituído ao Dono' : 'Perdido a Favor do Estado'}</span>
                               </div>`
                            }
                    </div>
                </div>
            `,
                        confirmButtonText: 'Fechar Ficha',
                        confirmButtonColor: '#059669',
                        customClass: {
                            popup: 'rounded-3xl p-6 dark:bg-zinc-900 shadow-xl border border-gray-100 dark:border-zinc-800',
                            confirmButton: 'rounded-xl text-xs font-black uppercase tracking-wider px-6 py-2.5 shadow-sm'
                        }
                    });
                    return; // Interrompe a execução para não abrir o modal de formulário
                }

                // --- FLUXO PADRÃO (Caso ainda NÃO esteja deliberado) ---

                // Funções auxiliares seguras para evitar erros caso o elemento não exista no HTML
                const setVal = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.value = val;
                };
                const setText = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.innerText = val;
                };

                // Preenche campos ocultos e textos com segurança
                setVal('modal_processo_id', t.processo_id);
                setVal('modal_tipo_tarefa', t.tipo_tarefa);
                setText('modalNumProcesso', `Processo nº ${t.num_processo}`);
                setText('modalNomeArguido', `Arguido: ${t.nome_arguido}`);

                const modalBadge = document.getElementById('modalBadgeTipo');
                const modalTitulo = document.getElementById('modalTitulo');
                const camposAuto = document.getElementById('camposAuto');
                const camposBens = document.getElementById('camposBens');

                if (t.tipo_tarefa === 'auto') {
                    if (modalBadge) {
                        modalBadge.innerText = 'AUTO FINALIZADO';
                        modalBadge.className = 'text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-blue-50 text-blue-600';
                    }
                    if (modalTitulo) modalTitulo.innerText = 'Encaminhar Auto de Interrogatório';
                    if (camposAuto) camposAuto.classList.remove('hidden');
                    if (camposBens) camposBens.classList.add('hidden');
                } else {
                    if (modalBadge) {
                        modalBadge.innerText = 'RESTITUIR / PERDER BEM';
                        modalBadge.className = 'text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-amber-50 text-amber-600';
                    }
                    if (modalTitulo) modalTitulo.innerText = 'Despacho sobre Bens Apreendidos';
                    if (camposBens) camposBens.classList.remove('hidden');
                    if (camposAuto) camposAuto.classList.add('hidden');
                }

                // Abre o modal de emissão de despacho
                const modal = document.getElementById('modalDespacho');
                if (modal) {
                    modal.classList.remove('hidden');
                } else {
                    console.error('Elemento com ID "modalDespacho" não foi encontrado no HTML.');
                }
            };

            window.fecharModalDespacho = function () {
                const modal = document.getElementById('modalDespacho');
                if (modal) modal.classList.add('hidden');
                const form = document.getElementById('formDespacho');
                if (form) form.reset();
            }

            // Renderiza a paginação estilo iOS dinamicamente
            function renderizarPaginacao(atual, total) {
                if (!containerPaginacao) return;

                if (total <= 1) {
                    containerPaginacao.innerHTML = '';
                    return;
                }

                let botoesPaginas = '';
                for (let i = 1; i <= total; i++) {
                    if (i === 1 || i === total || (i >= atual - 1 && i <= atual + 1)) {
                        if (i === atual) {
                            botoesPaginas += `<button type="button" class="px-3.5 py-1.5 rounded-full text-xs font-black bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 shadow-sm transition-all">${i}</button>`;
                        } else {
                            botoesPaginas += `<button type="button" onclick="carregarProcessos(${i})" class="px-3.5 py-1.5 rounded-full text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/80 dark:hover:bg-zinc-800/60 transition-all cursor-pointer">${i}</button>`;
                        }
                    } else if (i === atual - 2 || i === atual + 2) {
                        botoesPaginas += `<span class="text-xs text-zinc-400 px-1 font-bold select-none">•••</span>`;
                    }
                }

                containerPaginacao.innerHTML = `
                    <nav aria-label="Navegação de Páginas" class="inline-flex items-center gap-1.5 p-1.5 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 rounded-full shadow-xl shadow-zinc-950/5 transition-all">
                        <button type="button" ${atual <= 1 ? 'disabled' : `onclick="carregarProcessos(${atual - 1})"`} class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                        <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-800 my-auto"></div>
                        <div class="flex items-center gap-1 px-1">${botoesPaginas}</div>
                        <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-800 my-auto"></div>
                        <button type="button" ${atual >= total ? 'disabled' : `onclick="carregarProcessos(${atual + 1})"`} class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </nav>
                `;
            }

            // Evento de Digitação na Barra de Busca (com Debounce)
            if (inputBusca) {
                inputBusca.addEventListener('input', () => {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        carregarProcessos(1);
                    }, 300);
                });
            }

            // Carga inicial ao abrir a página
            carregarProcessos(1);
        });

    </script>

    <!-- MODAL UNIFICADO DE DESPACHO -->
    <div id="modalDespacho"
        class="hidden fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-zinc-900 rounded-3xl w-full max-w-lg p-6 shadow-2xl border border-gray-100 dark:border-zinc-800 relative">

            <!-- Cabeçalho do Modal -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-zinc-800 mb-5">
                <div>
                    <span id="modalBadgeTipo"
                        class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-600">
                        Despacho
                    </span>
                    <h3 id="modalTitulo" class="text-base font-black text-gray-900 dark:text-white mt-1">
                        Instruir Despacho
                    </h3>
                </div>
                <button type="button" onclick="fecharModalDespacho()"
                    class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Formulário Dinâmico -->
            <form id="formDespacho" onsubmit="submeterDespacho(event)" class="space-y-4">
                <input type="hidden" id="modal_processo_id" name="processo_id">
                <input type="hidden" id="modal_tipo_tarefa" name="tipo_tarefa">

                <!-- Resumo do Processo/Arguido -->
                <div class="bg-gray-50 dark:bg-zinc-800/50 p-3 rounded-2xl border border-gray-100 dark:border-zinc-800">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Processo Referência</p>
                    <p id="modalNumProcesso" class="text-xs font-black text-gray-800 dark:text-zinc-200"></p>
                    <p id="modalNomeArguido" class="text-xs font-semibold text-gray-500 dark:text-zinc-400"></p>
                </div>

                <!-- Campos para Auto de Interrogatório Finalizado -->
                <div id="camposAuto" class="hidden space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-1">Prazo limite de
                            Submissão</label>
                        <input type="date" id="prazo_submissao" name="prazo_submissao"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 text-xs font-semibold text-gray-800 dark:text-zinc-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-1">Órgão / Magistrado
                            de Destino</label>
                        <input type="text" id="destino_submissao" name="destino_submissao"
                            placeholder="Ex: Tribunal da Comarca de Luanda / Juiz de Garantias"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 text-xs font-semibold text-gray-800 dark:text-zinc-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-1">Responsável pelo
                            Envio</label>
                        <input type="text" id="responsavel_envio" name="responsavel_envio"
                            placeholder="Nome do oficial ou técnico responsável"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 text-xs font-semibold text-gray-800 dark:text-zinc-200">
                    </div>
                </div>

                <!-- Campos para Bens Apreendidos -->
                <div id="camposBens" class="hidden space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-1">Destino do Bem
                            Apreendido</label>
                        <select id="destino_bem" name="destino_bem"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 text-xs font-bold text-gray-800 dark:text-zinc-200">
                            <option value="dono">Restituição ao Legítimo Proprietário (Dono)</option>
                            <option value="estado">Declaração de Perda a Favor do Estado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-1">Fundamentação /
                            Descrição da Decisão</label>
                        <textarea id="descricao_decisao" name="descricao_decisao" rows="3"
                            placeholder="Insira o texto descritivo do despacho de restituição ou perda..."
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 text-xs font-semibold text-gray-800 dark:text-zinc-200"></textarea>
                    </div>
                </div>

                <!-- Botões de Ação do Modal -->
                <div class="flex items-center gap-3 pt-3">
                    <button type="button" onclick="fecharModalDespacho()"
                        class="w-1/2 py-2.5 rounded-xl border border-gray-200 dark:border-zinc-700 text-xs font-bold text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="w-1/2 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition cursor-pointer">
                        Emitir Despacho
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.submeterDespacho = function (event) {
            event.preventDefault(); // Evita que a página recarregue

            const form = document.getElementById('formDespacho');
            const formData = new FormData(form);

            // Feedback de carregamento
            Swal.fire({
                title: 'Processando...',
                text: 'A guardar o despacho no sistema.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch('../controller/despacho/processar_despacho.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: data.message,
                            confirmButtonColor: '#4f46e5'
                        }).then(() => {
                            fecharModalDespacho(); // Fecha o modal
                            carregarProcessos(1);   // Recarrega a grid para ver a alteração
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro de Sistema',
                        text: 'Não foi possível conectar ao servidor.'
                    });
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