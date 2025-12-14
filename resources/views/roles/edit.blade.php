<x-app-layout>
    <x-slot name="header">
        <div>
            <nav class="text-xs text-gray-500 mb-1 space-x-1">
                <a href="{{ route('roles.index') }}" class="hover:text-indigo-600">Roles</a>
                <span>/</span>
                <span class="text-gray-700">Editar</span>
            </nav>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar rol</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('roles.update', $role) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                @include('roles.form', ['role' => $role, 'submit' => 'Guardar'])
            </form>
        </div>
    </div>
</x-app-layout>
