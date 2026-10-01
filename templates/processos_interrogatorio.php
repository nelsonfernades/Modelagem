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

// Busca estritamente apenas os processos afetos ao técnico responsável ou criados por ele
$sqlProcessos = "SELECT id, num_processo FROM processos WHERE tecnico_responsavel_id = ? OR criado_por_id = ? ORDER BY id DESC";
$stmtProc = $conn->prepare($sqlProcessos);
$stmtProc->bind_param("ii", $userLogado['id'], $userLogado['id']);
$stmtProc->execute();
$listaProcessos = $stmtProc->get_result();
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
                            class="relative flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600/10 text-white transition-all">
                            <div class="absolute left-0 w-1 h-6 bg-blue-400 rounded-r-full"></div>
                            <i class="fa-solid fa-file w-5 text-blue-400 transition-colors"></i>
                            <span class="text-sm font-medium text-blue-400">Proc. Interrogatório</span>
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

            <main class="p-6">
                <!-- 1. TOPO DA PÁGINA & CONTADORES RÁPIDOS -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
                    <div>
                        <h2 class="text-2xl font-black text-gray-800 dark:text-white tracking-tight">Processos para
                            Interrogatórios</h2>
                        <p class="text-sm font-medium text-gray-500 dark:text-zinc-400 mt-1">Acompanhe, baixe e emita
                            pareceres sobre os arguidos, e seus antecedentes.</p>
                    </div>

                    <div
                        class="flex items-center gap-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-100/60 dark:border-amber-900/50 px-5 py-3 rounded-2xl self-start lg:self-auto">
                        <div
                            class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 text-base font-black">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <div>
                            <p
                                class="text-[10px] font-bold text-amber-700/80 dark:text-amber-400 uppercase tracking-wider">
                                Aguardando Avaliação</p>
                            <!-- ID adicionado para atualizar dinamicamente -->
                            <h3 id="contador-aguardando"
                                class="text-base font-black text-amber-800 dark:text-amber-300">0 Processos</h3>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 2. BARRA DE FILTROS, BUSCA E DEPÓSITO -->
                <!-- ========================================== -->
                <div
                    class="bg-white dark:bg-zinc-900 p-4 rounded-3xl border border-gray-100 dark:border-zinc-800 shadow-sm flex flex-col xl:flex-row xl:items-center justify-between gap-4 mb-6">

                    <!-- Botões de Filtros (Empilhados e adaptativos em telemóveis) -->
                    <div class="flex flex-wrap items-center gap-2">
                        <button onclick="definirFiltro('total')"
                            class="flex-1 sm:flex-none justify-center bg-gray-50 dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-600 dark:text-zinc-300 px-3 sm:px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2">
                            <span>Total</span>
                            <span id="badge-total"
                                class="px-2 py-0.5 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-lg text-[10px]">0</span>
                        </button>
                        <button onclick="definirFiltro('finalizados')"
                            class="flex-1 sm:flex-none justify-center bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-400 px-3 sm:px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2">
                            <span>Finalizados</span>
                            <span id="badge-finalizados"
                                class="px-2 py-0.5 bg-emerald-200/60 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 rounded-lg text-[10px]">0</span>
                        </button>
                        <button onclick="definirFiltro('pendentes')"
                            class="flex-1 sm:flex-none justify-center bg-orange-50 dark:bg-orange-950/40 hover:bg-orange-100 text-orange-700 dark:text-orange-400 px-3 sm:px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2">
                            <span>Pendentes</span>
                            <span id="badge-pendentes"
                                class="px-2 py-0.5 bg-orange-200/60 dark:bg-orange-900/50 text-orange-800 dark:text-orange-300 rounded-lg text-[10px]">0</span>
                        </button>
                    </div>

                    <!-- Bloco de Pesquisa e Ação (100% largura em mobile, alinhado em xl) -->
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto">

                        <!-- Input de Busca -->
                        <div class="relative w-full sm:w-72">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-gray-400 text-xs"></i>
                            <input type="text" id="input-busca" placeholder="Buscar processo, arguido..."
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 dark:bg-zinc-800/50 rounded-xl border border-gray-100 dark:border-zinc-700 text-xs font-semibold text-gray-700 dark:text-zinc-200 focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <!-- Botão Instruir Processo -->
                        <button onclick="abrirAutoInterrogatorio(null, true)"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl transition font-bold text-xs shadow-md shadow-indigo-100 dark:shadow-none cursor-pointer">
                            <i class="fa-solid fa-cloud"></i> Instruir Processo
                        </button>

                    </div>
                </div>

                <!-- 3. GRID DE PROCESSOS: Onde o JS injeta os cards via API -->
                <div id="grid-processos"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    <!-- Os cards serão carregados dinamicamente aqui pelo JavaScript -->
                </div>

                <!-- 4. PAGINAÇÃO ESTILO IPHONE (iOS FLUTUANTE) -->
                <div class="mt-8 flex items-center justify-center">
                    <!-- ID adicionado no nav para gerir os números de páginas dinamicamente -->
                    <nav id="paginacao-nav-container" aria-label="Navegação de Páginas"
                        class="inline-flex items-center gap-1.5 p-1.5 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 rounded-full shadow-xl shadow-zinc-950/5 transition-all">
                        <!-- Preenchido automaticamente pelo JavaScript -->
                    </nav>
                </div>
            </main>
        </div>
    </div>

    <script>
        let filtroAtual = 'total';
        let paginaAtual = 1;
        let termoBusca = '';

        // Executa ao carregar a página
        document.addEventListener('DOMContentLoaded', () => {
            carregarProcessos();

            // Evento de busca com debounce (evita requisições excessivas a cada tecla)
            let timeoutBusca;
            const inputBusca = document.getElementById('input-busca');
            if (inputBusca) {
                inputBusca.addEventListener('input', (e) => {
                    clearTimeout(timeoutBusca);
                    timeoutBusca = setTimeout(() => {
                        termoBusca = e.target.value;
                        paginaAtual = 1;
                        carregarProcessos();
                    }, 300);
                });
            }
        });

        // Função para mudar o filtro ativo pelos botões
        function definirFiltro(tipo) {
            filtroAtual = tipo;
            paginaAtual = 1;

            // Atualizar classes visuais dos botões de filtro se necessário...
            carregarProcessos();
        }

        async function carregarProcessos(pagina = 1) {
            paginaAtual = pagina;
            const grid = document.getElementById('grid-processos');
            if (!grid) return;

            grid.style.opacity = '0.5';

            try {
                const response = await fetch(`../controller/auto-interrogatorio/listar_processos.php?filtro=${filtroAtual}&busca=${encodeURIComponent(termoBusca)}&pagina=${paginaAtual}`);
                const data = await response.json();

                if (!data.sucesso) {
                    console.error('Erro ao carregar processos');
                    return;
                }

                // ATUALIZAR TODAS AS MÉTRICAS E CONTADORES
                const elAguardando = document.getElementById('contador-aguardando');
                if (elAguardando) elAguardando.innerText = `${data.total_pendentes} Processos`;

                const badgeTotal = document.getElementById('badge-total');
                if (badgeTotal) badgeTotal.innerText = data.total_geral;

                const badgeFin = document.getElementById('badge-finalizados');
                if (badgeFin) badgeFin.innerText = data.total_finalizados;

                const badgePend = document.getElementById('badge-pendentes');
                if (badgePend) badgePend.innerText = data.total_pendentes;

                // Renderizar os Cards de Processos
                grid.innerHTML = '';

                // 🔴 Correção 1: Corrigido de 99 para 0 para verificar se não há processos
                if (!data.processos || data.processos.length === 0) {
                    grid.innerHTML = `
                <div class="col-span-full py-12 text-center text-gray-400 font-medium text-xs bg-white dark:bg-zinc-900 rounded-[32px] border border-gray-100 dark:border-zinc-800">
                    <i class="fa-solid fa-folder-open text-3xl mb-2"></i>
                    <p>Nenhum processo encontrado.</p>
                </div>
            `;
                } else {
                    data.processos.forEach(p => {
                        const iniciais = p.arguido_nome ? p.arguido_nome.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase() : 'ARG';
                        const estadoClasse = p.estado === 'finalizado'
                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50'
                            : 'bg-orange-50 text-orange-700 dark:bg-orange-950/50';

                        grid.innerHTML += `
                    <div class="bg-white dark:bg-zinc-900 rounded-[32px] border border-gray-100 dark:border-zinc-800 p-5 shadow-sm hover:shadow-xl hover:shadow-indigo-50/40 transition-all flex flex-col justify-between relative overflow-visible group">
                        <div>
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xs font-black uppercase flex-shrink-0">
                                    ${iniciais}
                                </div>
                            </div>
                            <div class="space-y-1 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block text-[9px] font-black uppercase ${estadoClasse} px-2 py-0.5 rounded-md">
                                        ${p.estado || 'Rascunho'}
                                    </span>
                                    <span class="text-[10px] font-mono font-bold text-gray-400">Proc. nº ${p.numero_processo}</span>
                                </div>
                                <div class="text-sm font-black text-gray-900 dark:text-white truncate">
                                    ${p.arguido_nome || 'Desconhecido'}
                                </div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                                    BI: ${p.nif || 'N/A'} · Cont.: ${p.nip || 'N/A'}
                                </div>
                                <h4 class="text-xs font-black text-gray-800 dark:text-zinc-200 line-clamp-2 pt-1 leading-snug">
                                    ${p.crime || 'Sem especificação de crime'}
                                </h4>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-100 dark:border-zinc-800 space-y-2.5">
                            <div class="text-xs font-bold text-gray-600 dark:text-zinc-400 truncate flex items-center justify-between">
                                <span class="truncate"><i class="fa-solid fa-user-shield text-gray-400 mr-1.5"></i> ${p.tecnico_nome || 'Técnico'}</span>
                                <span class="text-[10px] text-gray-400 font-mono">${p.criado_em ? p.criado_em.split(' ')[0] : ''}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <!-- Botão de Visualizar Ficha -->
                                <button onclick="visualizarFicha('${p.numero_processo}')" class="text-center block bg-slate-600 hover:bg-slate-700 text-white py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-colors cursor-pointer shadow-sm">
                                    Visualizar Ficha
                                </button>
                                
                                <!-- Botão de Editar Auto -->
                                <button onclick="carregarEAbriAutoPorProcesso('${p.numero_processo}')" class="text-center block bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-colors cursor-pointer shadow-sm">
                                    Editar Auto
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                    });
                }

                // 🔴 Correção 2: Passagem correta dos argumentos de paginação esperados pelo objeto JSON retornado
                if (typeof renderizarPaginacao === 'function') {
                    renderizarPaginacao({
                        pagina_atual: data.pagina_atual,
                        total_paginas: data.total_paginas
                    });
                }

            } catch (error) {
                console.error('Erro na requisição fetch:', error);
            } finally {
                grid.style.opacity = '1';
            }
        }

        async function carregarEAbriAutoPorProcesso(numeroProcesso) {
            Swal.fire({
                title: 'A verificar processo...',
                text: 'A consultar os dados na base de dados',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Faz a chamada ao endpoint buscar.php que partilhou
                const resposta = await fetch(`../controller/auto-interrogatorio/buscar.php?numero=${encodeURIComponent(numeroProcesso)}`);
                const resultado = await resposta.json();

                // Se o processo já estiver finalizado (instruído), bloqueia e avisa o utilizador
                if (!resultado.sucesso) {
                    Swal.fire({
                        title: 'Processo Já Instruído',
                        text: resultado.mensagem,
                        icon: 'warning',
                        confirmButtonColor: '#4f46e5'
                    });
                    return; // Interrompe e não abre o modal
                }

                Swal.close();

                const dados = resultado.dados;
                const modal = document.getElementById('modalAutoInterrogatorio');
                const form = document.getElementById('formAutoInterrogatorio');
                const badge = document.getElementById('badgeEstadoAuto');

                if (!modal || !form) {
                    console.error("Modal ou formulário não encontrados no DOM.");
                    return;
                }

                form.reset();

                // Altera a cor e texto do badge consoante o estado
                if (dados.estado_auto === 'finalizado') {
                    badge.innerText = "Auto Finalizado";
                    badge.className = "text-[10px] font-black uppercase px-2.5 py-1 rounded-lg bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400";
                } else if (resultado.origem === 'rascunho_existente') {
                    badge.innerText = "Rascunho Existente";
                    badge.className = "text-[10px] font-black uppercase px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400";
                } else {
                    badge.innerText = "Novo Auto (Baseado no Arguido)";
                    badge.className = "text-[10px] font-black uppercase px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400";
                }

                // Preenche todos os campos do formulário com os dados retornados pelo PHP
                document.getElementById('autoId').value = dados.id || '';
                document.getElementById('autoProcessoId').value = dados.processo_id || '';
                document.getElementById('autoArguidoId').value = dados.arguido_id || '';
                document.getElementById('autoUtilizadorId').value = dados.utilizador_id || '';
                document.getElementById('autoEstado').value = dados.estado_auto || 'rascunho';

                document.getElementById('autoProcNum').value = dados.numero_processo || '';
                document.getElementById('autoData').value = dados.data_diligencia || new Date().toISOString().split('T')[0];
                document.getElementById('autoCidade').value = dados.cidade || 'Luanda';
                document.getElementById('autoMagistrado').value = dados.magistrado_mp || '';
                document.getElementById('autoDefensor').value = dados.defensor_advogado || '';
                document.getElementById('autoEscrivao').value = dados.oficial_escrivao || '';

                document.getElementById('autoNomeArguido').value = dados.nome_completo || '';
                document.getElementById('autoEstadoCivil').value = dados.estado_civil || '';
                document.getElementById('autoProfissao').value = dados.profissao || '';
                document.getElementById('autoIdade').value = dados.idade || '';
                document.getElementById('autoDataNasc').value = dados.data_nascimento || '';
                document.getElementById('autoNaturalidade').value = dados.naturalidade || '';
                document.getElementById('autoPai').value = dados.nome_pai || '';
                document.getElementById('autoMae').value = dados.nome_mae || '';
                document.getElementById('autoResidencia').value = dados.residencia_habitual || '';
                document.getElementById('autoBINum').value = dados.bi_numero || '';
                document.getElementById('autoBIEmissor').value = dados.bi_arquivo_emissao || '';
                document.getElementById('autoBIData').value = dados.bi_data_emissao || '';

                document.getElementById('autoAntecedentes').value = dados.antecedentes_criminais || '';
                document.getElementById('autoDeclaracoes').value = dados.transcricao_declaracoes || '';

                // ** Abre o modal de forma garantida **
                modal.classList.remove('hidden');

            } catch (erro) {
                console.error('Erro ao abrir o auto:', erro);
                Swal.fire({
                    title: 'Erro de Comunicação',
                    text: 'Ocorreu um erro ao tentar processar a solicitação.',
                    icon: 'error',
                    confirmButtonColor: '#991b1b'
                });
            }
        }

        // Renderizador da paginação estilo iOS
        function renderizarPaginacao(atual, total) {
            const nav = document.getElementById('paginacao-nav-container');
            if (!nav) return;

            let html = `
                <button type="button" onclick="carregarProcessos(${atual - 1})" ${atual <= 1 ? 'disabled' : ''} class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-800 my-auto"></div>
                <div class="flex items-center gap-1 px-1">
            `;

            for (let i = 1; i <= total; i++) {
                if (i === atual) {
                    html += `<button type="button" class="px-3.5 py-1.5 rounded-full text-xs font-black bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 shadow-sm">${i}</button>`;
                } else {
                    html += `<button type="button" onclick="carregarProcessos(${i})" class="px-3.5 py-1.5 rounded-full text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all cursor-pointer">${i}</button>`;
                }
            }

            html += `
                </div>
                <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-800 my-auto"></div>
                <button type="button" onclick="carregarProcessos(${atual + 1})" ${atual >= total ? 'disabled' : ''} class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            `;
            nav.innerHTML = html;
        }
    </script>

    <!-- ========================================== -->
    <!-- OVERLAY E PAINEL DO AUTO DE INTERROGATÓRIO -->
    <!-- ========================================== -->
    <div id="drawerAutoInterrogatorio" class="fixed inset-0 z-50 hidden" aria-labelledby="slide-over-title"
        role="dialog" aria-modal="true">
        <!-- Fundo escuro com efeito Blur -->
        <div id="drawerOverlayAuto" onclick="fecharAutoInterrogatorio()"
            class="fixed inset-0 bg-zinc-950/60 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-0 sm:pl-10">
            <!-- Container do Painel -->
            <div id="drawerContentAuto"
                class="w-screen max-w-3xl bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 ease-in-out">

                <!-- CABEÇALHO DO PAINEL -->
                <div
                    class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-950/50">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black shadow-md shadow-indigo-500/20">
                            <i class="fa-solid fa-file-pen text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black uppercase text-zinc-900 dark:text-white tracking-wider">Auto
                                de Interrogatório</h3>
                            <p class="text-[11px] text-zinc-500 font-mono">PROCESSO Nº <span
                                    id="autoDrawerNumProcesso">#2026/0489</span></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="gerarPdfAutoInterrogatorio()"
                            class="p-2 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg transition-colors cursor-pointer"
                            title="Exportar PDF">
                            <i class="fa-solid fa-print text-base"></i>
                        </button>
                        <button type="button" onclick="fecharFicha()"
                            class="p-2 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg transition-colors cursor-pointer">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- CORPO DO PAINEL (SCROLLÁVEL) -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">

                    <!-- SEÇÃO 1: DADOS DA DILIGÊNCIA E MAGISTRADO -->
                    <div class="space-y-3">
                        <h4
                            class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-scale-balanced"></i> 1. Dados do Processo & Diligência
                        </h4>
                        <div
                            class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-zinc-50/50 dark:bg-zinc-950/20 p-4 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 text-xs">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Data da
                                    Diligência</span>
                                <span id="autoDrawerData"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">06/08/2026</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Cidade / Local</span>
                                <span id="autoDrawerCidade"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">Luanda</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Estado do Auto</span>
                                <span id="autoDrawerEstadoBadge"
                                    class="font-bold text-sky-600 dark:text-sky-400">Rascunho Oficial</span>
                            </div>
                            <div
                                class="sm:col-span-3 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/60 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-zinc-400 block">Magistrado do
                                        MP</span>
                                    <span id="autoDrawerMagistrado"
                                        class="font-semibold text-zinc-800 dark:text-zinc-200">Dr. António Silva</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-zinc-400 block">Oficial /
                                        Escrivão</span>
                                    <span id="autoDrawerEscrivao"
                                        class="font-semibold text-zinc-800 dark:text-zinc-200">Carlos Alberto</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-zinc-400 block">Defensor /
                                        Advogado</span>
                                    <span id="autoDrawerDefensor"
                                        class="font-semibold text-zinc-800 dark:text-zinc-200">Dr.ª Maria
                                        Joaquina</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-zinc-150 dark:border-zinc-800" />

                    <!-- SEÇÃO 2: QUALIFICAÇÃO DO ARGUIDO -->
                    <div class="space-y-3">
                        <h4
                            class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-user-shield"></i> 2. Qualificação Completa do Arguido
                        </h4>
                        <div
                            class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-zinc-50/50 dark:bg-zinc-950/20 p-4 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 text-xs">
                            <div class="sm:col-span-2">
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Nome Completo</span>
                                <span id="autoDrawerNome" class="font-bold text-zinc-900 dark:text-white uppercase">João
                                    Pedro de Carvalho</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Estado Civil</span>
                                <span id="autoDrawerEstadoCivil"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">Solteiro(a)</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Profissão</span>
                                <span id="autoDrawerProfissao"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">Técnico de Informática</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Idade / Data
                                    Nasc.</span>
                                <span id="autoDrawerNasc" class="font-semibold text-zinc-800 dark:text-zinc-200">32 anos
                                    (14/05/1994)</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Naturalidade</span>
                                <span id="autoDrawerNaturalidade"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">Luanda, Luanda</span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Filiação (Pai e
                                    Mãe)</span>
                                <span id="autoDrawerFiliacao"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">Manuel de Carvalho & Helena
                                    Domingos</span>
                            </div>
                            <div class="sm:col-span-3">
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Residência
                                    Habitual</span>
                                <span id="autoDrawerResidencia"
                                    class="font-semibold text-zinc-800 dark:text-zinc-200">Rua dos Cedros, Casa nº 14,
                                    Maianga, Luanda</span>
                            </div>
                            <div
                                class="sm:col-span-3 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/60 grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-zinc-400 block">Bilhete de
                                        Identidade</span>
                                    <span id="autoDrawerBI"
                                        class="font-mono font-bold text-zinc-800 dark:text-zinc-200">004892314LA042</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-zinc-400 block">Arquivo Emissor /
                                        Data</span>
                                    <span id="autoDrawerBIEmissor"
                                        class="font-semibold text-zinc-800 dark:text-zinc-200">Luanda
                                        (12/03/2020)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEÇÃO 3: ANTECEDENTES CRIMINAIS -->
                    <div class="space-y-3">
                        <h4
                            class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved"></i> 3. Antecedentes Criminais & Condições Pessoais
                        </h4>
                        <div
                            class="p-3 bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs">
                            <p id="autoDrawerAntecedentes"
                                class="text-zinc-700 dark:text-zinc-300 font-medium leading-relaxed">
                                O arguido declara não possuir antecedentes criminais averbados nos registos oficiais e
                                manifesta plenas condições de saúde física e mental para prestar declarações sem coação.
                            </p>
                        </div>
                    </div>

                    <!-- SEÇÃO 4: TRANSCRIÇÃO DAS DECLARAÇÕES -->
                    <div class="space-y-3">
                        <h4
                            class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-comments"></i> 4. Transcrição das Declarações
                        </h4>
                        <div
                            class="p-4 bg-zinc-900 text-zinc-100 dark:bg-zinc-950 border border-zinc-800 rounded-xl text-xs font-mono leading-relaxed max-h-48 overflow-y-auto">
                            <p id="autoDrawerDeclaracoes" class="whitespace-pre-wrap">Aos seis dias do mês de Agosto de
                                2026, nesta secção de instrução criminal, perante o Magistrado do Ministério Público...
                                "Compreendo perfeitamente a acusação que me é imputada e desejo declarar em minha
                                defesa..."</p>
                        </div>
                    </div>

                    <div
                        class="bg-rose-50/60 dark:bg-rose-950/20 border border-rose-200/60 dark:border-rose-900/40 rounded-2xl p-3 mb-3 text-[11px] text-rose-800 dark:text-rose-400 font-medium">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        <strong>Advertência Legal (Geração de Declarações):</strong> Somente os autos terminados e
                        assinados pelo Magistrado do Ministério Público podem ser considerados válidos para efeitos
                        legais.
                    </div>

                </div>

                <!-- RODAPÉ DO PAINEL -->
                <div
                    class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50 flex justify-between items-center">
                    <!-- Input oculto para reter o ID do auto no drawer -->
                    <input type="hidden" id="autoDrawerId" value="">

                    <!-- Botão de Exportar PDF integrado -->
                    <button type="button" onclick="exportarAutoPdf(document.getElementById('autoDrawerId').value)"
                        class="px-4 py-2 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 text-xs font-bold uppercase rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf text-orange-600"></i>
                        Exportar PDF
                    </button>
                    <button type="button" onclick="fecharFicha()"
                        class="px-5 py-2 bg-indigo-600 text-white text-xs font-black uppercase rounded-xl hover:bg-indigo-700 transition-all cursor-pointer shadow-md shadow-indigo-600/20">
                        Concluído
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        // 1. Visualizar Ficha e Carregar Dados do Auto de Interrogatório
        window.visualizarFicha = async function (numeroProcesso) {
            if (!numeroProcesso) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aviso',
                    text: 'Número de processo inválido.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            try {
                const response = await fetch(`../controller/auto-interrogatorio/get_dados_auto_ficha.php?numero=${encodeURIComponent(numeroProcesso)}`);
                const resultado = await response.json();

                if (!resultado.sucesso) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Não Encontrado',
                        text: resultado.mensagem || 'Ainda não existe nenhum auto registado para este processo.',
                        confirmButtonColor: '#4f46e5'
                    });
                    return;
                }

                const d = resultado.dados;

                // INCORPORAÇÃO: Guarda o ID do auto no input hidden do drawer para o botão de PDF o ler
                const inputAutoId = document.getElementById('autoDrawerId');
                if (inputAutoId) {
                    inputAutoId.value = d.id || '';
                }

                // Secção 1: Dados da Diligência & Magistrado
                document.getElementById('autoDrawerNumProcesso').innerText = d.numero_processo || numeroProcesso;
                document.getElementById('autoDrawerData').innerText = d.data_diligencia || '---';
                document.getElementById('autoDrawerCidade').innerText = d.cidade || 'Luanda';

                const badgeEstado = document.getElementById('autoDrawerEstadoBadge');
                if (badgeEstado) {
                    if (d.estado_auto === 'finalizado') {
                        badgeEstado.innerText = 'Finalizado';
                        badgeEstado.className = 'font-bold text-emerald-600 dark:text-emerald-400';
                    } else {
                        badgeEstado.innerText = 'Rascunho Oficial';
                        badgeEstado.className = 'font-bold text-sky-600 dark:text-sky-400';
                    }
                }

                document.getElementById('autoDrawerMagistrado').innerText = d.magistrado_mp || '---';
                document.getElementById('autoDrawerEscrivao').innerText = d.oficial_escrivao || '---';
                document.getElementById('autoDrawerDefensor').innerText = d.defensor_advogado || '---';

                // Secção 2: Qualificação Completa do Arguido
                document.getElementById('autoDrawerNome').innerText = d.nome_completo || '---';
                document.getElementById('autoDrawerEstadoCivil').innerText = d.estado_civil || '---';
                document.getElementById('autoDrawerProfissao').innerText = d.profissao || '---';

                const idadeStr = d.idade ? `${d.idade} anos` : '';
                const nascStr = d.data_nascimento ? `(${d.data_nascimento})` : '';
                document.getElementById('autoDrawerNasc').innerText = `${idadeStr} ${nascStr}`.trim() || '---';

                document.getElementById('autoDrawerNaturalidade').innerText = d.naturalidade || '---';
                document.getElementById('autoDrawerFiliacao').innerText = `${d.nome_pai || 'Desconhecido'} & ${d.nome_mae || 'Desconhecido'}`;
                document.getElementById('autoDrawerResidencia').innerText = d.residencia_habitual || '---';
                document.getElementById('autoDrawerBI').innerText = d.bi_numero || '---';
                document.getElementById('autoDrawerBIEmissor').innerText = `${d.bi_arquivo_emissao || '---'} (${d.bi_data_emissao || '---'})`;

                // Secção 3 & 4: Antecedentes e Declarações
                document.getElementById('autoDrawerAntecedentes').innerText = d.antecedentes_criminais || 'Nenhum antecedente registado.';
                document.getElementById('autoDrawerDeclaracoes').innerText = d.transcricao_declaracoes || 'Nenhuma transcrição efetuada.';

                // Abrir o Drawer
                window.abrirAutoInterrogatorioDrawer();

            } catch (error) {
                console.error('Erro na requisição da ficha:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de Servidor',
                    text: 'Ocorreu um erro ao comunicar com o servidor.',
                    confirmButtonColor: '#4f46e5'
                });
            }
        };

        // 2. Abrir o Drawer com animação
        window.abrirAutoInterrogatorioDrawer = function () {
            const drawer = document.getElementById('drawerAutoInterrogatorio');
            const overlay = document.getElementById('drawerOverlayAuto');
            const content = document.getElementById('drawerContentAuto');

            if (!drawer) return;

            drawer.classList.remove('hidden');
            setTimeout(() => {
                if (overlay) {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');
                }
                if (content) {
                    content.classList.remove('translate-x-full');
                    content.classList.add('translate-x-0');
                }
            }, 10);
        };

        // 3. Fechar o Drawer (Padronizado e compatível com ambos os nomes para evitar conflitos)
        window.fecharFicha = function () {
            const drawer = document.getElementById('drawerAutoInterrogatorio');
            const overlay = document.getElementById('drawerOverlayAuto');
            const content = document.getElementById('drawerContentAuto');

            if (!drawer) return;

            if (overlay) {
                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
            }
            if (content) {
                content.classList.remove('translate-x-0');
                content.classList.add('translate-x-full');
            }

            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 300);
        };

        // 4. Exportar Auto de Interrogatório para PDF (com feedback visual)
        function exportarAutoPdf(autoId) {
            if (!autoId) {
                Swal.fire({
                    title: 'Aviso',
                    text: 'Nenhum auto gravado selecionado para exportar.',
                    icon: 'warning',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            // Exibe o SweetAlert com o indicador de progresso / loading
            Swal.fire({
                title: 'A preparar documento...',
                text: 'A gerar o Auto de Interrogatório no modelo oficial da PGR...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Simula um breve momento de processamento e abre o documento na nova aba
            setTimeout(() => {
                const novaJanela = // Em vez de window.open, usas window.location.href:
                    window.location.href = `../views/auto-interrogatorio/gerar-pdf.php?id=${autoId}`;

                if (novaJanela) {
                    Swal.close(); // Fecha o loading se abriu com sucesso
                    Swal.fire({
                        title: 'Pronto!',
                        text: 'O documento foi gerado com sucesso.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    // Caso o bloqueador de pop-ups do browser impeça a abertura
                    Swal.fire({
                        title: 'Bloqueador de Pop-ups',
                        text: 'Por favor, permita pop-ups neste site para visualizar o documento.',
                        icon: 'error',
                        confirmButtonColor: '#4f46e5'
                    });
                }
            }, 1200); // 1.2 segundos de animação fluida
        }

    </script>

    <!-- MODAL: AUTO DE INTERROGATÓRIO DE ARGUIDO (PGR) -->
    <div id="modalAutoInterrogatorio"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
        <div
            class="bg-white dark:bg-zinc-900 rounded-3xl border border-gray-100 dark:border-zinc-800 w-full max-w-4xl shadow-2xl overflow-hidden transform transition-all my-8 animate-fade-in">

            <!-- CABEÇALHO OFICIAL COM CABEÇALHO DA PGR -->
            <div class="p-6 border-b border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-800/30">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 border border-amber-200/50 dark:border-amber-900/40 flex items-center justify-center text-amber-700 dark:text-amber-400 flex-shrink-0">
                            <i class="fa-solid fa-scale-balanced text-xl"></i>
                        </div>
                        <div>
                            <div
                                class="text-[10px] font-black tracking-widest text-amber-700 dark:text-amber-400 uppercase">
                                REPÚBLICA DE ANGOLA · PROCURADORIA-GERAL DA REPÚBLICA
                            </div>
                            <h3 class="text-base font-black text-gray-900 dark:text-white">
                                Auto de Interrogatório de Arguido
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium">
                                Gabinete do Procurador Junto da DNIC/SIC — Província de Luanda
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span id="badgeEstadoAuto"
                            class="text-[10px] font-black uppercase px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-400">
                            Edição de Auto
                        </span>
                        <button onclick="fecharAutoInterrogatorio()"
                            class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-gray-200 dark:hover:bg-zinc-800 text-gray-400 hover:text-gray-700 dark:hover:text-white transition">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- FORMULÁRIO DO AUTO COM IDENTIFICADORES PARA API -->
            <form id="formAutoInterrogatorio" onsubmit="salvarAutoInterrogatorio(event, false)"
                class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">

                <!-- CAMPOS OCULTOS PARA CHAVES ESTRANGEIRAS E ESTADO -->
                <input type="hidden" id="autoId" name="id" value="">
                <input type="hidden" id="autoProcessoId" name="processo_id" value="">
                <input type="hidden" id="autoArguidoId" name="arguido_id" value="">
                <input type="hidden" id="autoUtilizadorId" name="utilizador_id" value="">
                <input type="hidden" id="autoEstado" name="estado_auto" value="rascunho">

                <!-- SEÇÃO 1: DADOS DA DILIGÊNCIA & MAGISTRADO -->
                <div>
                    <h4
                        class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-gavel"></i> 1. Dados da Diligência e Magistrado
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="mb-4">
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Nº do
                                Processo <span class="text-red-500">*</span></label>
                            <select id="autoProcNum" name="numero_processo"
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                onchange="carregarDadosPorProcesso(this.value)" required>
                                <option value="">Selecione o processo...</option>
                                <?php while ($proc = $listaProcessos->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($proc['num_processo']) ?>">
                                        <?= htmlspecialchars($proc['num_processo']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Data da
                                Diligência <span class="text-red-500">*</span></label>
                            <input type="date" id="autoData" name="data_diligencia" required
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Cidade /
                                Local <span class="text-red-500">*</span></label>
                            <input type="text" id="autoCidade" name="cidade" value="Luanda"
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Magistrado
                                    do M.P. <span class="text-red-500">*</span></label>
                                <input type="text" id="autoMagistrado" name="magistrado_mp"
                                    placeholder="Ex: Exmo. Sr. Dr. Manuel Silva"
                                    class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Defensor /
                                    Advogado <span class="text-red-500">*</span></label>
                                <input type="text" id="autoDefensor" name="defensor_advogado"
                                    placeholder="Ex: Dr. António Cabral (Cédula 412)"
                                    class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Oficial
                                    / Escrivão ("Comigo") <span class="text-red-500">*</span></label>
                                <input type="text" id="autoEscrivao" name="oficial_escrivao"
                                    placeholder="Ex: Escrivão Pires"
                                    class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEÇÃO 2: IDENTIFICAÇÃO QUALIFICADA DO ARGUIDO -->
                <div class="pt-2 border-t border-gray-100 dark:border-zinc-800">
                    <h4
                        class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-user-gear"></i> 2. Qualificação e Identidade do Arguido
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Nome
                                Completo do Arguido <span class="text-red-500">*</span></label>
                            <input type="text" id="autoNomeArguido" readonly name="nome_completo" required
                                placeholder="Nome completo"
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Estado
                                Civil <span class="text-red-500">*</span></label>
                            <input type="text" id="autoEstadoCivil" name="estado_civil" readonly
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Profissão
                                <span class="text-red-500">*</span></label>
                            <input type="text" readonly id="autoProfissao" name="profissao" placeholder="-----"
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Idade
                                (Anos) <span class="text-red-500">*</span></label>
                            <input type="number" readonly id="autoIdade" name="idade" placeholder="Ex: 38"
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Data de
                                Nascimento <span class="text-red-500">*</span></label>
                            <input type="date" readonly id="autoDataNasc" name="data_nascimento"
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Naturalidade
                                <span class="text-red-500">*</span></label>
                            <input type="text" readonly id="autoNaturalidade" name="naturalidade"
                                placeholder="Ex: Maianga, Luanda"
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Nome do
                                Pai <span class="text-red-500">*</span></label>
                            <input type="text" readonly id="autoPai" name="nome_pai" placeholder="Filho de..."
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Nome da
                                Mãe <span class="text-red-500">*</span></label>
                            <input type="text" readonly id="autoMae" name="nome_mae" placeholder="E de..."
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="md:col-span-4">
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Residência
                                Habitual <span class="text-red-500">*</span></label>
                            <input type="text" readonly id="autoResidencia" name="residencia_habitual"
                                placeholder="Endereço, Bairro, Rua e Nº de Casa"
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Bilhete de
                                Identidade (B.I.) Nº <span class="text-red-500">*</span></label>
                            <input type="text" readonly id="autoBINum" name="bi_numero" placeholder="Ex: 004819201LA042"
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Arquivo de
                                Emissão <span class="text-red-500">*</span></label>
                            <input type="text" id="autoBIEmissor" name="bi_arquivo_emissao" placeholder="Ex: Luanda"
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Data de
                                Emissão B.I. <span class="text-red-500">*</span></label>
                            <input type="date" id="autoBIData" name="bi_data_emissao"
                                class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl px-3 py-2 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- SEÇÃO 3: ANTECEDENTES CRIMINAIS & ADVERTÊNCIA LEGAL -->
                <div class="pt-2 border-t border-gray-100 dark:border-zinc-800">
                    <div
                        class="bg-rose-50/60 dark:bg-rose-950/20 border border-rose-200/60 dark:border-rose-900/40 rounded-2xl p-3 mb-3 text-[11px] text-rose-800 dark:text-rose-400 font-medium">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        <strong>Advertência Legal (Identidade e Antecedentes):</strong> O arguido foi advertido de que a
                        falta de resposta às perguntas sobre a sua identidade e antecedentes criminais o fará incorrer
                        na <strong>pena de desobediência</strong> e a sua falsidade na <strong>pena de falsas
                            declarações</strong>.
                    </div>

                    <h4
                        class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left"></i> 3. Antecedentes Criminais
                    </h4>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Perguntado se
                            já esteve preso, quando e porquê, se foi ou não condenado <span
                                class="text-red-500">*</span></label>
                        <textarea id="autoAntecedentes" name="antecedentes_criminais" rows="2"
                            placeholder="Respostas referentes a antecedentes penais ou condenações prévias..."
                            class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl p-3 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"></textarea>
                    </div>
                </div>

                <!-- SEÇÃO 4: DECLARAÇÕES SOBRE OS FACTOS IMPUTADOS -->
                <div class="pt-2 border-t border-gray-100 dark:border-zinc-800">
                    <div
                        class="bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/40 rounded-2xl p-3 mb-3 text-[11px] text-amber-800 dark:text-amber-400 font-medium">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        <strong>Advertência Legal (Factos Imputados):</strong> O arguido foi esclarecido de que não é
                        obrigado a responder às perguntas que lhe vão ser feitas sobre os factos imputados.
                    </div>

                    <h4
                        class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-comment-dots"></i> 4. Declarações sobre os Factos Imputados
                    </h4>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-1">Transcrição do
                            Interrogatório e Respostas <span class="text-red-500">*</span></label>
                        <textarea id="autoDeclaracoes" name="transcricao_declaracoes" rows="6" required
                            placeholder="Interrogado seguidamente sobre os factos que lhe são imputados, respondeu que..."
                            class="w-full bg-gray-50 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700/60 rounded-xl p-3 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"></textarea>
                    </div>
                </div>

                <!-- RODAPÉ DE AÇÕES -->
                <div class="pt-4 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                    <button type="button" onclick="fecharAutoInterrogatorio()"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-gray-500 hover:text-gray-800 dark:text-zinc-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-zinc-800 transition">
                        Cancelar
                    </button>
                    <div class="flex items-center gap-2">
                        <!-- Botão de Rascunho -->
                        <button type="button" onclick="salvarAutoInterrogatorio(event, true)"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-gray-700 dark:text-zinc-300 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 transition">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Salvar Rascunho
                        </button>

                        <!-- Botão de Finalizar (mudado para type="button" para disparar a função) -->
                        <button type="button" onclick="salvarAutoInterrogatorio(event, false)"
                            class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm cursor-pointer">
                            <i class="fa-solid fa-file-signature mr-1"></i> Finalizar Auto
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        async function abrirAutoInterrogatorio(autoId = null, isNovo = false) {
            const modal = document.getElementById('modalAutoInterrogatorio');
            const form = document.getElementById('formAutoInterrogatorio');
            const badge = document.getElementById('badgeEstadoAuto');

            if (!modal || !form) return;

            form.reset();

            if (isNovo || !autoId) {
                // Modo Novo Registo
                badge.innerText = "Novo Auto (Vazio)";
                badge.className = "text-[10px] font-black uppercase px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400";
                document.getElementById('autoCidade').value = "Luanda";
                document.getElementById('autoData').value = new Date().toISOString().split('T')[0];
                document.getElementById('autoEstado').value = "rascunho";

                // Atribui o ID do utilizador logado na sessão (ajustar conforme a implementação real)
                document.getElementById('autoUtilizadorId').value = "1";

                modal.classList.remove('hidden');
            } else {
                // Modo Edição: Carrega os dados reais da Base de Dados
                badge.innerText = "Edição / Leitura de Auto";
                badge.className = "text-[10px] font-black uppercase px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-400";

                // Exibe indicador de carregamento enquanto busca na BD
                Swal.fire({
                    title: 'A carregar...',
                    text: 'A obter os dados do auto a partir da base de dados',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    // Requisição Fetch para o endpoint do backend que retorna os dados da tabela 'auto_interrogatorios'
                    const resposta = await fetch(`../controller/auto-interrogatorio/obter.php?id=${autoId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const resultado = await resposta.json();

                    if (!resposta.ok) {
                        throw new Error(resultado.mensagem || 'Erro ao obter dados do auto na base de dados.');
                    }

                    const dados = resultado.dados; // Registo retornado pela tabela SQL

                    // Preenchimento de todos os campos do formulário com os valores vindos da BD
                    document.getElementById('autoId').value = dados.id || '';
                    document.getElementById('autoProcessoId').value = dados.processo_id || '';
                    document.getElementById('autoArguidoId').value = dados.arguido_id || '';
                    document.getElementById('autoUtilizadorId').value = dados.utilizador_id || '';
                    document.getElementById('autoEstado').value = dados.estado_auto || 'rascunho';

                    document.getElementById('autoProcNum').value = dados.numero_processo || '';
                    document.getElementById('autoData').value = dados.data_diligencia || '';
                    document.getElementById('autoCidade').value = dados.cidade || 'Luanda';
                    document.getElementById('autoMagistrado').value = dados.magistrado_mp || '';
                    document.getElementById('autoDefensor').value = dados.defensor_advogado || '';
                    document.getElementById('autoEscrivao').value = dados.oficial_escrivao || '';

                    document.getElementById('autoNomeArguido').value = dados.nome_completo || '';
                    document.getElementById('autoEstadoCivil').value = dados.estado_civil || 'Solteiro(a)';
                    document.getElementById('autoProfissao').value = dados.profissao || '';
                    document.getElementById('autoIdade').value = dados.idade || '';
                    document.getElementById('autoDataNasc').value = dados.data_nascimento || '';
                    document.getElementById('autoNaturalidade').value = dados.naturalidade || '';
                    document.getElementById('autoPai').value = dados.nome_pai || '';
                    document.getElementById('autoMae').value = dados.nome_mae || '';
                    document.getElementById('autoResidencia').value = dados.residencia_habitual || '';
                    document.getElementById('autoBINum').value = dados.bi_numero || '';
                    document.getElementById('autoBIEmissor').value = dados.bi_arquivo_emissao || '';
                    document.getElementById('autoBIData').value = dados.bi_data_emissao || '';

                    document.getElementById('autoAntecedentes').value = dados.antecedentes_criminais || '';
                    document.getElementById('autoDeclaracoes').value = dados.transcricao_declaracoes || '';

                    // Fecha o alerta de loading e exibe o modal
                    Swal.close();
                    modal.classList.remove('hidden');

                } catch (erro) {
                    console.error('Erro ao carregar dados:', erro);
                    Swal.fire({
                        title: 'Erro de Carregamento',
                        text: erro.message || 'Não foi possível recuperar os dados da base de dados.',
                        icon: 'error',
                        confirmButtonColor: '#991b1b'
                    });
                }
            }
        }

        async function salvarAutoInterrogatorio(event, isRascunho = false) {
            if (event) event.preventDefault();

            const inputEstado = document.getElementById('autoEstado');
            if (inputEstado) {
                inputEstado.value = isRascunho ? 'rascunho' : 'finalizado';
            }

            const form = document.getElementById('formAutoInterrogatorio');
            const formData = new FormData(form);
            const dadosAuto = Object.fromEntries(formData.entries());

            const tituloAlerta = isRascunho ? 'Guardar Rascunho?' : 'Finalizar Auto de Interrogatório?';
            const textoAlerta = isRascunho
                ? 'O documento será guardado como rascunho para edição posterior.'
                : 'Após finalizado, o auto ficará registado oficialmente no processo.';

            const confirmacao = await Swal.fire({
                title: tituloAlerta,
                text: textoAlerta,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sim, prosseguir',
                cancelButtonText: 'Cancelar'
            });

            if (!confirmacao.isConfirmed) return;

            Swal.fire({
                title: 'A processar...',
                text: 'A gravar os dados do auto no sistema da PGR',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Define dinamicamente para qual arquivo enviar os dados
                const urlEndpoint = isRascunho
                    ? '../controller/auto-interrogatorio/rascunho.php'
                    : '../controller/auto-interrogatorio/salvar.php';

                const resposta = await fetch(urlEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(dadosAuto)
                });

                const resultado = await resposta.json();

                if (!resposta.ok) {
                    throw new Error(resultado.mensagem || 'Erro ao comunicar com o servidor.');
                }

                await Swal.fire({
                    title: 'Sucesso!',
                    text: resultado.mensagem || 'Auto de Interrogatório guardado com sucesso.',
                    icon: 'success',
                    confirmButtonColor: '#4f46e5'
                });

                // ==========================================
                // FECHA O MODAL E LIMpa OS CAMPOS AUTOMATICAMENTE
                // ==========================================
                if (typeof fecharAutoInterrogatorio === 'function') {
                    fecharAutoInterrogatorio();
                }

                if (typeof recarregarListaAutos === 'function') {
                    recarregarListaAutos();
                }

            } catch (erro) {
                console.error('Erro na gravação:', erro);
                Swal.fire({
                    title: 'Erro de Gravação',
                    text: erro.message || 'Ocorreu um erro inesperado ao tentar salvar o auto.',
                    icon: 'error',
                    confirmButtonColor: '#991b1b'
                });
            }

            carregarProcessos();
        }

        /**
         * Toggle do Menu Dropdown do Card
         */
        function toggleInstrucaoMenu(event, menuId) {
            event.stopPropagation();
            const menu = document.getElementById(menuId);

            document.querySelectorAll('[id^="menu-inst-"]').forEach(m => {
                if (m.id !== menuId) m.classList.add('hidden');
            });

            menu.classList.toggle('hidden');
        }

        // Event Listeners globais para fechar menus dropdown e modal (ESC ou clique fora)
        document.addEventListener('click', (e) => {
            document.querySelectorAll('[id^="menu-inst-"]').forEach(m => m.classList.add('hidden'));

            const modal = document.getElementById('modalAutoInterrogatorio');
            if (e.target === modal) {
                fecharAutoInterrogatorio();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                fecharAutoInterrogatorio();
            }
        });

        window.carregarDadosPorProcesso = async function (numeroProcesso) {
            if (!numeroProcesso) {
                // Limpar o formulário se nenhum processo estiver selecionado
                const form = document.getElementById('formAutoInterrogatorio');
                if (form) form.reset();

                if (document.getElementById('autoId')) document.getElementById('autoId').value = '';
                if (document.getElementById('autoProcessoId')) document.getElementById('autoProcessoId').value = '';
                if (document.getElementById('autoArguidoId')) document.getElementById('autoArguidoId').value = '';
                if (document.getElementById('autoUtilizadorId')) document.getElementById('autoUtilizadorId').value = '';
                if (document.getElementById('autoEstado')) document.getElementById('autoEstado').value = 'rascunho';
                return;
            }

            try {
                const response = await fetch(`../controller/auto-interrogatorio/get_dados_auto.php?numero=${encodeURIComponent(numeroProcesso)}`);
                const resultado = await response.json();

                if (!resultado.sucesso) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Atenção',
                        text: resultado.mensagem || 'Erro ao carregar dados do processo.',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#4f46e5'
                    });
                    return;
                }

                const d = resultado.dados;

                // 1. Preencher IDs Ocultos (GARANTE O ID PARA O UPDATE)
                if (document.getElementById('autoId')) document.getElementById('autoId').value = d.id || '';
                if (document.getElementById('autoProcessoId')) document.getElementById('autoProcessoId').value = d.processo_id || '';
                if (document.getElementById('autoArguidoId')) document.getElementById('autoArguidoId').value = d.arguido_id || '';
                if (document.getElementById('autoUtilizadorId')) document.getElementById('autoUtilizadorId').value = d.utilizador_id || '';
                if (document.getElementById('autoEstado')) document.getElementById('autoEstado').value = d.estado_auto || 'rascunho';

                // 2. Preencher Dados da Secção 1 & Identificação
                if (document.getElementById('autoNomeArguido')) document.getElementById('autoNomeArguido').value = d.nome_completo || '';
                if (document.getElementById('autoEstadoCivil')) document.getElementById('autoEstadoCivil').value = d.estado_civil || 'Solteiro(a)';
                if (document.getElementById('autoProfissao')) document.getElementById('autoProfissao').value = d.profissao || '';
                if (document.getElementById('autoIdade')) document.getElementById('autoIdade').value = d.idade || '';
                if (document.getElementById('autoDataNasc')) document.getElementById('autoDataNasc').value = d.data_nascimento || '';
                if (document.getElementById('autoNaturalidade')) document.getElementById('autoNaturalidade').value = d.naturalidade || '';
                if (document.getElementById('autoPai')) document.getElementById('autoPai').value = d.nome_pai || '';
                if (document.getElementById('autoMae')) document.getElementById('autoMae').value = d.nome_mae || '';
                if (document.getElementById('autoResidencia')) document.getElementById('autoResidencia').value = d.residencia_habitual || '';
                if (document.getElementById('autoBINum')) document.getElementById('autoBINum').value = d.bi_numero || '';
                if (document.getElementById('autoBIEmissor')) document.getElementById('autoBIEmissor').value = d.bi_arquivo_emissao || '';
                if (document.getElementById('autoBIData')) document.getElementById('autoBIData').value = d.bi_data_emissao || '';

                // 3. Preencher Antecedentes e Declarações
                if (document.getElementById('autoAntecedentes')) document.getElementById('autoAntecedentes').value = d.antecedentes_criminais || '';
                if (document.getElementById('autoDeclaracoes')) document.getElementById('autoDeclaracoes').value = d.transcricao_declaracoes || '';

                if (d.data_diligencia && document.getElementById('autoData')) document.getElementById('autoData').value = d.data_diligencia;
                if (d.cidade && document.getElementById('autoCidade')) document.getElementById('autoCidade').value = d.cidade;
                if (d.magistrado_mp && document.getElementById('autoMagistrado')) document.getElementById('autoMagistrado').value = d.magistrado_mp;
                if (d.defensor_advogado && document.getElementById('autoDefensor')) document.getElementById('autoDefensor').value = d.defensor_advogado;
                if (d.oficial_escrivao && document.getElementById('autoEscrivao')) document.getElementById('autoEscrivao').value = d.oficial_escrivao;

                // Notificação discreta (Toast) caso seja um rascunho anterior carregado
                if (resultado.origem === 'rascunho_existente') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'info',
                        title: 'Rascunho anterior carregado para edição.'
                    });
                }

            } catch (error) {
                console.error('Erro na requisição:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de Comunicação',
                    text: 'Ocorreu um erro ao comunicar com o servidor.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4f46e5'
                });
            }
        };

        function fecharAutoInterrogatorio() {
            const modal = document.getElementById('modalAutoInterrogatorio');
            const form = document.getElementById('formAutoInterrogatorio');

            if (modal) {
                modal.classList.add('hidden');
            }

            if (form) {
                form.reset();
            }

            // Limpeza manual de segurança para campos ocultos e estados
            const autoId = document.getElementById('autoId');
            if (autoId) autoId.value = '';

            const autoProcessoId = document.getElementById('autoProcessoId');
            if (autoProcessoId) autoProcessoId.value = '';

            const autoArguidoId = document.getElementById('autoArguidoId');
            if (autoArguidoId) autoArguidoId.value = '';

            const autoUtilizadorId = document.getElementById('autoUtilizadorId');
            if (autoUtilizadorId) autoUtilizadorId.value = '';

            const autoEstado = document.getElementById('autoEstado');
            if (autoEstado) autoEstado.value = 'rascunho';
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