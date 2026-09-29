<?php
$error = isset($_GET['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plandet - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="/ConsultaTituloPlandet-1/src/icon.svg">
    <style>
        .gradient-bg {
            background: linear-gradient(to bottom right, rgb(37, 99, 235), rgb(29, 78, 216));
        }
    </style>
</head>
<body class="m-0 p-0 h-screen overflow-hidden bg-white">
    <div class="min-h-screen flex">
        <div class="hidden lg:flex lg:w-1/2 gradient-bg p-12 flex-col justify-between relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <line x1="10" y1="9" x2="8" y2="9"/>
                        </svg>
                    </div>
                    <h1 class="text-white text-3xl font-medium">Plandet</h1>
                </div>
                <h2 class="text-white/90 text-4xl leading-tight font-medium">
                    Sistema de Gestion de<br>Partidas Registrales
                </h2>
            </div>

            <div class="relative z-10">
                <p class="text-white/80 text-base">
                    Plataforma integral para la administracion y control de partidas registrales
                </p>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <div class="mb-8">
                    <div class="lg:hidden flex items-center gap-3 mb-6">
                        <div class="bg-blue-600 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <line x1="10" y1="9" x2="8" y2="9"/>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-medium">Plandet</h1>
                    </div>
                    <h2 class="text-3xl mb-2 font-medium">Bienvenido</h2>
                    <p class="text-gray-500">Ingresa tus credenciales para acceder al sistema</p>
                </div>

                <?php if ($error): ?>
                    <div class="mb-4 rounded-lg border border-red-300 bg-red-50 text-red-700 px-4 py-3">
                        Credenciales invalidas. Verifica usuario/correo y contrasena.
                    </div>
                <?php endif; ?>

                <form action="/ConsultaTituloPlandet-1/index.php?action=login" method="POST" class="space-y-5" autocomplete="off">
                    <div>
                        <label for="name" class="block mb-2 text-gray-900 font-medium">
                            Correo o usuario
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"
                            placeholder="usuario@plandet.local"
                            required
                        />
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="text-gray-900 font-medium">
                                Contrasena
                            </label>
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-700 transition-colors">
                                Olvide mi contrasena
                            </a>
                        </div>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-4 py-3 pr-12 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"
                                placeholder="********"
                                required
                            />
                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-900 transition-colors"
                                aria-label="Mostrar u ocultar contrasena"
                            >
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-600"
                        />
                        <label for="remember" class="ml-2 text-gray-900 font-medium">
                            Recordar mi sesion
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg transition-colors duration-200 font-medium"
                    >
                        Iniciar sesion
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-center text-gray-500">
                        Necesitas ayuda? <a href="#" class="text-blue-600 hover:text-blue-700 transition-colors">Contacta al soporte</a>
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
