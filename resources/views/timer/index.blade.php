<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Timer</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cronómetro de tareas</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="timerApp()">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Selecciona una tarea y ejecuta el cronómetro</p>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">Recordatorio cada 20 min</span>
                            <span class="px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold">Máximo 2 horas</span>
                        </div>
                    </div>
                    <div class="w-full sm:w-72">
                        <label class="text-sm text-gray-600">Tarea</label>
                        <select x-model="selectedTask" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Selecciona tarea</option>
                            @foreach($tasks as $task)
                                <option value="{{ $task->id }}">{{ $task->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 grid sm:grid-cols-2 gap-6">
                    <div class="bg-indigo-600 text-white rounded-2xl p-6 shadow-md flex flex-col items-center gap-4">
                        <div class="text-sm uppercase tracking-wide">Tiempo transcurrido</div>
                        <div class="text-5xl font-mono" x-text="formattedTime"></div>
                        <div class="flex gap-3">
                            <button type="button" @click="start()" :disabled="!selectedTask || running || elapsed >= maxSeconds" class="px-4 py-2 rounded bg-white text-indigo-700 font-semibold shadow hover:bg-indigo-50 disabled:opacity-50">Iniciar</button>
                            <button type="button" @click="stop()" :disabled="!running" class="px-4 py-2 rounded bg-amber-100 text-amber-800 font-semibold hover:bg-amber-200 disabled:opacity-50">Pausar</button>
                            <button type="button" @click="reset()" class="px-4 py-2 rounded bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200">Reset</button>
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
                            <span class="font-semibold text-gray-800 truncate max-w-[200px]" x-text="taskLabel"></span>
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
                <div class="space-y-2">
                    @forelse($tasks as $task)
                        <div class="flex items-center justify-between rounded border border-gray-100 px-3 py-2 hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full flex items-center justify-center text-xs font-semibold text-white" style="background: {{ $task->project->color ?? '#9ca3af' }}">
                                    {{ collect(explode(' ', trim($task->project->name ?? '?')))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('') ?: '?' }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $task->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $task->project->name ?? 'Sin proyecto' }}</p>
                                </div>
                            </div>
                            <button type="button" class="text-sm text-indigo-600 hover:text-indigo-800" @click="selectedTask = '{{ $task->id }}'">Usar</button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No tienes tareas pendientes asignadas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        function timerApp() {
            return {
                running: false,
                elapsed: 0,
                intervalId: null,
                selectedTask: '',
                maxSeconds: 7200,
                beepEvery: 1200,
                lastBeepAt: 0,
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
                get taskLabel() {
                    const option = document.querySelector(`select[x-model='selectedTask'] option[value='${this.selectedTask}']`);
                    return option ? option.textContent : 'Ninguna';
                },
                start() {
                    if (!this.selectedTask || this.running || this.elapsed >= this.maxSeconds) return;
                    this.running = true;
                    if (this.elapsed === 0) {
                        this.lastBeepAt = 0;
                    }
                    const startedAt = Date.now() - this.elapsed * 1000;
                    this.intervalId = setInterval(() => {
                        this.elapsed = Math.floor((Date.now() - startedAt) / 1000);
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
                stop() {
                    if (this.intervalId) {
                        clearInterval(this.intervalId);
                        this.intervalId = null;
                    }
                    this.running = false;
                },
                reset() {
                    this.stop();
                    this.elapsed = 0;
                    this.lastBeepAt = 0;
                },
                playBeep() {
                    const audio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEAIlYAAESsAAACABAAZGF0YYgAAACAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA=');
                    audio.play().catch(() => {});
                },
            };
        }
    </script>
</x-app-layout>
