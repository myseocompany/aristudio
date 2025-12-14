<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <nav class="text-xs text-gray-500 space-x-1">
                <a href="{{ route('modules.index') }}" class="hover:text-indigo-600">Módulos</a>
                <span>/</span>
                <span class="text-gray-700">{{ $module->name }}</span>
            </nav>
            <div class="space-x-2">
                <a href="{{ route('modules.edit', $module) }}" class="px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Editar</a>
                <a href="{{ route('modules.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Nombre</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $module->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Slug</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $module->slug }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Peso</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $module->weight }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
