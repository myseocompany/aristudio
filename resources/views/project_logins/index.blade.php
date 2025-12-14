<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Accesos</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Logins de proyectos</h2>
                <p class="text-xs text-gray-500">
                    @if($canSeeAll)
                        Puedes ver todos los proyectos (view scope: todos).
                    @else
                        Solo verás los logins de proyectos asignados a ti.
                    @endif
                </p>
            </div>
            <a href="{{ route('projects.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Ir a proyectos</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <form method="GET" action="{{ route('logins.index') }}" class="bg-white border border-gray-100 rounded-lg shadow-sm p-4 sm:p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Buscar</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nombre, usuario, URL..." class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Proyecto</label>
                        <select name="project_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" @selected(($filters['project_id'] ?? '') == $project->id)>{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Filtrar</button>
                        <a href="{{ route('logins.index') }}" class="px-4 py-2 border rounded text-sm text-gray-700 hover:bg-gray-50">Limpiar</a>
                    </div>
                </div>
            </form>

            <div class="bg-white border border-gray-100 rounded-lg shadow-sm">
                <div class="border-b border-gray-100 px-4 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Resultados</p>
                        <p class="font-semibold text-gray-900">{{ $logins->total() }} logins</p>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($logins as $login)
                        <div class="px-4 sm:px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 rounded-lg flex items-center justify-center text-sm font-semibold text-white" style="background: {{ $login->project->color ?? '#6366f1' }}">
                                    {{ collect(explode(' ', trim($login->project->name ?? '?')))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('') ?: '?' }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $login->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $login->project->name ?? 'Sin proyecto' }}</p>
                                    <div class="text-xs text-gray-600 mt-1 space-y-0.5">
                                        <p><span class="font-semibold">Usuario:</span> {{ $login->user }}</p>
                                        <p><span class="font-semibold">Contraseña:</span> {{ $login->password }}</p>
                                        @if($login->url)
                                            <p><a href="{{ $login->url }}" target="_blank" rel="noreferrer" class="text-indigo-600 hover:underline truncate inline-flex items-center gap-1">Ir al sitio</a></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <a href="{{ route('projects.logins.edit', [$login->project_id, $login]) }}" class="px-3 py-1.5 rounded border border-gray-200 hover:bg-gray-50">Editar</a>
                                <form action="{{ route('projects.logins.destroy', [$login->project_id, $login]) }}" method="POST" onsubmit="return confirm('¿Eliminar este login?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded border border-red-200 text-red-700 hover:bg-red-50">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="px-4 sm:px-5 py-6 text-sm text-gray-600">No hay logins para los filtros aplicados.</p>
                    @endforelse
                </div>

                <div class="px-4 sm:px-5 py-3">
                    {{ $logins->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
