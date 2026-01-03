<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Timer</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cronómetro de tareas</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="timerApp(@json($prefillTask))" x-init="init()">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-4 sm:p-6">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Selecciona de la lista o escribe una tarea y ejecuta el cronómetro</p>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">Recordatorio cada 20 min</span>
                                <span class="px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold">Máximo 2 horas</span>
                            </div>
                        </div>
                        <div class="w-full sm:w-72 text-sm bg-gray-50 border border-gray-200 rounded px-3 py-2">
                            <p class="text-gray-500">Tarea seleccionada</p>
                            <p class="font-semibold text-gray-900 truncate" x-text="taskLabelDisplay"></p>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm text-gray-600">Nombre de la tarea</label>
                            <input type="text" x-model="manualTaskName" placeholder="Ej: Llamada con cliente" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Proyecto</label>
                            <select x-model="manualProjectId" x-ref="projectSelect" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sin proyecto</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2 flex items-center justify-end">
                            <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm" @click="clearManual()">Limpiar</button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid sm:grid-cols-2 gap-6">
                    <div class="bg-indigo-600 text-white rounded-2xl p-6 shadow-md flex flex-col items-center gap-4">
                        <div class="text-sm uppercase tracking-wide">Tiempo transcurrido</div>
                        <div class="text-5xl font-mono" x-text="formattedTime"></div>
                        <div class="flex gap-3">
                            <button type="button" @click="start()" :disabled="!taskLabel || running || elapsed >= maxSeconds" class="px-4 py-2 rounded bg-white text-indigo-700 font-semibold shadow hover:bg-indigo-50 disabled:opacity-50">Iniciar</button>
                            <button type="button" @click="pause()" :disabled="!running" class="px-4 py-2 rounded bg-amber-100 text-amber-800 font-semibold hover:bg-amber-200 disabled:opacity-50">Pausar</button>
                            <button type="button" @click="stop()" class="px-4 py-2 rounded bg-red-100 text-red-800 font-semibold hover:bg-red-200">Detener</button>
                        </div>
                        <div class="text-xs text-indigo-100">
                            Próximo aviso en <span class="font-semibold" x-text="nextBeepIn"></span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Estado</span>
                            <span class="font-semibold" :class="running ? 'text-emerald-600' : 'text-gray-700'" x-text="running ? 'En ejecución' : 'En pausa'"></span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Límite</span>
                            <span class="font-semibold text-gray-800" x-text="`${maxSeconds / 3600} h`"></span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Recordatorio</span>
                            <span class="font-semibold text-gray-800">cada 20 min</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Tarea seleccionada</span>
                            <span class="font-semibold text-gray-800 truncate max-w-[200px]" x-text="taskLabelDisplay"></span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Proyecto</span>
                            <span class="font-semibold text-gray-800 truncate max-w-[200px]" x-text="currentProjectLabel || 'Sin proyecto'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-500">Tus tareas pendientes</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $tasks->count() }} tareas</p>
                    </div>
                </div>
                <div x-cloak x-show="recentCreated.length" class="mb-4">
                    <p class="text-sm font-semibold text-gray-800">Tareas creadas con el timer</p>
                    <div class="space-y-2 mt-2">
                        <template x-for="item in recentCreated" :key="item.at">
                            <div class="flex items-center justify-between rounded border border-gray-100 px-3 py-2 bg-emerald-50">
                                <div>
                                    <p class="font-semibold text-gray-900" x-text="item.name"></p>
                                    <p class="text-xs text-gray-600">Puntos: <span x-text="item.points"></span></p>
                                </div>
                                <span class="text-xs text-gray-500" x-text="item.at"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($tasks as $task)
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <div class="h-11 w-11 rounded-full flex items-center justify-center text-xs font-semibold text-white ring-2 ring-white" style="background: {{ $task->project->color ?? '#9ca3af' }}">
                                        {{ collect(explode(' ', trim($task->project->name ?? '?')))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('') ?: '?' }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <button type="button" class="font-semibold text-gray-900 text-left hover:underline" @click="loadPanel('{{ route('tasks.show', $task) }}?sidebar=1')">
                                                    {{ $task->name }}
                                                </button>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    @if($task->project)
                                                        <span class="inline-flex items-center gap-1">
                                                            <span class="h-2 w-2 rounded-full" style="background: {{ $task->project->color ?? '#6366f1' }}"></span>
                                                            {{ $task->project->name }}
                                                        </span>
                                                    @else
                                                        Sin proyecto
                                                    @endif
                                                </p>
                                            </div>
                                            @if($task->status)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $task->status->background_color ?? '#eef2ff' }}; color: {{ $task->status->color ?? '#312e81' }}">
                                                    {{ $task->status->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-indigo-50 text-indigo-700">{{ $task->points ?? '—' }} pts</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-700">ID #{{ $task->id }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    class="flex items-center gap-1 px-3 py-2 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold hover:bg-indigo-100"
                                    @click="setTask(@js($task->id), @js($task->name), @js($task->project_id), @js($task->project->name ?? ''))"
                                >
                                    ▶ <span>Usar</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No tienes tareas pendientes asignadas.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div x-cloak x-show="showTaskPanel" class="fixed inset-0 z-40">
            <div class="absolute inset-0 bg-gray-900/50" @click="showTaskPanel = false" x-transition.opacity></div>
            <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl border-l border-gray-200 flex flex-col" x-transition>
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Detalle de tarea</p>
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

    <script>
        function timerApp(prefillTask = null) {
            return {
                prefillTask,
                running: false,
                elapsed: 0,
                intervalId: null,
                startedAt: null,
                elapsedBaseline: 0,
                selectedTask: '',
                selectedTaskLabel: '',
                manualTaskName: '',
                manualProjectId: '',
                projectLabel: '',
                recentCreated: [],
                showTaskPanel: false,
                taskPanelHtml: '',
                maxSeconds: {{ $maxSeconds }},
                beepEvery: 1200,
                lastBeepAt: 0,
                async init() {
                    await this.loadStatus();
                    if (this.prefillTask) {
                        this.applyPrefill(this.prefillTask);
                        this.prefillTask = null;
                    }
                },
                formattedTime() {
                    const hrs = Math.floor(this.elapsed / 3600);
                    const mins = Math.floor((this.elapsed % 3600) / 60);
                    const secs = this.elapsed % 60;
                    return `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                },
                get nextBeepIn() {
                    const diff = this.beepEvery - (this.elapsed - this.lastBeepAt);
                    if (diff <= 0) return '0s';
                    const mins = Math.floor(diff / 60);
                    const secs = diff % 60;
                    return `${mins}m ${secs}s`;
                },
                get isManualMode() {
                    const manual = this.manualTaskName.trim();
                    if (!manual) {
                        return false;
                    }
                    if (!this.selectedTask) {
                        return true;
                    }
                    return manual !== (this.selectedTaskLabel || '').trim();
                },
                get taskLabel() {
                    if (this.isManualMode) {
                        return this.manualTaskName.trim();
                    }
                    return (this.selectedTaskLabel || '').trim();
                },
                get taskLabelDisplay() {
                    return this.taskLabel || 'Ninguna';
                },
                get currentProjectLabel() {
                    if (this.isManualMode) {
                        return this.getProjectLabelFromSelect();
                    }
                    return this.projectLabel || '';
                },
                setTask(id, label, projectId = '', projectName = '') {
                    this.selectedTask = id ? String(id) : '';
                    this.selectedTaskLabel = label ? String(label) : '';
                    this.manualTaskName = label ? String(label) : '';
                    this.manualProjectId = projectId ? String(projectId) : '';
                    this.projectLabel = projectName ? String(projectName) : '';
                    this.$nextTick(() => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                },
                applyPrefill(taskData) {
                    if (!taskData) {
                        return;
                    }
                    this.setTask(
                        taskData.id ?? '',
                        taskData.name ?? '',
                        taskData.project_id ?? '',
                        taskData.project_name ?? ''
                    );
                    if (taskData.name) {
                        this.manualTaskName = taskData.name;
                    }
                },
                clearManual() {
                    this.manualTaskName = '';
                    this.manualProjectId = '';
                    this.projectLabel = '';
                    this.selectedTaskLabel = '';
                    this.selectedTask = '';
                },
                loadPanel(url) {
                    if (!url) return;
                    this.showTaskPanel = true;
                    this.taskPanelHtml = '<div class="p-4 text-sm text-gray-600">Cargando...</div>';
                    fetch(url)
                        .then(r => r.text())
                        .then(html => { this.taskPanelHtml = html || '<div class="p-4 text-sm text-gray-600">Sin contenido.</div>'; })
                        .catch(() => {
                            this.showTaskPanel = false;
                            this.taskPanelHtml = '';
                        });
                },
                applyServerState(data) {
                    if (typeof data?.max_seconds === 'number') {
                        this.maxSeconds = data.max_seconds;
                    }
                    this.running = Boolean(data?.running);
                    this.elapsed = Number(data?.elapsed ?? 0);
                    this.elapsedBaseline = this.elapsed;
                    this.selectedTask = data?.task_id ? String(data.task_id) : '';
                    this.selectedTaskLabel = data?.task_label || '';
                    this.manualTaskName = this.selectedTask ? '' : (data?.task_label || '');
                    this.manualProjectId = data?.project_id ? String(data.project_id) : '';
                    this.projectLabel = data?.project_name || '';
                    this.lastBeepAt = Math.floor(this.elapsed / this.beepEvery) * this.beepEvery;
                    this.clearTicker();
                    if (this.running) {
                        this.startedAt = Date.now();
                        this.startTicker();
                    } else {
                        this.startedAt = null;
                    }
                },
                startTicker() {
                    if (!this.running) {
                        return;
                    }
                    this.clearTicker();
                    const startedAt = this.startedAt ?? Date.now();
                    this.startedAt = startedAt;
                    const base = this.elapsedBaseline ?? 0;
                    this.intervalId = setInterval(() => {
                        const total = Math.min(base + Math.floor((Date.now() - startedAt) / 1000), this.maxSeconds);
                        this.elapsed = total;
                        if (this.elapsed - this.lastBeepAt >= this.beepEvery) {
                            this.playBeep();
                            this.lastBeepAt = this.elapsed;
                        }
                        if (this.elapsed >= this.maxSeconds) {
                            this.elapsed = this.maxSeconds;
                            this.stop();
                        }
                    }, 1000);
                },
                clearTicker() {
                    if (this.intervalId) {
                        clearInterval(this.intervalId);
                        this.intervalId = null;
                    }
                },
                async loadStatus() {
                    try {
                        const response = await fetch('{{ route('timer.status') }}', {
                            headers: {
                                'Accept': 'application/json',
                            },
                        });
                        if (!response.ok) {
                            return;
                        }
                        const data = await response.json();
                        this.applyServerState(data);
                    } catch (e) {
                        console.error(e);
                    }
                },
                async start() {
                    const label = this.taskLabel;
                    if (!label || this.running || this.elapsed >= this.maxSeconds) {
                        return;
                    }
                    const payload = {
                        task_id: this.isManualMode ? null : (this.selectedTask || null),
                        task_label: label,
                        project_id: this.manualProjectId || null,
                        project_name: this.currentProjectLabel || '',
                        _token: document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                    };
                    try {
                        const response = await fetch('{{ route('timer.start') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': payload._token,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload),
                        });
                        if (!response.ok) {
                            return;
                        }
                        const data = await response.json();
                        this.applyServerState(data);
                    } catch (e) {
                        console.error(e);
                    }
                },
                async pause() {
                    this.clearTicker();
                    try {
                        const response = await fetch('{{ route('timer.pause') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                        });
                        if (!response.ok) {
                            this.running = false;
                            return;
                        }
                        const data = await response.json();
                        this.applyServerState(data);
                    } catch (e) {
                        console.error(e);
                        this.running = false;
                    }
                },
                async stop() {
                    this.clearTicker();
                    await this.pause();
                    const label = this.taskLabel;
                    const payload = {
                        name: label,
                        project_id: this.manualProjectId || null,
                        seconds: this.elapsed,
                        _token: document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                    };
                    if (payload.name && payload.seconds > 0) {
                        try {
                            const response = await fetch('{{ route('timer.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': payload._token,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify(payload),
                            });
                            const data = await response.json().catch(() => null);
                            if (data && data.ok) {
                                this.recentCreated.unshift({
                                    name: payload.name,
                                    points: data.points,
                                    at: new Date().toLocaleTimeString(),
                                });
                                this.recentCreated = this.recentCreated.slice(0, 5);
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }
                    await this.resetRemote();
                    this.resetLocalState();
                },
                async resetRemote() {
                    try {
                        await fetch('{{ route('timer.reset') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                        });
                    } catch (e) {
                        console.error(e);
                    }
                },
                resetLocalState() {
                    this.clearTicker();
                    this.running = false;
                    this.elapsedBaseline = 0;
                    this.elapsed = 0;
                    this.lastBeepAt = 0;
                    this.startedAt = null;
                },
                async reset() {
                    await this.stop();
                    this.resetSelection();
                },
                resetSelection() {
                    this.selectedTask = '';
                    this.selectedTaskLabel = '';
                    this.manualTaskName = '';
                    this.manualProjectId = '';
                    this.projectLabel = '';
                },
                playBeep() {
                    const audio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEAIlYAAESsAAACABAAZGF0YYgAAACAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA=');
                    audio.play().catch(() => {});
                },
                getProjectLabelFromSelect() {
                    if (!this.manualProjectId) {
                        return '';
                    }
                    const select = this.$refs?.projectSelect ?? document.querySelector('select[x-model="manualProjectId"]');
                    if (!select) {
                        return '';
                    }
                    const option = select.selectedOptions?.[0]
                        ?? select.querySelector(`option[value="${this.manualProjectId}"]`);
                    return option ? option.textContent.trim() : '';
                },
            };
        }
    </script>
</x-app-layout>
