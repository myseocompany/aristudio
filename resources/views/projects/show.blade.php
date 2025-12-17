<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Proyecto</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $project->name }}</h2>
            </div>
            <a href="{{ route('projects.edit', $project->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Editar</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 space-y-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900">{{ $project->name }}</h1>
                                <div class="text-sm text-gray-700 leading-relaxed">
                                    {!! $project->description !!}
                                </div>
                            </div>
                            @php
                                $isActive = ($project->status_id ?? null) == 1;
                                $statusColor = $isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">{{ $statusName ?? 'N/A' }}</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="space-y-1">
                                <p class="text-gray-500">Tipo</p>
                                <p class="text-gray-800">{{ $typeName ?? '—' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-gray-500">Color</p>
                                <p class="text-gray-800">{{ $project->color ?? '—' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-gray-500">Inicio</p>
                                <p class="text-gray-800">{{ $project->start_date ?? '—' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-gray-500">Fin</p>
                                <p class="text-gray-800">{{ $project->finish_date ?? '—' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-gray-500">Presupuesto</p>
                                <p class="text-gray-800">{{ $project->budget ?? '—' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-gray-500">Presupuesto Ads</p>
                                <p class="text-gray-800">{{ $project->ads_budget ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Metas</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Weekly pieces</span>
                            <span class="text-gray-800">{{ $project->weekly_pieces ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Lead target</span>
                            <span class="text-gray-800">{{ $project->lead_target ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Monthly points</span>
                            <span class="text-gray-800">{{ $project->monthly_points_goal ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Sales</span>
                            <span class="text-gray-800">{{ $project->sales ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-3">
                    <h3 class="font-semibold text-gray-900">Fechas</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Creado</span>
                            <span class="text-gray-800">{{ $project->created_at ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Actualizado</span>
                            <span class="text-gray-800">{{ $project->updated_at ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-3">
                    <h3 class="font-semibold text-gray-900">Notas</h3>
                    <p class="text-sm text-gray-500">Agrega aquí detalles adicionales del proyecto.</p>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-3" id="team">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-gray-900">Usuarios asignados</h3>
                        <span class="text-xs text-gray-500">{{ $project->users->count() }} usuarios</span>
                    </div>
                    <a href="{{ route('projects.edit', $project->id) }}#team" class="text-xs text-indigo-600 hover:text-indigo-500 font-semibold">Gestionar</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($project->users as $user)
                        @php
                            $initials = collect(explode(' ', trim($user->name)))
                                ->filter()
                                ->map(fn($part) => mb_substr($part, 0, 1))
                                ->take(2)
                                ->implode('');
                            $img = $user->image_url ? (str_contains($user->image_url, '/') ? $user->image_url : 'files/users/'.$user->image_url) : null;
                            $roleName = $roleNames[$user->role_id] ?? '—';
                            $statusName = $userStatusNames[$user->status_id] ?? '—';
                        @endphp
                        <div class="flex items-center gap-3 border rounded-lg px-3 py-2">
                            <div class="h-10 w-10 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-pink-500 text-white flex items-center justify-center text-sm font-semibold">
                                @if($img)
                                    <img src="{{ asset('storage/'.$img) }}" class="h-full w-full object-cover" alt="{{ $user->name }}">
                                @else
                                    {{ $initials ?: '?' }}
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $roleName }}</p>
                            </div>
                            <span class="text-[11px] px-2 py-1 rounded-full bg-gray-50 text-gray-700">{{ $statusName }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Sin usuarios asignados.</p>
                    @endforelse
                </div>
            </div>

            @if($canManageLogins)
                <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-3" id="logins">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-gray-900">Logins del proyecto</h3>
                            <span class="text-xs text-gray-500">{{ $logins->count() }} registros</span>
                        </div>
                        <a href="{{ route('projects.logins.create', $project->id) }}" class="text-xs text-indigo-600 hover:text-indigo-500 font-semibold">Nuevo</a>
                    </div>
                    @if($logins->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($logins as $login)
                                <div class="border rounded-lg px-4 py-3 space-y-2 hover:border-indigo-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $login->name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ $login->url }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs">
                                            <a href="{{ route('projects.logins.edit', [$project->id, $login->id]) }}" class="text-indigo-600 hover:text-indigo-500 font-semibold">Editar</a>
                                            <form action="{{ route('projects.logins.destroy', [$project->id, $login->id]) }}" method="POST" onsubmit="return confirm('¿Eliminar este login?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-500 font-semibold">Borrar</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-500">Usuario</span>
                                            <span class="font-medium">{{ $login->user }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-500">Clave</span>
                                            <span class="font-mono text-xs bg-gray-50 px-2 py-1 rounded">{{ $login->password }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Sin logins registrados.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
