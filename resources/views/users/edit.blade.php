<x-app-layout>
    <x-slot name="header">
        <div>
            <nav class="text-xs text-gray-500 mb-1 space-x-1">
                <a href="{{ route('users.index') }}" class="hover:text-indigo-600">Usuarios</a>
                <span>/</span>
                <span class="text-gray-700">Editar</span>
            </nav>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar usuario
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white shadow rounded p-6">
                @csrf
                @method('PUT')

                @include('users.partials.form', [
                    'user' => $user,
                    'roles' => $roles,
                    'statuses' => $statuses,
                    'projects' => $projects,
                    'selectedProjects' => $selectedProjects,
                    'submit' => 'Actualizar'
                ])
            </form>
        </div>
    </div>
</x-app-layout>
