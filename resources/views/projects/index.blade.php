<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Proyectos</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Listado</h2>
            </div>
            <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Nuevo proyecto</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="mb-4 text-green-700 bg-green-100 border border-green-200 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded border border-gray-100">
                <div class="px-4 py-3 border-b border-gray-100 flex flex-wrap items-center gap-3">
                    <div class="flex-1">
                        <p class="text-sm text-gray-500">Listado de proyectos</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $projects->total() }} registros</p>
                    </div>
                    <form method="get" class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Estado</label>
                        <select name="status" onchange="this.form.submit()" class="border-gray-300 rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" @selected((string)$statusFilter === (string)$status->id)>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600 uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 w-14"></th>
                                <th class="px-4 py-3">Proyecto</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3 text-right">Presupuesto</th>
                                <th class="px-4 py-3">Inicio</th>
                                <th class="px-4 py-3">Fin</th>
                                <th class="px-4 py-3 text-center">Equipo</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($projects as $project)
                                @php
                                    $isActive = ($project->status_id ?? null) == 1 || ($project->status_id ?? null) == 3;
                                    $statusColor = $isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700';
                                    $typeColor = 'bg-indigo-50 text-indigo-700';
                                    $desc = \Illuminate\Support\Str::limit(strip_tags($project->description ?? ''), 120);
                                    $initials = collect(explode(' ', $project->name))
                                        ->filter()
                                        ->map(fn ($w) => mb_substr($w, 0, 1))
                                        ->take(2)
                                        ->implode('');
                                    $bgColor = $project->color ?? 'rgb(229, 231, 235)';
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="h-9 w-9 rounded-full flex items-center justify-center text-xs font-semibold text-white" style="background: {{ $bgColor }}">
                                            {{ $initials ?: '?' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        <a href="{{ route('projects.show', $project->id) }}" class="hover:underline">{{ $project->name }}</a>
                                        <div class="text-xs text-gray-500 leading-relaxed">
                                            {{ $desc }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $typeColor }}">
                                            {{ $project->type_name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                            {{ $project->status_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-700 font-semibold">
                                        {{ $project->budget ? '$'.number_format($project->budget, 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $project->start_date ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $project->finish_date ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center text-gray-700">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-xs font-medium">
                                            {{ $project->users_count }} miembros
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('projects.edit', $project->id) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">Editar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between text-sm text-gray-600">
                    <div>Mostrando {{ $projects->firstItem() }}-{{ $projects->lastItem() }} de {{ $projects->total() }}</div>
                    <div>
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
