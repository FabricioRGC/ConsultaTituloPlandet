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
            background: linear-gradient(135deg, #070e1b 0%, #0d1b32 50%, #070e1b 100%);
        }
    </style>
</head>
<body class="m-0 p-0 min-h-screen flex items-center justify-center p-4 sm:p-6 overflow-hidden relative">

    <!-- Tarjeta Principal -->
    <div class="w-full max-w-5xl bg-[#091224] rounded-3xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.7)] border border-blue-900/30 overflow-hidden flex flex-col lg:flex-row relative z-10">
        
        <!-- PANEL IZQUIERDO -->
        <div class="lg:w-1/2 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-900 p-8 lg:p-12 text-white flex flex-col justify-between relative overflow-hidden border-b lg:border-b-0 lg:border-r border-blue-800/40">
            
            <div class="relative z-10">
                <!-- Logo Oficial -->
                <div class="mb-6 flex items-center">
                    <img src="/ConsultaTituloPlandet/logo/logoplandet-white.png" alt="Plandet Logo" class="h-8 w-auto object-contain">
                </div>
                
                <!-- Título y Subtítulo exactos -->
                <h2 class="text-5sxl lg:text-4xl font-extrabold leading-[1.2] text-white tracking-tight mb-3">
                    Sistema de Gestión de<br><span class="text-blue-200">Partidas Registrales</span>
                </h2>
                <p class="text-blue-100/90 text-xs lg:text-sm leading-relaxed font-normal mb-6">
                    Plataforma integral y segura para la administración, control y validación digital de documentos.
                </p>

                <!-- Bloques funcionales compactos para equilibrar el espacio -->
                <div class="space-y-2.5">
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/15">
                        <div class="bg-white/15 p-2 rounded-xl shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <path d="m9 15 2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white text-xs font-bold">Gestión Documental & QRs</h4>
                            <p class="text-blue-100/80 text-[10px]">Validación y estampado digital de archivos.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/15">
                        <div class="bg-white/15 p-2 rounded-xl shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white text-xs font-bold">Seguridad y Auditoría</h4>
                            <p class="text-blue-100/80 text-[10px]">Historial de acciones y control por usuario.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer con la misma distancia -->
            <div class="relative z-10 mt-6 pt-4 border-t border-white/15">
                <p class="text-blue-200/80 text-xs font-medium tracking-wide text-center">
                    © <?= date('Y') ?> Plandet 
                </p>
            </div>
        </div>

        <!-- PANEL DERECHO (Formulario con distribución compacta y sin vacíos) -->
        <div class="lg:w-1/2 px-8 py-8 lg:px-12 lg:py-10 bg-[#091224] border border-blue-500/20 shadow-[0_0_30px_rgba(37,99,235,0.12)] lg:rounded-r-3xl flex flex-col justify-center relative">
            
            <div class="w-full max-w-md mx-auto">
                <div class="mb-5">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-white tracking-tight mb-1">Bienvenido</h2>
                    <p class="text-slate-400 text-xs lg:text-sm font-medium">Ingresa tus credenciales corporativas para continuar</p>
                </div>

                <?php if ($error): ?>
                    <div class="mb-4 rounded-xl border border-red-500/30 bg-red-950/50 text-red-300 px-3.5 py-2.5 text-xs flex items-center gap-2.5">
                        <span class="text-base">⚠️</span>
                        <span class="font-semibold">Credenciales inválidas. Verifica tu usuario y contraseña.</span>
                    </div>
                <?php endif; ?>

                <form action="/ConsultaTituloPlandet/index.php?action=login" method="POST" class="space-y-4" autocomplete="off">
                    
                    <!-- Input Usuario / Correo -->
                    <div>
                        <label for="name" class="block mb-1.5 text-xs font-bold text-slate-400 uppercase tracking-wider">
                            Correo o Usuario
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all duration-200 text-sm shadow-inner"
                            placeholder="usuario@plandet.local"
                            required
                        />
                    </div>

                    <!-- Input Contraseña -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
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
                                class="w-full px-4 py-3 pr-12 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all duration-200 text-sm shadow-inner"
                                placeholder="••••••••"
                                required
                            />
                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors p-1"
                                aria-label="Mostrar u ocultar contraseña"
                            >
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Botón Principal -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white py-3 rounded-xl transition-all duration-300 font-bold shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 hover:-translate-y-0.5 active:translate-y-0 text-sm tracking-wide mt-2"
                    >
                        Iniciar Sesión
                    </button>
                </form>

                <div class="mt-5 pt-4 border-t border-slate-800/80">
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