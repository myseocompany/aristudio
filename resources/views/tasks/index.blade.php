<x-app-layout>
    @php
        $selectedPointsDisplay = number_format((float) ($selectedPointsTotal ?? 0), 2, '.', ',');
        $defaultRangeStart = $defaultFromDate ?? now()->startOfMonth()->toDateString();
        $defaultRangeEnd = $defaultToDate ?? now()->endOfMonth()->toDateString();
    @endphp
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <p class="text-sm text-gray-500">Tareas</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Listado</h2>
            </div>
        </div>
    </x-slot>

    <div x-data="{
            showFilters: false,
            showCreate: false,
            createClicked: false,
            showTaskPanel: false,
            taskPanelHtml: '',
            loadingTaskId: null,
            loadPanel(url) {
                this.loadingTaskId = null;
                this.showTaskPanel = true;
                this.taskPanelHtml = '';
                fetch(url)
                    .then(r => r.text())
                    .then(html => { this.taskPanelHtml = html; })
                    .catch(() => {
                        this.showTaskPanel = false;
                        this.taskPanelHtml = '';
                    });
            }
        }">
        <div class="py-6">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="mb-4 text-green-700 bg-green-100 border border-green-200 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @php
            $currentRangeValue = $filters['from_date'] && $filters['to_date']
                ? $filters['from_date'].'|'.$filters['to_date']
                : $defaultRangeStart.'|'.$defaultRangeEnd;
            @endphp
            <div class="relative bg-white shadow-sm rounded border border-gray-100" x-data="{ showFilters: false }">
                <div class="px-4 py-3 border-b border-gray-100 flex flex-wrap items-center gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <p class="text-sm text-gray-500">Listado de tareas</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $tasks->total() }} registros</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 justify-end">
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">
                            {{ $selectedPointsDisplay }} pts
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold">{{ $tasks->where('status.pending', 1)->count() }} pendientes</span>
                        <form method="GET" id="tasksRangeForm" class="flex items-center gap-2">
                            <input type="text" id="tasksRangePicker" class="w-56 px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white shadow-sm cursor-pointer" readonly>
                            <input type="hidden" name="range" id="tasksRangeValue" value="{{ $currentRangeValue }}">
                            <input type="hidden" name="from_date" id="tasksFromDate" value="{{ $filters['from_date'] }}">
                            <input type="hidden" name="to_date" id="tasksToDate" value="{{ $filters['to_date'] }}">
                            <input type="hidden" name="status_id" value="{{ $filters['status_id'] }}">
                            <input type="hidden" name="project_id" value="{{ $filters['project_id'] }}">
                            <input type="hidden" name="user_id" value="{{ $filters['user_id'] }}">
                            <input type="hidden" name="q" value="{{ $filters['q'] }}">
                        </form>
                        <button type="button" @click="showFilters = !showFilters" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded border border-gray-200 text-gray-700">
                            <span x-show="!showFilters">Mostrar filtros</span>
                            <span x-show="showFilters">Ocultar filtros</span>
                        </button>
                        <button
                            type="button"
                            @click="showCreate = true; createClicked = true; setTimeout(() => createClicked = false, 800)"
                            class="hidden md:flex h-11 w-11 rounded-full bg-blue-600 text-white shadow-lg items-center justify-center text-xl hover:bg-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-300"
                            title="Nueva tarea"
                        >
                            +
                        </button>
                    </div>
                </div>

                <div class="px-4 py-4 border-b border-gray-100" x-show="showFilters" x-transition x-cloak>
                    <form method="get" class="grid gap-3 md:grid-cols-4" x-data="{
                        presets: @js($timePresets),
                        applyPreset(id) {
                            if (!id) return;
                            const preset = this.presets[id];
                            if (!preset) return;
                            this.$refs.from_date.value = preset.from;
                            this.$refs.to_date.value = preset.to;
                        }
                    }">
                        <input type="hidden" name="from_date" id="tasksFiltersFrom" value="{{ $filters['from_date'] }}">
                        <input type="hidden" name="to_date" id="tasksFiltersTo" value="{{ $filters['to_date'] }}">
                        <div class="md:col-span-2">
                            <label class="text-sm text-gray-600">Buscar</label>
                            <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Nombre, descripción o copia" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Estado</label>
                            <select name="status_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todos</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" @selected((string)$filters['status_id'] === (string)$status->id)>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Proyecto</label>
                            <select name="project_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todos</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" @selected((string)$filters['project_id'] === (string)$project->id)>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Responsable</label>
                            <select name="user_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todos</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected((string)$filters['user_id'] === (string)$user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-2 md:col-span-3">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Aplicar filtros</button>
                            @php
                                $hasFilters = $filters['status_id']
                                    || $filters['project_id']
                                    || $filters['user_id']
                                    || $filters['q']
                                    || $filters['value_generated']
                                    || $filters['from_date'] !== $defaultRangeStart
                                    || $filters['to_date'] !== $defaultRangeEnd;
                            @endphp
                            @if($hasFilters)
                                <a href="{{ route('tasks.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Limpiar</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto hidden md:block">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600 uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3">Tarea</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Vence</th>
                                <th class="px-4 py-3">Trabajo</th>
                                <th class="px-4 py-3 text-center w-16">Resp.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tasks as $task)
                                @php
                                    $project = $task->project;
                                    $status = $task->status;
                                    $user = $task->user;
                                    $dueDate = $task->due_date;
                                    $completedIds = [6, 56, 57];
                                    $isComplete = in_array($status?->id, $completedIds, true);
                                    $isOverdue = ! $isComplete && $dueDate && $dueDate->isPast() && ($status?->pending ?? false);
                                    $projectInitials = $project
                                        ? collect(explode(' ', trim($project->name)))
                                            ->filter()
                                            ->map(fn ($w) => mb_substr($w, 0, 1))
                                            ->take(2)
                                            ->implode('')
                                        : '?';
                                    $imgPath = $user?->image_url
                                        ? (str_contains($user->image_url, '/') ? $user->image_url : 'files/users/'.$user->image_url)
                                        : null;
                                    $initials = collect(explode(' ', trim($user->name ?? '')))
                                        ->filter()
                                        ->map(fn ($part) => mb_substr($part, 0, 1))
                                        ->take(2)
                                        ->implode('');
                                    $pointsUsed = (float) ($task->points ?? 0);
                                    $maxPoints = 2;
                                    $progressPct = max(0, min(1, $pointsUsed / $maxPoints)) * 100;
                                    $palette = [
                                        'bg-amber-100 text-amber-800',
                                        'bg-indigo-100 text-indigo-800',
                                        'bg-emerald-100 text-emerald-800',
                                        'bg-sky-100 text-sky-800',
                                        'bg-pink-100 text-pink-800',
                                        'bg-slate-100 text-slate-800',
                                        'bg-purple-100 text-purple-800',
                                        'bg-teal-100 text-teal-800',
                                    ];
                                    $colorClass = $user ? $palette[$user->id % count($palette)] : 'bg-gray-100 text-gray-600';
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-start gap-3">
                                            <div class="h-10 w-10 rounded-full flex items-center justify-center text-xs font-semibold text-white" style="background: {{ $project->color ?? '#9ca3af' }}">
                                                {{ $projectInitials ?: '?' }}
                                            </div>
                                            <div>
                                                <button type="button" class="font-semibold text-gray-900 hover:underline text-left" @click="loadPanel('{{ route('tasks.show', $task) }}?sidebar=1')">
                                                    {{ $task->name }}
                                                </button>
                                                <div class="text-xs text-gray-500 leading-relaxed">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($task->description ?? ''), 120) ?: 'Sin descripción' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($status)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $status->background_color ?? '#eef2ff' }}; color: {{ $status->color ?? '#312e81' }}">
                                                {{ $status->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                        @if($dueDate)
                                            <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                                                {{ $dueDate->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">Sin fecha</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="space-y-1">
                                            <div class="bg-gray-100 rounded-full h-2 overflow-hidden">
                                                <div class="h-2 rounded-full" style="width: {{ $progressPct }}%; background: linear-gradient(90deg, #6366f1, #22c55e);"></div>
                                            </div>
                                            <div class="text-xs text-gray-600">{{ $pointsUsed }} / {{ $maxPoints }} pts</div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($user)
                                            @if($imgPath)
                                                <img src="{{ asset('storage/'.$imgPath) }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-100 mx-auto" alt="{{ $user->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full flex items-center justify-center text-xs font-semibold {{ $colorClass }} mx-auto">
                                                    {{ $initials ?: '?' }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="h-10 w-10 rounded-full flex items-center justify-center text-xs font-semibold bg-gray-100 text-gray-500 mx-auto">
                                                —
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-6 text-center text-gray-500">No hay tareas que coincidan con el filtro.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden px-4 py-4 space-y-3">
                    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-indigo-50 to-slate-50 px-4 py-3 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Tareas totales</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $tasks->total() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Puntos filtrados</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $selectedPointsDisplay }} pts</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                            {{ $tasks->where('status.pending', 1)->count() }} pendientes
                        </span>
                    </div>
                    @forelse($tasks as $task)
                        @php
                            $project = $task->project;
                            $status = $task->status;
                            $user = $task->user;
                            $dueDate = $task->due_date;
                            $completedIds = [6, 56, 57];
                            $isComplete = in_array($status?->id, $completedIds, true);
                            $isOverdue = ! $isComplete && $dueDate && $dueDate->isPast() && ($status?->pending ?? false);
                            $projectInitials = $project
                                ? collect(explode(' ', trim($project->name)))
                                    ->filter()
                                    ->map(fn ($w) => mb_substr($w, 0, 1))
                                    ->take(2)
                                    ->implode('')
                                : '?';
                            $imgPath = $user?->image_url
                                ? (str_contains($user->image_url, '/') ? $user->image_url : 'files/users/'.$user->image_url)
                                : null;
                            $initials = collect(explode(' ', trim($user->name ?? '')))
                                ->filter()
                                ->map(fn ($part) => mb_substr($part, 0, 1))
                                ->take(2)
                                ->implode('');
                            $pointsUsed = (float) ($task->points ?? 0);
                            $maxPoints = 2;
                            $progressPct = max(0, min(1, $pointsUsed / $maxPoints)) * 100;
                            $palette = [
                                'bg-amber-100 text-amber-800',
                                'bg-indigo-100 text-indigo-800',
                                'bg-emerald-100 text-emerald-800',
                                'bg-sky-100 text-sky-800',
                                'bg-pink-100 text-pink-800',
                                'bg-slate-100 text-slate-800',
                                'bg-purple-100 text-purple-800',
                                'bg-teal-100 text-teal-800',
                            ];
                            $colorClass = $user ? $palette[$user->id % count($palette)] : 'bg-gray-100 text-gray-600';
                        @endphp
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <div class="h-11 w-11 rounded-full flex items-center justify-center text-xs font-semibold text-white ring-2 ring-white" style="background: {{ $project->color ?? '#9ca3af' }}">
                                        {{ $projectInitials ?: '?' }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-2">
        <div>
                                            <button type="button" class="font-semibold text-gray-900 text-left hover:underline" @click="loadPanel('{{ route('tasks.show', $task) }}?sidebar=1')">
                                                {{ $task->name }}
                                            </button>
            <p class="text-xs text-gray-500">
                @if($project)
                    <span class="inline-flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                        {{ $project->name }}
                    </span>
                @else
                    Sin proyecto
                @endif
            </p>
        </div>
                                            @if($status)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $status->background_color ?? '#eef2ff' }}; color: {{ $status->color ?? '#312e81' }}">
                                                    {{ $status->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                                            <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                                @if($dueDate)
                                                    {{ $dueDate->format('d M Y') }}
                                                @else
                                                    Sin fecha
                                                @endif
                                            </span>
                                            @if($user)
                                                @if($imgPath)
                                                    <img src="{{ asset('storage/'.$imgPath) }}" class="h-7 w-7 rounded-full object-cover ring-2 ring-gray-100" alt="{{ $user->name }}">
                                                @else
                                                    <div class="h-7 w-7 rounded-full flex items-center justify-center text-xs font-semibold {{ $colorClass }}">
                                                        {{ $initials ?: '?' }}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                @if($task->type?->name)
                                    <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">{{ $task->type->name }}</span>
                                @endif
                                @if($task->subType?->name)
                                    <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">{{ $task->subType->name }}</span>
                                @endif
                                <span class="px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs">{{ $pointsUsed }} / {{ $maxPoints }} pts</span>
                            </div>
                            <div class="mt-2 space-y-1">
                                <div class="bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full" style="width: {{ $progressPct }}%; background: linear-gradient(90deg, #6366f1, #22c55e);"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 text-sm py-6">No hay tareas que coincidan con el filtro.</div>
                    @endforelse
                </div>
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between text-sm text-gray-600">
                    <div>Mostrando {{ $tasks->firstItem() }}-{{ $tasks->lastItem() }} de {{ $tasks->total() }}</div>
                    <div>
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-cloak x-show="showCreate" class="fixed inset-0 z-30">
        <div class="absolute inset-0 bg-gray-900/50" @click="showCreate = false" x-transition.opacity></div>
        <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white shadow-xl border-l border-gray-200 flex flex-col" x-transition>
            <div class="px-4 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Nueva tarea rápida</p>
                    <h3 class="text-lg font-semibold text-gray-900">Crear tarea</h3>
                </div>
                <button type="button" class="text-gray-500 hover:text-gray-700" @click="showCreate = false">✕</button>
            </div>
                <div class="p-4 overflow-y-auto">
                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data" x-data="{ showQuickAdvanced: false }">
                    @csrf
                    <p class="text-sm font-semibold text-gray-800">Haz clic aquí y empieza a escribir 😎</p>
                    <div>
                        <x-input-label for="quick_name" value="Nombre" />
                        <x-text-input id="quick_name" name="name" type="text" class="mt-1 block w-full" required />
                        </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="quick_project_id" value="Proyecto" />
                            <select id="quick_project_id" name="project_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sin proyecto</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="quick_user_id" value="Responsable" />
                            <select id="quick_user_id" name="user_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sin asignar</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                        <div>
                            <x-input-label for="quick_url_finished" value="URL entregable" />
                            <x-text-input id="quick_url_finished" name="url_finished" type="url" class="mt-1 block w-full" />
                        </div>
                        <div class="flex items-center justify-between border border-dashed border-gray-200 rounded px-3 py-2 bg-gray-50">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Datos adicionales</p>
                                <p class="text-xs text-gray-500">Estado, prioridad, fechas y más.</p>
                            </div>
                            <button type="button" @click="showQuickAdvanced = !showQuickAdvanced" class="px-2 py-1 text-sm bg-white border border-gray-200 rounded hover:bg-gray-100">
                                <span x-show="!showQuickAdvanced">Mostrar</span>
                                <span x-show="showQuickAdvanced">Ocultar</span>
                            </button>
                        </div>
                        <div class="space-y-4" x-show="showQuickAdvanced" x-transition x-cloak>
                            <div>
                                <x-input-label for="quick_status_id" value="Estado" />
                                <select id="quick_status_id" name="status_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" @selected((string)$defaultStatusId === (string)$status->id)>{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-input-label for="quick_priority" value="Prioridad (1 a 10)" />
                                    <input id="quick_priority" name="priority" type="range" min="1" max="10" step="1" value="5" class="mt-2 w-full accent-indigo-600">
                                    <div class="text-xs text-gray-600 mt-1 flex justify-between">
                                        <span>Baja</span>
                                        <span class="font-semibold">5</span>
                                        <span>Alta</span>
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="quick_points" value="Puntos" />
                                    <x-text-input id="quick_points" name="points" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-input-label for="quick_estimated_points" value="Estimado" />
                                    <x-text-input id="quick_estimated_points" name="estimated_points" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <x-input-label for="quick_due_date" value="Fecha de vencimiento" />
                                    <x-text-input id="quick_due_date" name="due_date" type="datetime-local" class="mt-1 block w-full" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-input-label for="quick_delivery_date" value="Fecha de entrega" />
                                    <x-text-input id="quick_delivery_date" name="delivery_date" type="datetime-local" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <x-input-label for="quick_type_id" value="Tipo" />
                                    <select id="quick_type_id" name="type_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Sin tipo</option>
                                        @foreach($parentTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <x-input-label for="quick_sub_type_id" value="Subtipo" />
                                <select id="quick_sub_type_id" name="sub_type_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Sin subtipo</option>
                                    @foreach($subTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->label ?? $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="quick_description" value="Descripción" />
                                <textarea id="quick_description" name="description" rows="2" class="mt-1 block w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-input-label for="quick_copy" value="Copy / instrucciones" />
                                    <textarea id="quick_copy" name="copy" rows="2" class="mt-1 block w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>
                                <div>
                                    <x-input-label for="quick_caption" value="Caption" />
                                    <textarea id="quick_caption" name="caption" rows="2" class="mt-1 block w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>
                            </div>
                            <div>
                                <x-input-label for="quick_file" value="Archivo" />
                                <input id="quick_file" name="file" type="file" class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showCreate = false" class="text-sm text-gray-600 hover:text-gray-800">Cancelar</button>
                        <x-primary-button>Guardar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <button
        type="button"
        @click="showCreate = true; createClicked = true; setTimeout(() => createClicked = false, 800)"
        class="fixed top-20 right-4 h-12 w-12 rounded-full bg-blue-600 text-white shadow-lg flex items-center justify-center text-2xl hover:bg-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-300 md:hidden"
        title="Nueva tarea"
    >
        +
    </button>
    <div x-cloak x-show="showTaskPanel" class="fixed inset-0 z-40">
        <div class="absolute inset-0 bg-gray-900/50" @click="showTaskPanel = false" x-transition.opacity></div>
        <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl border-l border-gray-200 flex flex-col" x-transition>
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Detalle de tarea</p>
                    <p class="text-xs text-gray-400" x-show="loadingTaskId">Cargando...</p>
                </div>
                <button type="button" class="text-sm text-gray-600 hover:text-gray-800" @click="showTaskPanel = false">Cerrar</button>
            </div>
            <div class="flex-1 overflow-y-auto" x-ref="taskPanelBody" x-html="taskPanelHtml" x-init="
                const body = $refs.taskPanelBody;
                body.addEventListener('click', (e) => {
                    const link = e.target.closest('[data-task-panel-url]');
                    if (link) {
                        e.preventDefault();
                        loadPanel(link.getAttribute('data-task-panel-url'));
                    }
                });
            "></div>
        </div>
    </div>
</div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment-with-locales.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        moment.locale('es');
        const rangeInput = $('#tasksRangePicker');
        const rangeValue = $('#tasksRangeValue');
        const fromField = $('#tasksFromDate');
        const toField = $('#tasksToDate');
        const rangeForm = document.getElementById('tasksRangeForm');
        const filterFromField = $('#tasksFiltersFrom');
        const filterToField = $('#tasksFiltersTo');

        if (!rangeInput.length || !rangeForm) {
            return;
        }

        const existingRange = rangeValue.val();
        const defaultStart = moment('{{ $defaultRangeStart }}', 'YYYY-MM-DD');
        const defaultEnd = moment('{{ $defaultRangeEnd }}', 'YYYY-MM-DD');
        const [startRaw, endRaw] = existingRange ? existingRange.split('|') : [null, null];
        const startDate = startRaw ? moment(startRaw, 'YYYY-MM-DD') : defaultStart;
        const endDate = endRaw ? moment(endRaw, 'YYYY-MM-DD') : defaultEnd;

        function updateDisplay(start, end) {
            const startStr = start.format('YYYY-MM-DD');
            const endStr = end.format('YYYY-MM-DD');
            rangeInput.val(`${start.format('DD MMM YYYY')} - ${end.format('DD MMM YYYY')}`);
            rangeValue.val(`${startStr}|${endStr}`);
            fromField.val(startStr);
            toField.val(endStr);
            filterFromField.val(startStr);
            filterToField.val(endStr);
        }

        rangeInput.daterangepicker({
            startDate,
            endDate,
            ranges: {
                'Hoy': [moment().startOf('day'), moment().endOf('day')],
                'Este mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Trimestre pasado': [moment().subtract(3, 'month').startOf('quarter'), moment().subtract(3, 'month').endOf('quarter')],
                'Semestre pasado': (function () {
                    const start = moment().startOf('month').subtract(6, 'month').startOf('month');
                    const end = start.clone().add(5, 'month').endOf('month');
                    return [start, end];
                })(),
                'Este año': [moment().startOf('year'), moment().endOf('year')],
                'Año pasado': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            },
            locale: {
                format: 'DD MMM YYYY',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
            },
            opens: 'left',
        }, function(start, end) {
            updateDisplay(start, end);
            rangeForm.submit();
        });

        updateDisplay(startDate, endDate);
    });
</script>
