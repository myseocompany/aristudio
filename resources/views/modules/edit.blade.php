<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <nav class="text-xs text-gray-500 space-x-1">
                <a href="{{ route('modules.index') }}" class="hover:text-indigo-600">Módulos</a>
                <span>/</span>
                <span class="text-gray-700">{{ $module->name }}</span>
            </nav>
            <span class="text-sm text-gray-400">Editar módulo</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <form method="POST" action="{{ route('modules.update', $module) }}">
                    @method('PUT')
                    @include('modules.form', ['submitLabel' => 'Actualizar'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
