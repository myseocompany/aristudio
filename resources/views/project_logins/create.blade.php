<x-app-layout>
    <x-slot name="header">
        <div>
            <nav class="text-xs text-gray-500 mb-1 space-x-1">
                <a href="{{ route('projects.index') }}" class="hover:text-indigo-600">Proyectos</a>
                <span>/</span>
                <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">{{ $project->name }}</a>
                <span>/</span>
                <span class="text-gray-700">Nuevo login</span>
            </nav>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo acceso</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('project_logins.form', ['project' => $project, 'login' => $login])
        </div>
    </div>
</x-app-layout>
