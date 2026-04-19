<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">
                    <a href="{{ route('projects.index') }}" class="hover:text-indigo-600">Proyectos</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">{{ $project->name }}</a>
                    <span class="mx-1">/</span>
                    Briefs
                </p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Briefs de {{ $project->name }}</h2>
            </div>
            <a href="{{ route('projects.briefs.create', $project) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Nuevo brief</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="text-green-700 bg-green-100 border border-green-200 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm text-gray-500">Historial de briefs</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $briefs->total() }} registros</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600 uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3">Brief</th>
                                <th class="px-4 py-3">Respuestas</th>
                                <th class="px-4 py-3">Creado por</th>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($briefs as $brief)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        <a href="{{ route('projects.briefs.show', [$project, $brief]) }}" class="hover:underline">{{ $brief->title }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $brief->answers_count }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $brief->creator?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $brief->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('projects.briefs.edit', [$project, $brief]) }}" class="text-indigo-600 hover:text-indigo-500 font-medium">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin briefs registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                    {{ $briefs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
