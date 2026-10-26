<?php
$error = isset($_GET['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plandet - Acceso al Sistema</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="/ConsultaTituloPlandet/src/icon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            /* Fondo general cambiado al tono marino oscuro solicitado */
            background-color: #070e1b;
        }
    </style>
</head>
<body class="m-0 p-0 min-h-screen flex items-center justify-center p-4 sm:p-6 overflow-hidden relative">

    <!-- Tarjeta Principal -->
    <div class="w-full max-w-5xl bg-[#091224] rounded-3xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.7)] border border-blue-900/30 overflow-hidden flex flex-col lg:flex-row relative z-10">
        
        <!-- PANEL IZQUIERDO -->
        <div class="lg:w-1/2 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-900 p-10 lg:p-14 text-white flex flex-col justify-between relative overflow-hidden border-b lg:border-b-0 lg:border-r border-blue-800/40">
            <div class="relative z-10">
                <!-- Logo -->
                <div class="mb-8 flex items-center">
                    <img src="/ConsultaTituloPlandet/src/logoplandet - white.png" alt="Plandet Logo" class="h-12 w-auto object-contain">
                </div>
                
                <h2 class="text-3xl lg:text-4xl font-extrabold leading-[1.2] text-white tracking-tight mb-4">
                    Sistema de Gestión de<br><span class="text-blue-200">Partidas Registrales</span>
                </h2>
                <p class="text-blue-100/90 text-sm lg:text-base leading-relaxed font-normal">
                    Plataforma integral y segura para la administración, control y validación digital de documentos.
                </p>
            </div>

            <div class="relative z-10 mt-10 pt-6 border-t border-white/15">
                <p class="text-blue-200/80 text-xs font-medium tracking-wide">
                    © <?= date('Y') ?> Plandet
                </p>
            </div>
        </div>

        <!-- PANEL DERECHO (Formulario) -->
        <div class="lg:w-1/2 p-10 lg:p-14 bg-[#091224] flex flex-col justify-center">
            
            <div class="w-full max-w-md mx-auto">
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-white tracking-tight mb-2">Bienvenido de nuevo</h2>
                    <p class="text-slate-400 text-sm font-medium">Ingresa tus credenciales corporativas para continuar</p>
                </div>

                <?php if ($error): ?>
                    <div class="mb-6 rounded-2xl border border-red-500/30 bg-red-950/50 text-red-300 px-4 py-3 text-sm flex items-center gap-3">
                        <span class="text-lg">⚠️</span>
                        <span class="font-semibold">Credenciales inválidas. Verifica tu usuario y contraseña.</span>
                    </div>
                <?php endif; ?>

                <form action="/ConsultaTituloPlandet/index.php?action=login" method="POST" class="space-y-6" autocomplete="off">
                    
                    <!-- Input Usuario / Correo -->
                    <div>
                        <label for="name" class="block mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                            Correo o Usuario
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="w-full px-4.5 py-3.5 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all duration-200 text-sm shadow-inner"
                            placeholder="usuario@plandet.local"
                            required
                        />
                    </div>

                    <!-- Input Contraseña -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                                Contraseña
                            </label>
                            <a href="#" class="text-xs font-semibold text-blue-400 hover:text-blue-300 transition-colors">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-4.5 py-3.5 pr-12 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all duration-200 text-sm shadow-inner"
                                placeholder="••••••••"
                                required
                            />
                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors p-1"
                                aria-label="Mostrar u ocultar contraseña"
                            >
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Recordar Sesión -->
                    <div class="flex items-center pt-1">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="w-4 h-4 rounded border-slate-800 bg-slate-900 text-blue-600 focus:ring-2 focus:ring-blue-500 cursor-pointer"
                        />
                        <label for="remember" class="ml-2.5 text-sm text-slate-400 font-medium cursor-pointer">
                            Recordar mi sesión
                        </label>
                    </div>

                    <!-- Botón Principal -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white py-3.5 rounded-xl transition-all duration-300 font-bold shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 hover:-translate-y-0.5 active:translate-y-0 text-sm tracking-wide"
                    >
                        Iniciar Sesión
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-800/80">
                    <p class="text-center text-xs text-slate-500 font-medium">
                        ¿Necesitas asistencia técnica? <a href="#" class="text-blue-400 font-semibold hover:underline">Contacta al soporte</a>
                    </p>
                </div>
            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }
    </script>
</body>
</html>