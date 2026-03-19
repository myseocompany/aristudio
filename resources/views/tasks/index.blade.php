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

        @keyframes task-created-highlight {
            0% {
                background-color: #d1fae5;
                transform: translateY(-8px);
            }

            100% {
                background-color: transparent;
                transform: translateY(0);
            }
        }

        .task-created-row {
            animation: task-created-highlight 1s ease-out;
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
            tasksBaseUrl: @js(url('tasks')),
            csrfToken: @js(csrf_token()),
            projectsCatalog: @js($projects->map(fn ($project) => [
                'id' => (string) $project->id,
                'name' => $project->name,
                'color' => $project->color ?? '#9ca3af',
                'initials' => collect(explode(' ', trim($project->name)))
                    ->filter()
                    ->map(fn ($word) => mb_substr($word, 0, 1))
                    ->take(2)
                    ->implode('') ?: 'SP',
            ])->values()),
            usersCatalog: @js($users->map(function ($user) {
                $initials = collect(explode(' ', trim($user->name)))
                    ->filter()
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->take(2)
                    ->implode('');
                $avatarPath = $user->image_url
                    ? (str_contains($user->image_url, '/') ? $user->image_url : 'files/users/'.$user->image_url)
                    : null;

                return [
                    'id' => (string) $user->id,
                    'name' => $user->name,
                    'initials' => $initials ?: '?',
                    'avatarUrl' => $avatarPath ? asset('storage/'.$avatarPath) : null,
                ];
            })->values()),
            statusesCatalog: @js($statuses->map(fn ($status) => [
                'id' => (string) $status->id,
                'name' => $status->name,
                'color' => $status->color ?? '#312e81',
                'backgroundColor' => $status->background_color ?? '#eef2ff',
            ])->values()),
            recentTasks: [],
            quickAssignUrl(taskId) {
                return `${this.tasksBaseUrl}/${taskId}/quick-assign`;
            },
            async updateRecentTaskAssignment(task, payload, field) {
                task.rowError = '';
                task.updatingField = field;

                try {
                    const response = await fetch(this.quickAssignUrl(task.id), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': this.csrfToken,
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(data?.message ?? 'No se pudo actualizar.');
                    }
                } catch (error) {
                    task.rowError = error?.message ?? 'No se pudo actualizar.';
                    throw error;
                } finally {
                    task.updatingField = null;
                }
            },
            async updateRecentTaskProject(task, projectId, projectName, projectColor, projectInitials) {
                if (task.updatingField) {
                    return;
                }

                const previous = {
                    projectId: task.projectId,
                    projectName: task.projectName,
                    projectColor: task.projectColor,
                    projectInitials: task.projectInitials,
                };

                task.projectId = String(projectId ?? '');
                task.projectName = projectName;
                task.projectColor = projectColor;
                task.projectInitials = projectInitials;
                task.showProjectPicker = false;

                try {
                    await this.updateRecentTaskAssignment(task, {
                        project_id: task.projectId !== '' ? Number(task.projectId) : null,
                    }, 'project');
                } catch (error) {
                    task.projectId = previous.projectId;
                    task.projectName = previous.projectName;
                    task.projectColor = previous.projectColor;
                    task.projectInitials = previous.projectInitials;
                }
            },
            async updateRecentTaskUser(task, userId, userName, userInitials, userAvatar) {
                if (task.updatingField) {
                    return;
                }

                const previous = {
                    userId: task.userId,
                    userName: task.userName,
                    userInitials: task.userInitials,
                    userAvatar: task.userAvatar,
                };

                task.userId = String(userId ?? '');
                task.userName = userName;
                task.userInitials = userInitials;
                task.userAvatar = userAvatar;
                task.showUserPicker = false;

                try {
                    await this.updateRecentTaskAssignment(task, {
                        user_id: task.userId !== '' ? Number(task.userId) : null,
                    }, 'user');
                } catch (error) {
                    task.userId = previous.userId;
                    task.userName = previous.userName;
                    task.userInitials = previous.userInitials;
                    task.userAvatar = previous.userAvatar;
                }
            },
            async updateRecentTaskStatus(task, statusId, statusName, statusColor, statusBackgroundColor) {
                if (task.updatingField) {
                    return;
                }

                const previous = {
                    statusId: task.statusId,
                    statusName: task.statusName,
                    statusColor: task.statusColor,
                    statusBackgroundColor: task.statusBackgroundColor,
                };

                task.statusId = String(statusId ?? '');
                task.statusName = statusName;
                task.statusColor = statusColor;
                task.statusBackgroundColor = statusBackgroundColor;

                try {
                    await this.updateRecentTaskAssignment(task, {
                        status_id: task.statusId !== '' ? Number(task.statusId) : null,
                    }, 'status');
                } catch (error) {
                    task.statusId = previous.statusId;
                    task.statusName = previous.statusName;
                    task.statusColor = previous.statusColor;
                    task.statusBackgroundColor = previous.statusBackgroundColor;
                }
            },
            async updateRecentTaskBillable(task, valueGenerated) {
                if (task.updatingField) {
                    return;
                }

                const previous = !!task.valueGenerated;
                task.valueGenerated = !!valueGenerated;

                try {
                    await this.updateRecentTaskAssignment(task, {
                        value_generated: task.valueGenerated,
                    }, 'value_generated');
                } catch (error) {
                    task.valueGenerated = previous;
                }
            },
            handleTaskCreated(task) {
                if (!task || !task.id) {
                    return;
                }

                this.recentTasks.unshift({
                    ...task,
                    projectId: String(task.projectId ?? ''),
                    statusId: String(task.statusId ?? task.status_id ?? ''),
                    userId: String(task.userId ?? ''),
                    valueGenerated: task.valueGenerated ?? true,
                    showProjectPicker: false,
                    showUserPicker: false,
                    updatingField: null,
                    rowError: '',
                    showUrl: task.showUrl ?? `${this.tasksBaseUrl}/${task.id}?sidebar=1`,
                    rowKey: `recent-${task.id}-${Date.now()}`,
                    isFresh: true,
                });

                const createdTaskId = task.id;
                window.setTimeout(() => {
                    const recentTask = this.recentTasks.find((item) => item.id === createdTaskId);
                    if (recentTask) {
                        recentTask.isFresh = false;
                    }
                }, 1000);
            },
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
        }" @task-created="handleTaskCreated($event.detail.task)">
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
                            <input type="hidden" name="value_generated" value="{{ $filters['value_generated'] ? 1 : 0 }}">
                        </form>
                        <button type="button" @click="showFilters = !showFilters" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded border border-gray-200 text-gray-700">
                            <span x-show="!showFilters">Mostrar filtros</span>
                            <span x-show="showFilters">Ocultar filtros</span>
                        </button>
                        <a href="{{ route('tasks.export', request()->query()) }}" class="px-3 py-2 text-sm bg-emerald-600 hover:bg-emerald-500 rounded border border-emerald-600 text-white">
                            Descargar CSV
                        </a>
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
                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="value_generated" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked($filters['value_generated'])>
                                Solo generan valor
                            </label>
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
                                <th class="px-4 py-3 text-center">Genera valor</th>
                                <th class="px-4 py-3 text-center w-16">Resp.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if($defaultStatusId)
                                @php
                                    $inlineQuickProjectId = (string) old('project_id', '');
                                    $inlineQuickProjectName = 'Sin proyecto';
                                    $inlineQuickProjectColor = '#9ca3af';
                                    $inlineQuickProjectInitials = 'SP';
                                    if ($inlineQuickProjectId !== '') {
                                        $inlineQuickProject = $projects->firstWhere('id', (int) $inlineQuickProjectId);
                                        $inlineQuickProjectName = $inlineQuickProject?->name ?? 'Sin proyecto';
                                        $inlineQuickProjectColor = $inlineQuickProject?->color ?? '#9ca3af';
                                        $inlineQuickProjectInitials = collect(explode(' ', trim($inlineQuickProjectName)))
                                            ->filter()
                                            ->map(fn ($word) => mb_substr($word, 0, 1))
                                            ->take(2)
                                            ->implode('') ?: 'SP';
                                    }
                                    $inlineQuickProjectsCatalog = $projects
                                        ->map(fn ($project) => [
                                            'id' => (string) $project->id,
                                            'name' => $project->name,
                                            'color' => $project->color ?? '#9ca3af',
                                            'initials' => collect(explode(' ', trim($project->name)))
                                                ->filter()
                                                ->map(fn ($word) => mb_substr($word, 0, 1))
                                                ->take(2)
                                                ->implode('') ?: 'SP',
                                        ])
                                        ->values();
                                    $inlineQuickUserId = (string) old('user_id', (string) auth()->id());
                                    $inlineQuickUser = $users->firstWhere('id', (int) $inlineQuickUserId);
                                    $inlineQuickUserName = $inlineQuickUser?->name ?? 'Sin asignar';
                                    $inlineQuickUserInitials = $inlineQuickUser
                                        ? collect(explode(' ', trim($inlineQuickUser->name)))
                                            ->filter()
                                            ->map(fn ($word) => mb_substr($word, 0, 1))
                                            ->take(2)
                                            ->implode('')
                                        : 'SA';
                                    $inlineQuickUserAvatar = null;
                                    if ($inlineQuickUser?->image_url) {
                                        $inlineQuickUserImagePath = str_contains($inlineQuickUser->image_url, '/')
                                            ? $inlineQuickUser->image_url
                                            : 'files/users/'.$inlineQuickUser->image_url;
                                        $inlineQuickUserAvatar = asset('storage/'.$inlineQuickUserImagePath);
                                    }
                                    $inlineQuickUsersCatalog = $users
                                        ->map(function ($user) {
                                            $initials = collect(explode(' ', trim($user->name)))
                                                ->filter()
                                                ->map(fn ($part) => mb_substr($part, 0, 1))
                                                ->take(2)
                                                ->implode('');
                                            $avatarPath = $user->image_url
                                                ? (str_contains($user->image_url, '/') ? $user->image_url : 'files/users/'.$user->image_url)
                                                : null;

                                            return [
                                                'id' => (string) $user->id,
                                                'name' => $user->name,
                                                'initials' => $initials ?: '?',
                                                'avatarUrl' => $avatarPath ? asset('storage/'.$avatarPath) : null,
                                            ];
                                        })
                                        ->values();
                                @endphp
                                <tr
                                    class="bg-white"
                                    x-data="{
                                        showProjectPicker: false,
                                        selectedProjectId: @js($inlineQuickProjectId),
                                        selectedProjectName: @js($inlineQuickProjectName),
                                        selectedProjectColor: @js($inlineQuickProjectColor),
                                        selectedProjectInitials: @js($inlineQuickProjectInitials),
                                        projectsCatalog: @js($inlineQuickProjectsCatalog),
                                        defaultStatusId: @js((string) $defaultStatusId),
                                        statusesCatalog: @js($statuses->mapWithKeys(fn ($status) => [(string) $status->id => [
                                            'name' => $status->name,
                                            'color' => $status->color ?? '#312e81',
                                            'backgroundColor' => $status->background_color ?? '#eef2ff',
                                        ]])->all()),
                                        showUserPicker: false,
                                        selectedUserId: @js($inlineQuickUserId),
                                        selectedUserName: @js($inlineQuickUserName),
                                        selectedUserInitials: @js($inlineQuickUserInitials),
                                        selectedUserAvatar: @js($inlineQuickUserAvatar),
                                        usersCatalog: @js($inlineQuickUsersCatalog),
                                        isSubmitting: false,
                                        inlineError: '',
                                        storageProjectKey: 'tasks.inline.quick.project_id',
                                        storageUserKey: 'tasks.inline.quick.user_id',
                                        init() {
                                            const rememberedProjectId = window.localStorage.getItem(this.storageProjectKey);
                                            const rememberedUserId = window.localStorage.getItem(this.storageUserKey);

                                            if (rememberedProjectId !== null) {
                                                this.setSelectedProject(rememberedProjectId, false);
                                            }

                                            if (rememberedUserId !== null) {
                                                this.setSelectedUser(rememberedUserId, false);
                                            }
                                        },
                                        setSelectedProject(projectId, persist = true) {
                                            const value = String(projectId ?? '');
                                            this.selectedProjectId = value;
                                            const project = this.projectsCatalog.find((item) => item.id === value);
                                            this.selectedProjectName = project ? project.name : 'Sin proyecto';
                                            this.selectedProjectColor = project ? (project.color ?? '#9ca3af') : '#9ca3af';
                                            this.selectedProjectInitials = project ? (project.initials ?? 'SP') : 'SP';

                                            if (persist) {
                                                window.localStorage.setItem(this.storageProjectKey, value);
                                            }
                                        },
                                        setSelectedUser(userId, persist = true) {
                                            const value = String(userId ?? '');
                                            this.selectedUserId = value;
                                            const user = this.usersCatalog.find((item) => item.id === value);
                                            this.selectedUserName = user ? user.name : 'Sin asignar';
                                            this.selectedUserInitials = user ? user.initials : 'SA';
                                            this.selectedUserAvatar = user ? (user.avatarUrl ?? null) : null;

                                            if (persist) {
                                                window.localStorage.setItem(this.storageUserKey, value);
                                            }
                                        },
                                        persistQuickPreferences() {
                                            window.localStorage.setItem(this.storageProjectKey, String(this.selectedProjectId ?? ''));
                                            window.localStorage.setItem(this.storageUserKey, String(this.selectedUserId ?? ''));
                                        },
                                        async submitQuickTask() {
                                            if (this.isSubmitting) {
                                                return;
                                            }

                                            this.inlineError = '';

                                            const input = this.$refs.inlineQuickName;
                                            const name = (input?.value ?? '').trim();

                                            if (!name) {
                                                this.inlineError = 'Escribe una tarea.';
                                                input?.focus();

                                                return;
                                            }

                                            const formData = new FormData(this.$refs.inlineQuickForm);
                                            formData.set('name', name);
                                            this.persistQuickPreferences();
                                            this.isSubmitting = true;

                                            try {
                                                const response = await fetch(this.$refs.inlineQuickForm.action, {
                                                    method: 'POST',
                                                    headers: {
                                                        'Accept': 'application/json',
                                                        'X-Requested-With': 'XMLHttpRequest',
                                                    },
                                                    body: formData,
                                                });
                                                const payload = await response.json().catch(() => ({}));

                                                if (!response.ok) {
                                                    this.inlineError = payload?.errors?.name?.[0]
                                                        ?? payload?.message
                                                        ?? 'No se pudo crear la tarea.';

                                                    return;
                                                }

                                                const createdTask = payload?.task ?? {};
                                                const selectedStatus = this.statusesCatalog[String(this.defaultStatusId)] ?? {
                                                    name: 'Pendiente',
                                                    color: '#312e81',
                                                    backgroundColor: '#eef2ff',
                                                };

                                                this.$dispatch('task-created', {
                                                    task: {
                                                        id: createdTask.id,
                                                        name: createdTask.name ?? name,
                                                        projectId: this.selectedProjectId ?? '',
                                                        projectName: this.selectedProjectName ?? 'Sin proyecto',
                                                        projectColor: this.selectedProjectColor ?? '#9ca3af',
                                                        projectInitials: this.selectedProjectInitials ?? 'SP',
                                                        statusName: selectedStatus.name,
                                                        statusColor: selectedStatus.color,
                                                        statusBackgroundColor: selectedStatus.backgroundColor,
                                                        userId: this.selectedUserId ?? '',
                                                        userName: this.selectedUserName ?? 'Sin asignar',
                                                        userInitials: this.selectedUserId ? this.selectedUserInitials : '—',
                                                        userAvatar: this.selectedUserId ? (this.selectedUserAvatar ?? null) : null,
                                                        valueGenerated: createdTask.value_generated ?? true,
                                                        showUrl: `{{ url('tasks') }}/${createdTask.id}?sidebar=1`,
                                                    },
                                                });

                                                input.value = '';
                                                input.focus();
                                            } catch (error) {
                                                this.inlineError = 'No se pudo crear la tarea.';
                                            } finally {
                                                this.isSubmitting = false;
                                            }
                                        },
                                    }"
                                    x-init="init()"
                                    @click.outside="showProjectPicker = false; showUserPicker = false"
                                >
                                    <td class="px-4 py-3">
                                        <form
                                            action="{{ route('tasks.store') }}"
                                            method="POST"
                                            x-ref="inlineQuickForm"
                                            @submit.prevent="submitQuickTask"
                                            class="flex items-center gap-3"
                                        >
                                            @csrf
                                            <input type="hidden" name="status_id" value="{{ $defaultStatusId }}">
                                            <input type="hidden" name="user_id" :value="selectedUserId">
                                            <input type="hidden" name="project_id" :value="selectedProjectId">
                                            <input type="hidden" name="value_generated" value="1">
                                            <div class="relative shrink-0">
                                                <button
                                                    id="tasks-inline-quick-project-toggle"
                                                    type="button"
                                                    class="h-10 w-10 rounded-full text-white hover:opacity-90 flex items-center justify-center text-xs font-semibold"
                                                    :style="`background:${selectedProjectColor}`"
                                                    @click="showProjectPicker = !showProjectPicker"
                                                    title="Escoger proyecto"
                                                >
                                                    <span x-text="selectedProjectInitials"></span>
                                                </button>
                                                <div
                                                    x-cloak
                                                    x-show="showProjectPicker"
                                                    x-transition
                                                    class="absolute left-0 top-12 z-20 w-72 max-h-72 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg p-1"
                                                >
                                                    <button
                                                        type="button"
                                                        class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-700"
                                                        @click="setSelectedProject(''); showProjectPicker = false"
                                                    >
                                                        Sin proyecto
                                                    </button>
                                                    @foreach($projects as $project)
                                                        <button
                                                            type="button"
                                                            class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-800 flex items-center gap-2"
                                                            @click="setSelectedProject('{{ $project->id }}'); showProjectPicker = false"
                                                        >
                                                            <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $project->color ?? '#9ca3af' }}"></span>
                                                            <span class="truncate">{{ $project->name }}</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <input
                                                id="tasks-inline-quick-name"
                                                name="name"
                                                type="text"
                                                x-ref="inlineQuickName"
                                                class="flex-1 border-0 bg-transparent text-sm text-gray-900 placeholder:text-gray-400 focus:ring-0"
                                                placeholder="Agregar una tarea..."
                                                :disabled="isSubmitting"
                                                required
                                                autofocus
                                            >
                                        </form>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold"
                                            :style="`background:${statusesCatalog[String(defaultStatusId)]?.backgroundColor ?? '#eef2ff'}; color:${statusesCatalog[String(defaultStatusId)]?.color ?? '#312e81'}`"
                                            x-text="statusesCatalog[String(defaultStatusId)]?.name ?? 'Pendiente'"
                                        ></span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 whitespace-nowrap">Sin fecha</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="text-xs"
                                            :class="inlineError ? 'text-red-600' : (isSubmitting ? 'text-blue-600' : 'text-gray-400')"
                                            x-text="inlineError ? inlineError : (isSubmitting ? 'Guardando...' : '')"
                                        ></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">Sí</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-flex">
                                            <button
                                                id="tasks-inline-quick-user-toggle"
                                                type="button"
                                                class="h-10 w-10 overflow-hidden rounded-full ring-2 ring-gray-100 bg-white flex items-center justify-center"
                                                @click="showUserPicker = !showUserPicker"
                                                :title="selectedUserName"
                                            >
                                                <template x-if="selectedUserAvatar">
                                                    <img :src="selectedUserAvatar" :alt="selectedUserName" class="h-10 w-10 object-cover">
                                                </template>
                                                <template x-if="!selectedUserAvatar">
                                                    <span class="text-xs font-semibold text-gray-600" x-text="selectedUserInitials"></span>
                                                </template>
                                            </button>
                                            <div
                                                x-cloak
                                                x-show="showUserPicker"
                                                x-transition
                                                class="absolute right-0 top-12 z-20 w-72 max-h-72 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg p-1"
                                            >
                                                <button
                                                    type="button"
                                                    class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-700"
                                                    @click="setSelectedUser(''); showUserPicker = false"
                                                >
                                                    Sin asignar
                                                </button>
                                                @foreach($users as $user)
                                                    @php
                                                        $userInitials = collect(explode(' ', trim($user->name)))
                                                            ->filter()
                                                            ->map(fn ($part) => mb_substr($part, 0, 1))
                                                            ->take(2)
                                                            ->implode('');
                                                        $userAvatarPath = $user->image_url
                                                            ? (str_contains($user->image_url, '/') ? $user->image_url : 'files/users/'.$user->image_url)
                                                            : null;
                                                    @endphp
                                                    <button
                                                        type="button"
                                                        class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-800 flex items-center gap-2"
                                                        @click="setSelectedUser('{{ $user->id }}'); showUserPicker = false"
                                                    >
                                                        @if($userAvatarPath)
                                                            <img src="{{ asset('storage/'.$userAvatarPath) }}" alt="{{ $user->name }}" class="h-6 w-6 rounded-full object-cover">
                                                        @else
                                                            <span class="h-6 w-6 rounded-full bg-gray-100 text-gray-700 flex items-center justify-center text-xs font-semibold">{{ $userInitials ?: '?' }}</span>
                                                        @endif
                                                        <span class="truncate">{{ $user->name }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr class="bg-white">
                                    <td colspan="6" class="px-4 py-2">
                                        <p class="text-sm text-amber-700">No hay estados activos para crear tareas rápidas.</p>
                                    </td>
                                </tr>
                            @endif
                            <template x-for="recentTask in recentTasks" :key="recentTask.rowKey">
                                <tr :class="recentTask.isFresh ? 'task-created-row' : 'hover:bg-gray-50'">
                                    <td class="px-4 py-3">
                                        <div class="flex items-start gap-3">
                                            <div class="relative shrink-0" @click.outside="recentTask.showProjectPicker = false">
                                                <button
                                                    type="button"
                                                    class="h-10 w-10 rounded-full text-white flex items-center justify-center text-xs font-semibold hover:opacity-90"
                                                    :style="`background:${recentTask.projectColor ?? '#9ca3af'}`"
                                                    @click.stop="recentTask.showProjectPicker = !recentTask.showProjectPicker"
                                                    :disabled="recentTask.updatingField !== null"
                                                    :title="recentTask.projectName"
                                                >
                                                    <span x-text="recentTask.projectInitials ?? 'SP'"></span>
                                                </button>
                                                <div
                                                    x-cloak
                                                    x-show="recentTask.showProjectPicker"
                                                    x-transition
                                                    class="absolute left-0 top-12 z-20 w-72 max-h-72 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg p-1"
                                                >
                                                    <button
                                                        type="button"
                                                        class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-700"
                                                        @click="updateRecentTaskProject(recentTask, '', 'Sin proyecto', '#9ca3af', 'SP')"
                                                    >
                                                        Sin proyecto
                                                    </button>
                                                    <template x-for="projectOption in projectsCatalog" :key="`recent-project-${recentTask.id}-${projectOption.id}`">
                                                        <button
                                                            type="button"
                                                            class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-800 flex items-center gap-2"
                                                            @click="updateRecentTaskProject(recentTask, projectOption.id, projectOption.name, projectOption.color ?? '#9ca3af', projectOption.initials ?? 'SP')"
                                                        >
                                                            <span class="h-2.5 w-2.5 rounded-full" :style="`background:${projectOption.color ?? '#9ca3af'}`"></span>
                                                            <span class="truncate" x-text="projectOption.name"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="font-semibold text-gray-900 hover:underline text-left" @click="loadPanel(recentTask.showUrl)">
                                                    <span x-text="recentTask.name"></span>
                                                </button>
                                                <p class="text-xs text-gray-500" x-text="recentTask.projectName"></p>
                                                <p class="text-xs text-red-600 mt-1" x-show="recentTask.rowError" x-text="recentTask.rowError"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <label class="sr-only" :for="`recent-task-status-${recentTask.id}`">Estado</label>
                                        <div class="relative">
                                            <select
                                                :id="`recent-task-status-${recentTask.id}`"
                                                class="w-full min-w-[9rem] rounded-full border-0 py-1 pl-3 pr-8 text-xs font-semibold focus:ring-2 focus:ring-indigo-500"
                                                :style="`background:${recentTask.statusBackgroundColor ?? '#eef2ff'}; color:${recentTask.statusColor ?? '#312e81'}`"
                                                x-model="recentTask.statusId"
                                                :disabled="recentTask.updatingField !== null"
                                                @change="updateRecentTaskStatus(
                                                    recentTask,
                                                    $event.target.value,
                                                    $event.target.selectedOptions[0]?.dataset.name ?? '',
                                                    $event.target.selectedOptions[0]?.dataset.color ?? '#312e81',
                                                    $event.target.selectedOptions[0]?.dataset.backgroundColor ?? '#eef2ff'
                                                )"
                                            >
                                                <template x-for="statusOption in statusesCatalog" :key="`recent-status-${recentTask.id}-${statusOption.id}`">
                                                    <option
                                                        :value="statusOption.id"
                                                        :selected="String(recentTask.statusId) === String(statusOption.id)"
                                                        :data-name="statusOption.name"
                                                        :data-color="statusOption.color"
                                                        :data-background-color="statusOption.backgroundColor"
                                                        x-text="statusOption.name"
                                                    ></option>
                                                </template>
                                            </select>
                                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[10px] text-current">▾</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 whitespace-nowrap">Sin fecha</td>
                                    <td class="px-4 py-3 text-xs font-semibold text-emerald-700">Recién creada</td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            type="button"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200"
                                            :class="recentTask.valueGenerated ? 'bg-emerald-500' : 'bg-gray-300'"
                                            :disabled="recentTask.updatingField !== null"
                                            @click="updateRecentTaskBillable(recentTask, !recentTask.valueGenerated)"
                                        >
                                            <span
                                                class="inline-block h-5 w-5 transform rounded-full bg-white transition duration-200"
                                                :class="recentTask.valueGenerated ? 'translate-x-5' : 'translate-x-1'"
                                            ></span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-flex" @click.outside="recentTask.showUserPicker = false">
                                            <button
                                                type="button"
                                                class="h-10 w-10 overflow-hidden rounded-full border border-gray-300 bg-white hover:border-blue-400 flex items-center justify-center"
                                                @click.stop="recentTask.showUserPicker = !recentTask.showUserPicker"
                                                :disabled="recentTask.updatingField !== null"
                                                :title="recentTask.userName"
                                            >
                                                <template x-if="recentTask.userAvatar">
                                                    <img :src="recentTask.userAvatar" :alt="recentTask.userName" class="h-10 w-10 object-cover">
                                                </template>
                                                <template x-if="!recentTask.userAvatar">
                                                    <span class="text-xs font-semibold text-gray-600" x-text="recentTask.userInitials ?? '—'"></span>
                                                </template>
                                            </button>
                                            <div
                                                x-cloak
                                                x-show="recentTask.showUserPicker"
                                                x-transition
                                                class="absolute right-0 top-12 z-20 w-72 max-h-72 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg p-1"
                                            >
                                                <button
                                                    type="button"
                                                    class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-700"
                                                    @click="updateRecentTaskUser(recentTask, '', 'Sin asignar', 'SA', null)"
                                                >
                                                    Sin asignar
                                                </button>
                                                <template x-for="userOption in usersCatalog" :key="`recent-user-${recentTask.id}-${userOption.id}`">
                                                    <button
                                                        type="button"
                                                        class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-800 flex items-center gap-2"
                                                        @click="updateRecentTaskUser(recentTask, userOption.id, userOption.name, userOption.initials ?? '?', userOption.avatarUrl ?? null)"
                                                    >
                                                        <template x-if="userOption.avatarUrl">
                                                            <img :src="userOption.avatarUrl" :alt="userOption.name" class="h-6 w-6 rounded-full object-cover">
                                                        </template>
                                                        <template x-if="!userOption.avatarUrl">
                                                            <span class="h-6 w-6 rounded-full bg-gray-100 text-gray-700 flex items-center justify-center text-xs font-semibold" x-text="userOption.initials ?? '?'"></span>
                                                        </template>
                                                        <span class="truncate" x-text="userOption.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
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
                                        : 'SP';
                                    $projectColor = $project->color ?? '#9ca3af';
                                    $imgPath = $user?->image_url
                                        ? (str_contains($user->image_url, '/') ? $user->image_url : 'files/users/'.$user->image_url)
                                        : null;
                                    $initials = collect(explode(' ', trim($user->name ?? '')))
                                        ->filter()
                                        ->map(fn ($part) => mb_substr($part, 0, 1))
                                        ->take(2)
                                        ->implode('');
                                    $selectedUserAvatarUrl = $imgPath ? asset('storage/'.$imgPath) : null;
                                    $pointsUsed = (float) ($task->points ?? 0);
                                    $maxPoints = 2;
                                    $progressPct = max(0, min(1, $pointsUsed / $maxPoints)) * 100;
                                @endphp
                                <tr
                                    class="hover:bg-gray-50"
                                    x-data="{
                                        showProjectPicker: false,
                                        showUserPicker: false,
                                        selectedProjectId: @js((string) ($task->project_id ?? '')),
                                        selectedProjectName: @js($project?->name ?? 'Sin proyecto'),
                                        selectedProjectColor: @js($projectColor),
                                        selectedProjectInitials: @js($projectInitials ?: 'SP'),
                                        selectedUserId: @js((string) ($task->user_id ?? '')),
                                        selectedUserName: @js($user?->name ?? 'Sin asignar'),
                                        selectedUserInitials: @js($initials ?: 'SA'),
                                        selectedUserAvatar: @js($selectedUserAvatarUrl),
                                        selectedStatusId: @js((string) ($task->status_id ?? '')),
                                        selectedStatusName: @js($status?->name ?? 'N/A'),
                                        selectedStatusColor: @js($status?->color ?? '#312e81'),
                                        selectedStatusBackgroundColor: @js($status?->background_color ?? '#eef2ff'),
                                        valueGenerated: @js((bool) $task->value_generated),
                                        updatingField: null,
                                        rowError: '',
                                        async persistAssignment(payload, field) {
                                            this.rowError = '';
                                            this.updatingField = field;

                                            try {
                                                const response = await fetch('{{ route('tasks.quick-assign', $task) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'Accept': 'application/json',
                                                        'X-Requested-With': 'XMLHttpRequest',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    },
                                                    body: JSON.stringify(payload),
                                                });
                                                const data = await response.json().catch(() => ({}));

                                                if (!response.ok) {
                                                    throw new Error(data?.message ?? 'No se pudo actualizar.');
                                                }
                                            } catch (error) {
                                                this.rowError = error?.message ?? 'No se pudo actualizar.';
                                                throw error;
                                            } finally {
                                                this.updatingField = null;
                                            }
                                        },
                                        async setProject(projectId, projectName, projectColor, projectInitials) {
                                            if (this.updatingField) {
                                                return;
                                            }

                                            const previous = {
                                                id: this.selectedProjectId,
                                                name: this.selectedProjectName,
                                                color: this.selectedProjectColor,
                                                initials: this.selectedProjectInitials,
                                            };

                                            this.selectedProjectId = String(projectId ?? '');
                                            this.selectedProjectName = projectName;
                                            this.selectedProjectColor = projectColor;
                                            this.selectedProjectInitials = projectInitials;
                                            this.showProjectPicker = false;

                                            try {
                                                await this.persistAssignment({
                                                    project_id: this.selectedProjectId !== '' ? Number(this.selectedProjectId) : null,
                                                }, 'project');
                                            } catch (error) {
                                                this.selectedProjectId = previous.id;
                                                this.selectedProjectName = previous.name;
                                                this.selectedProjectColor = previous.color;
                                                this.selectedProjectInitials = previous.initials;
                                            }
                                        },
                                        async setUser(userId, userName, userInitials, userAvatar) {
                                            if (this.updatingField) {
                                                return;
                                            }

                                            const previous = {
                                                id: this.selectedUserId,
                                                name: this.selectedUserName,
                                                initials: this.selectedUserInitials,
                                                avatar: this.selectedUserAvatar,
                                            };

                                            this.selectedUserId = String(userId ?? '');
                                            this.selectedUserName = userName;
                                            this.selectedUserInitials = userInitials;
                                            this.selectedUserAvatar = userAvatar;
                                            this.showUserPicker = false;

                                            try {
                                                await this.persistAssignment({
                                                    user_id: this.selectedUserId !== '' ? Number(this.selectedUserId) : null,
                                                }, 'user');
                                            } catch (error) {
                                                this.selectedUserId = previous.id;
                                                this.selectedUserName = previous.name;
                                                this.selectedUserInitials = previous.initials;
                                                this.selectedUserAvatar = previous.avatar;
                                            }
                                        },
                                        async setStatus(statusId, statusName, statusColor, statusBackgroundColor) {
                                            if (this.updatingField) {
                                                return;
                                            }

                                            const previous = {
                                                id: this.selectedStatusId,
                                                name: this.selectedStatusName,
                                                color: this.selectedStatusColor,
                                                backgroundColor: this.selectedStatusBackgroundColor,
                                            };

                                            this.selectedStatusId = String(statusId ?? '');
                                            this.selectedStatusName = statusName;
                                            this.selectedStatusColor = statusColor;
                                            this.selectedStatusBackgroundColor = statusBackgroundColor;

                                            try {
                                                await this.persistAssignment({
                                                    status_id: this.selectedStatusId !== '' ? Number(this.selectedStatusId) : null,
                                                }, 'status');
                                            } catch (error) {
                                                this.selectedStatusId = previous.id;
                                                this.selectedStatusName = previous.name;
                                                this.selectedStatusColor = previous.color;
                                                this.selectedStatusBackgroundColor = previous.backgroundColor;
                                            }
                                        },
                                        async setValueGenerated(valueGenerated) {
                                            if (this.updatingField) {
                                                return;
                                            }

                                            const previous = !!this.valueGenerated;
                                            this.valueGenerated = !!valueGenerated;

                                            try {
                                                await this.persistAssignment({
                                                    value_generated: this.valueGenerated,
                                                }, 'value_generated');
                                            } catch (error) {
                                                this.valueGenerated = previous;
                                            }
                                        },
                                    }"
                                    @click.outside="showProjectPicker = false; showUserPicker = false"
                                >
                                    <td class="px-4 py-3">
                                        <div class="flex items-start gap-3">
                                            <div class="relative shrink-0">
                                                <button
                                                    type="button"
                                                    class="h-10 w-10 rounded-full text-white flex items-center justify-center text-xs font-semibold hover:opacity-90"
                                                    data-task-row-project-toggle="{{ $task->id }}"
                                                    :style="`background:${selectedProjectColor}`"
                                                    @click.stop="showProjectPicker = !showProjectPicker"
                                                    :disabled="updatingField !== null"
                                                    :title="selectedProjectName"
                                                >
                                                    <span x-text="selectedProjectInitials"></span>
                                                </button>
                                                <div
                                                    x-cloak
                                                    x-show="showProjectPicker"
                                                    x-transition
                                                    class="absolute left-0 top-12 z-20 w-72 max-h-72 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg p-1"
                                                >
                                                    <button
                                                        type="button"
                                                        class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-700"
                                                        @click="setProject('', 'Sin proyecto', '#9ca3af', 'SP')"
                                                    >
                                                        Sin proyecto
                                                    </button>
                                                    @foreach($projects as $projectOption)
                                                        @php
                                                            $projectOptionInitials = collect(explode(' ', trim($projectOption->name)))
                                                                ->filter()
                                                                ->map(fn ($part) => mb_substr($part, 0, 1))
                                                                ->take(2)
                                                                ->implode('') ?: 'SP';
                                                        @endphp
                                                        <button
                                                            type="button"
                                                            class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-800 flex items-center gap-2"
                                                            @click="setProject('{{ $projectOption->id }}', @js($projectOption->name), @js($projectOption->color ?? '#9ca3af'), @js($projectOptionInitials))"
                                                        >
                                                            <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $projectOption->color ?? '#9ca3af' }}"></span>
                                                            <span class="truncate">{{ $projectOption->name }}</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="font-semibold text-gray-900 hover:underline text-left" @click="loadPanel('{{ route('tasks.show', $task) }}?sidebar=1')">
                                                    {{ $task->name }}
                                                </button>
                                                <div class="text-xs text-gray-500 leading-relaxed">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($task->description ?? ''), 120) ?: 'Sin descripción' }}
                                                </div>
                                                <p class="text-xs text-red-600 mt-1" x-show="rowError" x-text="rowError"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <label class="sr-only" for="task-status-{{ $task->id }}">Estado</label>
                                        <div class="relative">
                                            <select
                                                id="task-status-{{ $task->id }}"
                                                class="w-full min-w-[9rem] rounded-full border-0 py-1 pl-3 pr-8 text-xs font-semibold focus:ring-2 focus:ring-indigo-500"
                                                data-task-row-status-select="{{ $task->id }}"
                                                :style="`background:${selectedStatusBackgroundColor}; color:${selectedStatusColor}`"
                                                x-model="selectedStatusId"
                                                :disabled="updatingField !== null"
                                                @change="setStatus(
                                                    $event.target.value,
                                                    $event.target.selectedOptions[0]?.dataset.name ?? '',
                                                    $event.target.selectedOptions[0]?.dataset.color ?? '#312e81',
                                                    $event.target.selectedOptions[0]?.dataset.backgroundColor ?? '#eef2ff'
                                                )"
                                            >
                                                @foreach($statuses as $statusOption)
                                                    <option
                                                        value="{{ $statusOption->id }}"
                                                        @selected((string) ($task->status_id ?? '') === (string) $statusOption->id)
                                                        data-name="{{ $statusOption->name }}"
                                                        data-color="{{ $statusOption->color ?? '#312e81' }}"
                                                        data-background-color="{{ $statusOption->background_color ?? '#eef2ff' }}"
                                                    >
                                                        {{ $statusOption->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[10px] text-current">▾</span>
                                        </div>
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
                                        <button
                                            type="button"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200"
                                            :class="valueGenerated ? 'bg-emerald-500' : 'bg-gray-300'"
                                            :disabled="updatingField !== null"
                                            @click="setValueGenerated(!valueGenerated)"
                                        >
                                            <span
                                                class="inline-block h-5 w-5 transform rounded-full bg-white transition duration-200"
                                                :class="valueGenerated ? 'translate-x-5' : 'translate-x-1'"
                                            ></span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-flex">
                                            <button
                                                type="button"
                                                class="h-10 w-10 overflow-hidden rounded-full border border-gray-300 bg-white hover:border-blue-400 flex items-center justify-center"
                                                data-task-row-user-toggle="{{ $task->id }}"
                                                @click.stop="showUserPicker = !showUserPicker"
                                                :disabled="updatingField !== null"
                                                :title="selectedUserName"
                                            >
                                                <template x-if="selectedUserAvatar">
                                                    <img :src="selectedUserAvatar" :alt="selectedUserName" class="h-10 w-10 object-cover">
                                                </template>
                                                <template x-if="!selectedUserAvatar">
                                                    <span class="text-xs font-semibold text-gray-600" x-text="selectedUserId ? selectedUserInitials : '—'"></span>
                                                </template>
                                            </button>
                                            <div
                                                x-cloak
                                                x-show="showUserPicker"
                                                x-transition
                                                class="absolute right-0 top-12 z-20 w-72 max-h-72 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg p-1"
                                            >
                                                <button
                                                    type="button"
                                                    class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-700"
                                                    @click="setUser('', 'Sin asignar', 'SA', null)"
                                                >
                                                    Sin asignar
                                                </button>
                                                @foreach($users as $userOption)
                                                    @php
                                                        $userOptionInitials = collect(explode(' ', trim($userOption->name)))
                                                            ->filter()
                                                            ->map(fn ($part) => mb_substr($part, 0, 1))
                                                            ->take(2)
                                                            ->implode('') ?: '?';
                                                        $userOptionAvatarPath = $userOption->image_url
                                                            ? (str_contains($userOption->image_url, '/') ? $userOption->image_url : 'files/users/'.$userOption->image_url)
                                                            : null;
                                                        $userOptionAvatarUrl = $userOptionAvatarPath ? asset('storage/'.$userOptionAvatarPath) : null;
                                                    @endphp
                                                    <button
                                                        type="button"
                                                        class="w-full rounded px-3 py-2 text-left text-sm hover:bg-gray-100 text-gray-800 flex items-center gap-2"
                                                        @click="setUser('{{ $userOption->id }}', @js($userOption->name), @js($userOptionInitials), @js($userOptionAvatarUrl))"
                                                    >
                                                        @if($userOptionAvatarUrl)
                                                            <img src="{{ $userOptionAvatarUrl }}" alt="{{ $userOption->name }}" class="h-6 w-6 rounded-full object-cover">
                                                        @else
                                                            <span class="h-6 w-6 rounded-full bg-gray-100 text-gray-700 flex items-center justify-center text-xs font-semibold">{{ $userOptionInitials }}</span>
                                                        @endif
                                                        <span class="truncate">{{ $userOption->name }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr x-show="recentTasks.length === 0">
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No hay tareas que coincidan con el filtro.</td>
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
                        <div
                            class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm"
                            x-data="{
                                selectedStatusId: @js((string) ($task->status_id ?? '')),
                                selectedStatusColor: @js($status?->color ?? '#312e81'),
                                selectedStatusBackgroundColor: @js($status?->background_color ?? '#eef2ff'),
                                valueGenerated: @js((bool) $task->value_generated),
                                isUpdating: false,
                                rowError: '',
                                async setStatus(statusId, statusColor, statusBackgroundColor) {
                                    if (this.isUpdating) {
                                        return;
                                    }

                                    const previous = {
                                        id: this.selectedStatusId,
                                        color: this.selectedStatusColor,
                                        backgroundColor: this.selectedStatusBackgroundColor,
                                    };

                                    this.selectedStatusId = String(statusId ?? '');
                                    this.selectedStatusColor = statusColor;
                                    this.selectedStatusBackgroundColor = statusBackgroundColor;
                                    this.isUpdating = true;
                                    this.rowError = '';

                                    try {
                                        const response = await fetch('{{ route('tasks.quick-assign', $task) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            },
                                            body: JSON.stringify({
                                                status_id: Number(this.selectedStatusId),
                                            }),
                                        });
                                        const data = await response.json().catch(() => ({}));

                                        if (!response.ok) {
                                            throw new Error(data?.message ?? 'No se pudo actualizar.');
                                        }
                                    } catch (error) {
                                        this.selectedStatusId = previous.id;
                                        this.selectedStatusColor = previous.color;
                                        this.selectedStatusBackgroundColor = previous.backgroundColor;
                                        this.rowError = error?.message ?? 'No se pudo actualizar.';
                                    } finally {
                                        this.isUpdating = false;
                                    }
                                },
                                async toggleValueGenerated() {
                                    if (this.isUpdating) {
                                        return;
                                    }

                                    const previous = !!this.valueGenerated;
                                    this.valueGenerated = !this.valueGenerated;
                                    this.isUpdating = true;
                                    this.rowError = '';

                                    try {
                                        const response = await fetch('{{ route('tasks.quick-assign', $task) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            },
                                            body: JSON.stringify({
                                                value_generated: this.valueGenerated,
                                            }),
                                        });
                                        const data = await response.json().catch(() => ({}));

                                        if (!response.ok) {
                                            throw new Error(data?.message ?? 'No se pudo actualizar.');
                                        }
                                    } catch (error) {
                                        this.valueGenerated = previous;
                                        this.rowError = error?.message ?? 'No se pudo actualizar.';
                                    } finally {
                                        this.isUpdating = false;
                                    }
                                },
                            }"
                        >
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
                                                <div class="relative">
                                                    <label class="sr-only" for="mobile-task-status-{{ $task->id }}">Estado</label>
                                                    <select
                                                        id="mobile-task-status-{{ $task->id }}"
                                                        class="rounded-full border-0 py-1 pl-3 pr-8 text-xs font-semibold focus:ring-2 focus:ring-indigo-500"
                                                        :style="`background:${selectedStatusBackgroundColor}; color:${selectedStatusColor}`"
                                                        x-model="selectedStatusId"
                                                        :disabled="isUpdating"
                                                        @change="setStatus(
                                                            $event.target.value,
                                                            $event.target.selectedOptions[0]?.dataset.color ?? '#312e81',
                                                            $event.target.selectedOptions[0]?.dataset.backgroundColor ?? '#eef2ff'
                                                        )"
                                                    >
                                                        @foreach($statuses as $statusOption)
                                                            <option
                                                                value="{{ $statusOption->id }}"
                                                                @selected((string) ($task->status_id ?? '') === (string) $statusOption->id)
                                                                data-color="{{ $statusOption->color ?? '#312e81' }}"
                                                                data-background-color="{{ $statusOption->background_color ?? '#eef2ff' }}"
                                                            >
                                                                {{ $statusOption->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[10px] text-current">▾</span>
                                                </div>
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
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-full border px-2 py-1 text-xs"
                                    :class="valueGenerated ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50 text-gray-600'"
                                    :disabled="isUpdating"
                                    @click="toggleValueGenerated"
                                >
                                    <span
                                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200"
                                        :class="valueGenerated ? 'bg-emerald-500' : 'bg-gray-300'"
                                    >
                                        <span
                                            class="inline-block h-4 w-4 transform rounded-full bg-white transition duration-200"
                                            :class="valueGenerated ? 'translate-x-4' : 'translate-x-1'"
                                        ></span>
                                    </span>
                                    <span x-text="valueGenerated ? 'Genera valor' : 'No genera valor'"></span>
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-red-600" x-show="rowError" x-text="rowError"></p>
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
                                    <option value="{{ $user->id }}" @selected((string)$user->id === (string)old('user_id', auth()->id()))>{{ $user->name }}</option>
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
                                    <input id="quick_priority" name="priority" type="range" min="1" max="10" step="1" value="1" class="mt-2 w-full accent-indigo-600">
                                    <div class="text-xs text-gray-600 mt-1 flex justify-between">
                                        <span>Baja</span>
                                        <span class="font-semibold">1</span>
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
                                    <x-text-input id="quick_due_date" name="due_date" type="datetime-local" class="mt-1 block w-full" value="{{ $defaultDueDateTime }}" />
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
                                <x-input-label for="quick_value_generated" value="Generación de valor" />
                                <label class="mt-2 inline-flex items-center gap-3 cursor-pointer select-none">
                                    <input type="hidden" name="value_generated" value="0">
                                    <input id="quick_value_generated" name="value_generated" type="checkbox" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                    <span class="text-sm text-gray-700">Marcar si esta tarea genera valor.</span>
                                </label>
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
