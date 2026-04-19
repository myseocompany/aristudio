<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">
                    <a href="{{ route('projects.index') }}" class="hover:text-indigo-600">Proyectos</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">{{ $project->name }}</a>
                    <span class="mx-1">/</span>
                    Brief
                </p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $brief->title }}</h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.briefs.edit', [$project, $brief]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Editar</a>
                <form action="{{ route('projects.briefs.destroy', [$project, $brief]) }}" method="POST" onsubmit="return confirm('¿Eliminar este brief?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 border border-red-200 text-red-600 rounded hover:bg-red-50 text-sm">Eliminar</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="text-green-700 bg-green-100 border border-green-200 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Proyecto</p>
                        <p class="font-medium text-gray-900">{{ $project->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Creado por</p>
                        <p class="font-medium text-gray-900">{{ $brief->creator?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Fecha</p>
                        <p class="font-medium text-gray-900">{{ $brief->created_at?->format('Y-m-d H:i') ?? '—' }}</p>
                    </div>
                </div>
                @if($brief->notes)
                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-sm text-gray-500">Notas</p>
                        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $brief->notes }}</p>
                    </div>
                @endif
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-sm text-gray-500 mb-2">Enlace para el cliente</p>
                    @include('project_briefs.partials.public_link', ['brief' => $brief])
                </div>
            </div>

            @forelse($answersBySection as $section => $answers)
                <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-4">
                    <h3 class="font-semibold text-gray-900">{{ $section }}</h3>
                    <div class="space-y-4">
                        @foreach($answers as $answer)
                            <div class="border-t border-gray-100 pt-4 first:border-t-0 first:pt-0">
                                <p class="text-sm font-medium text-gray-800 whitespace-pre-line">{{ $answer->question?->value ?? 'Pregunta eliminada' }}</p>
                                <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">{{ $answer->value === 'on' ? 'Seleccionado' : $answer->value }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6">
                    <p class="text-sm text-gray-500">Este brief todavía no tiene respuestas.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
