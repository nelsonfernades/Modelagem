<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextGrade • Login</title>

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
    <script src="./integration/sweatalert2@11.js"></script>
    <script src="./integration/tailwind.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }

        /* Animação de entrada suave */
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Efeito de Loading no Botão */
        .loader {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #6366f1;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
            display: none;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body
    class="min-h-screen flex items-center justify-center p-6 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-indigo-50 via-white to-slate-50">

    <div class="max-w-md w-full fade-in-up mx-auto">

        <!-- Cabeçalho da Marca -->
        <div class="flex flex-col items-center mb-8">
            <div
                class="w-16 h-16 bg-white rounded-[24px] shadow-xl shadow-indigo-100 flex items-center justify-center text-indigo-600 mb-4 border border-indigo-50">
                <i class="fa-solid fa-graduation-cap text-green-500 text-3xl"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Kidi<span
                    class="text-green-500">Software</span></h1>
            <p class="text-slate-400 text-sm font-medium">Gestão Inteligente de Arguidos</p>
        </div>

        <!-- Cartão Principal com Glassmorphism -->
        <div
            class="bg-white/90 backdrop-blur-2xl rounded-[40px] p-8 sm:p-10 border border-white shadow-2xl shadow-indigo-100/50 relative overflow-hidden">

            <div
                class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent opacity-20">
            </div>

            <!-- Seletor de Métodos de Login (Abas) -->
            <div class="flex bg-slate-100/80 p-1.5 rounded-[20px] mb-8 relative">
                <button type="button" onclick="switchLoginTab('credentials')" id="tabCredBtn"
                    class="flex-1 py-2.5 text-xs font-bold rounded-[16px] text-slate-700 bg-white shadow-sm transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-lock text-indigo-500"></i> Manual
                </button>
                <button type="button" onclick="switchLoginTab('qr')" id="tabQrBtn"
                    class="flex-1 py-2.5 text-xs font-bold rounded-[16px] text-slate-400 hover:text-slate-700 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-qrcode"></i> QR Code
                </button>
                <button type="button" onclick="switchLoginTab('bio')" id="tabBioBtn"
                    class="flex-1 py-2.5 text-xs font-bold rounded-[16px] text-slate-400 hover:text-slate-700 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-fingerprint"></i> Biometria
                </button>
            </div>

            <!-- AB 1: LOGIN MANUAL (Credenciais) -->
            <form id="formCredentials" class="space-y-5 login-panel" onsubmit="handleLogin(event)">
                <div class="group">
                    <label
                        class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2">Acesso
                        do Utilizador</label>
                    <div class="relative transition-all duration-300">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i
                                class="fa-solid fa-at text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                        <input type="text" name="usuario" required
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-100/60 border-2 border-transparent rounded-[20px] focus:bg-white focus:border-indigo-500/20 focus:ring-4 focus:ring-indigo-500/5 outline-none text-sm font-semibold text-slate-700 transition-all"
                            placeholder="nome.sobrenome ou nip">
                    </div>
                </div>

                <div class="group">
                    <div class="flex justify-between mb-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Código de
                            Acesso</label>
                        <a href="#"
                            class="text-[11px] font-bold text-indigo-500 hover:text-indigo-700 uppercase tracking-tighter transition">Recuperar?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i
                                class="fa-solid fa-shield-halved text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                        <input type="password" name="senha" required
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-100/60 border-2 border-transparent rounded-[20px] focus:bg-white focus:border-indigo-500/20 focus:ring-4 focus:ring-indigo-500/5 outline-none text-sm font-semibold text-slate-700 transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" id="btnSubmitCred"
                    class="w-full group bg-slate-900 text-white py-4 rounded-[20px] font-bold text-sm shadow-xl shadow-slate-200 hover:bg-indigo-600 transition-all duration-300 flex items-center justify-center gap-3 relative overflow-hidden active:scale-95 mt-2">
                    <span>Entrar no Painel</span>
                    <i
                        class="fa-solid fa-arrow-right-long text-slate-400 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
                </button>
            </form>

            <!-- AB 2: LOGIN POR QR CODE DO PASSE -->
            <div id="formQrCode" class="space-y-6 login-panel hidden text-center py-2">
                <div class="w-24 h-24 bg-indigo-50 rounded-[28px] mx-auto flex items-center justify-center text-indigo-600 border border-indigo-100 relative group cursor-pointer shadow-inner"
                    onclick="iniciarLeitorQr()">
                    <i class="fa-solid fa-qrcode text-4xl animate-pulse text-indigo-500"></i>
                    <div
                        class="absolute inset-0 bg-indigo-600/10 rounded-[28px] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="fa-solid fa-camera text-white text-xl"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-700">Aproxime o QR Code do Passe</h3>
                    <p class="text-xs text-slate-400 mt-1">Posicione o cartão frente à câmara do dispositivo ou clique
                        no ícone para digitalizar.</p>
                </div>
                <button type="button" onclick="iniciarLeitorQr()"
                    class="w-full bg-indigo-600 text-white py-3.5 rounded-[20px] font-bold text-sm shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-camera"></i> Ativar Câmara / Leitor
                </button>
            </div>

            <!-- AB 3: LOGIN POR BIOMETRIA -->
            <div id="formBiometria" class="space-y-6 login-panel hidden text-center py-4">
                <div class="w-24 h-24 bg-emerald-50 rounded-[28px] mx-auto flex items-center justify-center text-emerald-500 border border-emerald-100 cursor-pointer shadow-inner hover:scale-105 transition-transform"
                    onclick="executarBiometria()">
                    <i class="fa-solid fa-fingerprint text-5xl"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-700">Autenticação Biométrica</h3>
                    <p class="text-xs text-slate-400 mt-1">Toque no sensor do seu dispositivo ou clique no botão abaixo
                        para verificar a identidade.</p>
                </div>
                <button type="button" onclick="executarBiometria()"
                    class="w-full bg-emerald-600 text-white py-3.5 rounded-[20px] font-bold text-sm shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-lock"></i> Autenticar com Biometria
                </button>
            </div>

            <!-- Rodapé do Card -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Sistema Protegido & Cifrado</p>
            </div>
        </div>

        <p class="text-center mt-6 text-slate-400 text-xs font-medium">
            © 2026 KidiSoftware. Todos os direitos reservados.
        </p>
    </div>

    <!-- Scripts de Interação das Abas e Novas Funcionalidades -->
    <script>
        function switchLoginTab(mode) {
            // Esconde todos os painéis
            document.querySelectorAll('.login-panel').forEach(panel => panel.classList.add('hidden'));

            // Reseta o estilo das abas
            const tabs = ['tabCredBtn', 'tabQrBtn', 'tabBioBtn'];
            tabs.forEach(id => {
                const btn = document.getElementById(id);
                btn.className = "flex-1 py-2.5 text-xs font-bold rounded-[16px] text-slate-400 hover:text-slate-700 transition-all duration-300 flex items-center justify-center gap-2";
            });

            // Ativa o painel e aba selecionados
            if (mode === 'credentials') {
                document.getElementById('formCredentials').classList.remove('hidden');
                estilizarAbaAtiva('tabCredBtn', 'text-indigo-500');
            } else if (mode === 'qr') {
                document.getElementById('formQrCode').classList.remove('hidden');
                estilizarAbaAtiva('tabQrBtn', 'text-indigo-500');
            } else if (mode === 'bio') {
                document.getElementById('formBiometria').classList.remove('hidden');
                estilizarAbaAtiva('tabBioBtn', 'text-emerald-500');
            }
        }

        function estilizarAbaAtiva(elementId, iconColorClass) {
            const btn = document.getElementById(elementId);
            btn.className = "flex-1 py-2.5 text-xs font-bold rounded-[16px] text-slate-700 bg-white shadow-sm transition-all duration-300 flex items-center justify-center gap-2";
            const icon = btn.querySelector('i');
            if (icon) icon.className = `fa-solid ${icon.className.split(' ')[1]} ${iconColorClass}`;
        }

        function iniciarLeitorQr() {
            // Aqui podes integrar a tua biblioteca de leitura de QR (ex: html5-qrcode)
            alert("Módulo de leitura por QR Code do passe acionado. A apontar para a câmara...");
        }

        function executarBiometria() {
            // Exemplo de integração nativa do navegador (WebAuthn / Sensor biométrico)
            if (window.PublicKeyCredential) {
                alert("A solicitar verificação biométrica do dispositivo...");
            } else {
                alert("A biometria não é suportada diretamente por este navegador.");
            }
        }
    </script>

    <script>
        async function handleLogin(event) {
            event.preventDefault();

            // Captura os valores dos inputs do formulário manual
            const form = document.getElementById('formCredentials');
            const usuarioInput = form.querySelector('input[name="usuario"]').value;
            const senhaInput = form.querySelector('input[name="senha"]').value;

            const btnSubmit = document.getElementById('btnSubmitCred');
            const textoOriginal = btnSubmit.innerHTML;

            // Altera o botão para estado de carregamento
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> A autenticar...`;

            try {
                const response = await fetch('api/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        usuario: usuarioInput,
                        senha: senhaInput
                    })
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#ffffff',
                        color: '#1e293b'
                    }).then(() => {
                        window.location.href = result.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Acesso Negado',
                        text: result.message,
                        confirmButtonColor: '#4f46e5'
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
                    confirmButtonColor: '#4f46e5'
                });
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = textoOriginal;
            }
        }
    </script>
</body>

</html>