@php
    $project = $task->project;
    $status = $task->status;
    $user = $task->user;
    $imgPath = $user?->image_url
        ? (str_contains($user->image_url, '/') ? $user->image_url : 'files/users/'.$user->image_url)
        : null;
    $initials = collect(explode(' ', trim($user->name ?? '')))
        ->filter()
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->take(2)
        ->implode('');
    $projectInitials = $project
        ? collect(explode(' ', trim($project->name)))
            ->filter()
            ->map(fn ($w) => mb_substr($w, 0, 1))
            ->take(2)
            ->implode('')
        : '?';
@endphp

<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
    <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-full flex items-center justify-center text-xs font-semibold text-white" style="background: {{ $project->color ?? '#9ca3af' }}">
            {{ $projectInitials ?: '?' }}
        </div>
        <div>
            <p class="text-xs text-gray-500">Tarea</p>
            <h1 class="text-lg font-semibold text-gray-900">{{ $task->name }}</h1>
        </div>
    </div>
    <div class="flex items-center gap-3">
        @if($status)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background: {{ $status->background_color ?? '#eef2ff' }}; color: {{ $status->color ?? '#312e81' }}">
                {{ $status->name }}
            </span>
        @endif
        <a href="{{ route('tasks.edit', $task) }}?sidebar=1" data-task-panel-url="{{ route('tasks.edit', $task) }}?sidebar=1" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">Editar</a>
        <button type="button" class="text-sm text-gray-600 hover:text-gray-800" @click="$dispatch('close-task-panel')">Cerrar</button>
    </div>
</div>

<div class="px-5 py-4 space-y-4 max-h-[80vh] overflow-y-auto">
    <div class="grid md:grid-cols-2 gap-4">
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500">Proyecto</span>
            <div class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full" style="background: {{ $project->color ?? '#e5e7eb' }}"></span>
                <span class="text-sm text-gray-800">{{ $project->name ?? 'Sin proyecto' }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500">Responsable</span>
            @if($user)
                @if($imgPath)
                    <img src="{{ asset('storage/'.$imgPath) }}" class="h-9 w-9 rounded-full object-cover ring-2 ring-gray-100" alt="{{ $user->name }}">
                @else
                    <div class="h-9 w-9 rounded-full flex items-center justify-center text-xs font-semibold bg-gray-200 text-gray-700">
                        {{ $initials ?: '?' }}
                    </div>
                @endif
                <span class="text-sm text-gray-800">{{ $user->name }}</span>
            @else
                <span class="text-sm text-gray-500">Sin asignar</span>
            @endif
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
        <div>
            <p class="text-xs text-gray-500">Vencimiento</p>
            <p class="font-medium">{{ optional($task->due_date)->format('d M Y H:i') ?? 'Sin fecha' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Entrega</p>
            <p class="font-medium">{{ optional($task->delivery_date)->format('d M Y H:i') ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Puntos</p>
            <p class="font-medium">{{ $task->points ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Estimado</p>
            <p class="font-medium">{{ $task->estimated_points ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Tipo</p>
            <p class="font-medium">{{ $task->type->name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Subtipo</p>
            <p class="font-medium">{{ $task->subType->name ?? '—' }}</p>
        </div>
        <div class="md:col-span-2">
            <p class="text-xs text-gray-500">URL</p>
            <p class="font-medium break-all">
                @if($task->url_finished)
                    <a href="{{ $task->url_finished }}" class="text-indigo-600 hover:underline" target="_blank" rel="noreferrer">{{ $task->url_finished }}</a>
                @else
                    —
                @endif
            </p>
        </div>
    </div>

    <div>
        <p class="text-xs text-gray-500 mb-1">Descripción</p>
        <div class="text-sm text-gray-800 leading-relaxed bg-gray-50 rounded p-3">
            {!! nl2br(e($task->description ?? 'Sin descripción')) !!}
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <p class="text-xs text-gray-500 mb-1">Copy / instrucciones</p>
            <div class="text-sm text-gray-800 leading-relaxed bg-gray-50 rounded p-3">
                {!! nl2br(e($task->copy ?? '—')) !!}
            </div>
        </div>
        <div>
            <p class="text-xs text-gray-500 mb-1">Caption</p>
            <div class="text-sm text-gray-800 leading-relaxed bg-gray-50 rounded p-3">
                {!! nl2br(e($task->caption ?? '—')) !!}
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <p class="text-xs text-gray-500">Adjunto</p>
        @if($task->file_url)
            <a href="{{ asset('storage/'.$task->file_url) }}" class="text-sm text-indigo-600 hover:underline" target="_blank" rel="noreferrer">Ver archivo</a>
        @else
            <span class="text-sm text-gray-500">Sin archivo</span>
        @endif
    </div>
</div>
