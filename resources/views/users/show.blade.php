<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="text-xs text-gray-500 mb-1 space-x-1">
                    <a href="{{ route('users.index') }}" class="hover:text-indigo-600">Usuarios</a>
                    <span>/</span>
                    <span class="text-gray-700">Perfil</span>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $user->name }}</h2>
            </div>
            <a href="{{ route('users.edit', $user->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Editar</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Avatar + info --}}
                <div class="lg:col-span-2 bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-1/3 bg-gray-50 flex items-center justify-center p-6">
                            @php
                                $imgPath = $user->image_url
                                    ? (str_contains($user->image_url, '/') ? $user->image_url : 'files/users/'.$user->image_url)
                                    : null;
                                $initials = collect(explode(' ', trim($user->name)))
                                    ->filter()
                                    ->map(fn($part) => mb_substr($part, 0, 1))
                                    ->take(2)
                                    ->implode('');
                            @endphp
                            <div class="h-36 w-36 rounded-full shadow-lg bg-gradient-to-br from-orange-400 via-pink-500 to-indigo-500 p-1">
                                <div class="h-full w-full rounded-full overflow-hidden bg-white/10">
                                    @if($imgPath)
                                        <img src="{{ asset('storage/'.$imgPath) }}" class="h-full w-full object-cover" alt="{{ $user->name }}">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center text-3xl font-semibold text-white">
                                            {{ $initials ?: '?' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="md:w-2/3 p-6 space-y-4">
                            <div class="flex items-center gap-3">
                                <div>
                                    <h1 class="text-2xl font-semibold text-gray-900">{{ $user->name }}</h1>
                                    <p class="text-sm text-gray-500">{{ $user->position }}</p>
                                </div>
                                @php
                                    $isActive = ($user->status_id ?? null) == 1;
                                    $statusColor = $isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">{{ $user->status_name ?? 'N/A' }}</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div class="space-y-1">
                                    <p class="text-gray-500">Email</p>
                                    <p class="text-gray-800">{{ $user->email }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-gray-500">Teléfono</p>
                                    <p class="text-gray-800">{{ $user->phone }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-gray-500">Documento</p>
                                    <p class="text-gray-800">{{ $user->document }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-gray-500">Dirección</p>
                                    <p class="text-gray-800">{{ $user->address }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-gray-500">Rol</p>
                                    <p class="text-gray-800">{{ $user->role_name }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-gray-500">Último login</p>
                                    <p class="text-gray-800">
                                        {{ $user->last_login ? \Illuminate\Support\Carbon::parse($user->last_login)->format('Y-m-d') : '—' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Estado laboral --}}
                <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Estado laboral</h3>
                        <span class="px-2 py-1 text-xs bg-indigo-50 text-indigo-700 rounded-full">Ficha</span>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Fecha ingreso</span>
                            <span class="text-gray-800">{{ $user->entry_date ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Fecha salida</span>
                            <span class="text-gray-800">{{ $user->termination_date ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Horas contratadas</span>
                            <span class="text-gray-800">{{ $user->contracted_hours ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Tipo contrato</span>
                            <span class="text-gray-800">{{ $user->contract_type ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Tarifa/hora</span>
                            <span class="text-gray-800">{{ $user->hourly_rate ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Disponibilidad</span>
                            <span class="text-gray-800">{{ $user->availability ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Salud --}}
                <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Salud</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">EPS</span>
                            <span class="text-gray-800">{{ $user->eps ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">ARL</span>
                            <span class="text-gray-800">{{ $user->arl ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Tipo de sangre</span>
                            <span class="text-gray-800">{{ $user->blood_type ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Fechas --}}
                <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Fechas</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Nacimiento</span>
                            <span class="text-gray-800">{{ $user->birth_date ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Creado</span>
                            <span class="text-gray-800">{{ $user->created_at ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Actualizado</span>
                            <span class="text-gray-800">{{ $user->updated_at ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-3" id="projects">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-gray-900">Proyectos</h3>
                        <span class="text-xs text-gray-500">{{ count($projects ?? []) }} asignados</span>
                    </div>
                    <a href="{{ route('users.edit', $user->id) }}#projects" class="text-xs text-indigo-600 hover:text-indigo-500 font-semibold">Gestionar</a>
                </div>
                @if(!empty($projects) && count($projects))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($projects as $project)
                            @php
                                $color = $project->color ?: '#e5e7eb';
                                $status = $project->status_name ?: '—';
                            @endphp
                            <a href="{{ route('projects.show', $project->id) }}" class="flex items-center gap-3 border rounded-lg px-3 py-2 hover:border-indigo-200">
                                <span class="h-9 w-9 flex items-center justify-center rounded-lg text-white font-semibold text-sm" style="background-color: {{ $color }};">
                                    {{ mb_substr($project->name,0,1) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $project->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $status }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Sin proyectos asignados.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
