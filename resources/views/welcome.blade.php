<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Ari Studio') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                :root {
                    font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                }
                * { box-sizing: border-box; }
                body { margin: 0; }
                .gradient-bg { background: radial-gradient(120% 120% at 10% 20%, #1f5bff22, transparent), radial-gradient(120% 120% at 90% 0%, #ff6cdd22, transparent), linear-gradient(135deg, #0B1021, #0F1834 40%, #0A0F24); }
            </style>
        @endif
    </head>
    <body class="min-h-screen gradient-bg text-black flex items-center justify-center px-6 py-12">
        <div class="max-w-5xl w-full grid md:grid-cols-2 gap-10 items-center">
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center backdrop-blur">
                        <img src="{{ asset('logo_ari.png') }}" alt="Ari" class="h-9 w-9 object-contain">
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-black/70">Bienvenido a Ari Studio</p>
                        <p class="text-4xl leading-tight font-semibold text-black tracking-tight">Organiza, crea y colabora</p>
                    </div>
                </div>
                <div class="bg-white/10 border border-white/30 rounded-2xl p-5 max-w-xl">
                    <p class="text-xl font-semibold text-black leading-tight">Aquí es donde las ideas se convierten en impacto.</p>
                    <p class="text-sm text-black/70 mt-2">Arranca tu jornada con un panel limpio y enfocado.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-3 bg-white text-[#0B1021] rounded-xl font-semibold shadow-lg shadow-black/10 hover:bg-white/90 transition">Ir al dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-3 bg-white text-[#0B1021] rounded-xl font-semibold shadow-lg shadow-black/10 hover:bg-white/90 transition">Iniciar sesión</a>
                    @endauth
                </div>
                <div class="flex items-center gap-4 text-sm text-white">
                    <div class="h-px w-12 bg-white/20"></div>
                    <span>Empieza con lo importante, sin ruido.</span>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -inset-6 bg-white/5 blur-3xl rounded-3xl"></div>
                <div class="relative bg-white/5 border border-white/10 rounded-3xl p-6 shadow-2xl backdrop-blur">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="h-11 w-11 rounded-2xl bg-white flex items-center justify-center shadow-md shadow-black/10">
                                <img src="{{ asset('logo_ari.png') }}" alt="Ari" class="h-8 w-8 object-contain">
                            </div>
                            <div>
                                <p class="text-sm text-white/70">Acceso rápido</p>
                                <p class="font-semibold">Ingresa a tu cuenta</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                            <span class="h-2 w-2 rounded-full bg-rose-400"></span>
                        </div>
                    </div>
                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="space-y-1 text-sm">
                            <label class="text-white/70" for="email">Correo electrónico</label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}" class="w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-black placeholder:text-white/50 focus:outline-none focus:border-white focus:bg-white/10">
                        </div>
                        <div class="space-y-1 text-sm">
                            <label class="text-white/70" for="password">Contraseña</label>
                            <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-black placeholder:text-white/50 focus:outline-none focus:border-white focus:bg-white/10">
                        </div>
                        <div class="flex items-center justify-between text-xs text-white/70">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/30 bg-white/5 accent-white">
                                Recordarme
                            </label>
                            <a href="{{ route('password.request') }}" class="hover:underline">¿Olvidaste tu contraseña?</a>
                        </div>
                        <button type="submit" class="w-full px-4 py-3 bg-white text-[#0B1021] font-semibold rounded-2xl shadow-lg shadow-black/20 hover:bg-white/90 transition">Entrar al panel</button>
                    </form>
                    <div class="grid gap-4 mt-6">
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-sm text-black/60 mb-1">Proyectos</p>
                            <p class="font-semibold text-lg">Mantén tus cuentas y accesos organizados.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-sm text-black/60 mb-1">Timer</p>
                            <p class="font-semibold text-lg">Continúa donde quedaste, en web o móvil.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-sm text-black/60 mb-1">Tareas</p>
                            <p class="font-semibold text-lg">Prioriza, asigna y entrega con claridad.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
