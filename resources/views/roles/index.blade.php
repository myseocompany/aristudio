<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Roles</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Listado</h2>
            </div>
            <a href="{{ route('roles.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Crear</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="mb-4 text-green-700 bg-green-100 border border-green-200 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded border border-gray-100">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm text-gray-500">Roles disponibles</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $roles->count() }} registros</p>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($roles as $role)
                        <div class="px-4 py-3 flex items-center justify-between">
                            <div class="space-y-1">
                                <p class="font-medium text-gray-900">{{ $role->name }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $role->id }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('roles.show', $role) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-semibold">Permisos</a>
                                <a href="{{ route('roles.edit', $role) }}" class="text-gray-700 hover:text-gray-900 text-sm">Editar</a>
                            </div>
                        </div>
                    @empty
                        <p class="px-4 py-3 text-sm text-gray-500">Sin roles.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
