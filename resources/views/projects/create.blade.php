<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Proyectos</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear proyecto</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
                @csrf
                @include('projects.form', [
                    'project' => $project,
                    'statuses' => $statuses,
                    'types' => $types,
                    'users' => $users,
                    'selectedUsers' => [],
                    'submit' => 'Crear'
                ])
            </form>
        </div>
    </div>
</x-app-layout>
