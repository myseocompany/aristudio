<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500">Configuración</p>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel principal</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 grid gap-6 md:grid-cols-3">
                <a href="{{ route('users.index') }}" class="block p-5 rounded-2xl border border-gray-200 hover:border-brand-200 hover:shadow transition">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Gestión</p>
                    <p class="text-lg font-semibold text-gray-900">Usuarios</p>
                    <p class="text-sm text-gray-500 mt-1">Administra todo el equipo.</p>
                </a>
                <a href="{{ route('roles.index') }}" class="block p-5 rounded-2xl border border-gray-200 hover:border-brand-200 hover:shadow transition">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Permisos</p>
                    <p class="text-lg font-semibold text-gray-900">Roles</p>
                    <p class="text-sm text-gray-500 mt-1">Define accesos y alcances.</p>
                </a>
                <a href="{{ route('modules.index') }}" class="block p-5 rounded-2xl border border-gray-200 hover:border-brand-200 hover:shadow transition">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Navegación</p>
                    <p class="text-lg font-semibold text-gray-900">Módulos</p>
                    <p class="text-sm text-gray-500 mt-1">Controla los accesos del menú.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
