<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Proyectos</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar proyecto</h2>
            </div>
            <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50 text-sm">Ver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                @include('projects.form', [
                    'project' => $project,
                    'statuses' => $statuses,
                    'types' => $types,
                    'users' => $users,
                    'selectedUsers' => $selectedUsers,
                    'submit' => 'Guardar cambios'
                ])
            </form>
        </div>
    </div>
</x-app-layout>
