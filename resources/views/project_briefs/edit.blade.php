<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">
                    <a href="{{ route('projects.index') }}" class="hover:text-indigo-600">Proyectos</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">{{ $project->name }}</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('projects.briefs.show', [$project, $brief]) }}" class="hover:text-indigo-600">{{ $brief->title }}</a>
                    <span class="mx-1">/</span>
                    Editar
                </p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar brief</h2>
            </div>
            <a href="{{ route('projects.briefs.show', [$project, $brief]) }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50 text-sm">Ver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('projects.briefs.update', [$project, $brief]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                @include('project_briefs.partials.form', [
                    'project' => $project,
                    'brief' => $brief,
                    'questions' => $questions,
                    'answers' => $answers,
                    'submit' => 'Guardar cambios'
                ])
            </form>
        </div>
    </div>
</x-app-layout>
