@php
    $statuses = $statuses ?? collect();
    $projects = $projects ?? collect();
    $users = $users ?? collect();
    $parentTypes = $parentTypes ?? collect();
    $subTypes = $subTypes ?? collect();
    $defaultStatusId = $defaultStatusId ?? null;
@endphp

<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
    <div>
        <p class="text-xs text-gray-500">Editar tarea</p>
        <h1 class="text-lg font-semibold text-gray-900">{{ $task->name }}</h1>
    </div>
    <button type="button" class="text-sm text-gray-600 hover:text-gray-800" @click="$dispatch('close-task-panel')">Cerrar</button>
</div>

<div class="px-5 py-4 max-h-[80vh] overflow-y-auto">
    <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="grid gap-3">
            <div>
                <x-input-label for="panel_name" value="Nombre" />
                <x-text-input id="panel_name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $task->name) }}" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-input-label for="panel_project_id" value="Proyecto" />
                    <select id="panel_project_id" name="project_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin proyecto</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected((string)old('project_id', $task->project_id) === (string)$project->id)>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('project_id')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="panel_user_id" value="Responsable" />
                    <select id="panel_user_id" name="user_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin asignar</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected((string)old('user_id', $task->user_id) === (string)$user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('user_id')" class="mt-1" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-input-label for="panel_status_id" value="Estado" />
                    <select id="panel_status_id" name="status_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" @selected((string)old('status_id', $task->status_id ?? $defaultStatusId) === (string)$status->id)>{{ $status->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status_id')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="panel_priority" value="Prioridad (1 a 10)" />
                    <input id="panel_priority" name="priority" type="range" min="1" max="10" step="1" value="{{ old('priority', $task->priority ?? 5) }}" class="mt-2 w-full accent-indigo-600">
                    <x-input-error :messages="$errors->get('priority')" class="mt-1" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-input-label for="panel_points" value="Puntos" />
                    <x-text-input id="panel_points" name="points" type="number" step="0.1" min="0" class="mt-1 block w-full" value="{{ old('points', $task->points) }}" />
                    <x-input-error :messages="$errors->get('points')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="panel_estimated_points" value="Estimado" />
                    <x-text-input id="panel_estimated_points" name="estimated_points" type="number" step="0.1" min="0" class="mt-1 block w-full" value="{{ old('estimated_points', $task->estimated_points) }}" />
                    <x-input-error :messages="$errors->get('estimated_points')" class="mt-1" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-input-label for="panel_due_date" value="Fecha de vencimiento" />
                    <x-text-input id="panel_due_date" name="due_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d\TH:i')) }}" />
                    <x-input-error :messages="$errors->get('due_date')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="panel_delivery_date" value="Fecha de entrega" />
                    <x-text-input id="panel_delivery_date" name="delivery_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('delivery_date', optional($task->delivery_date)->format('Y-m-d\TH:i')) }}" />
                    <x-input-error :messages="$errors->get('delivery_date')" class="mt-1" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-input-label for="panel_type_id" value="Tipo" />
                    <select id="panel_type_id" name="type_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin tipo</option>
                        @foreach($parentTypes as $type)
                            <option value="{{ $type->id }}" @selected((string)old('type_id', $task->type_id) === (string)$type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('type_id')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="panel_sub_type_id" value="Subtipo" />
                    <select id="panel_sub_type_id" name="sub_type_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin subtipo</option>
                        @foreach($subTypes as $type)
                            <option value="{{ $type->id }}" @selected((string)old('sub_type_id', $task->sub_type_id) === (string)$type->id)>{{ $type->label ?? $type->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('sub_type_id')" class="mt-1" />
                </div>
            </div>
            <div>
                <x-input-label for="panel_url_finished" value="URL entregable" />
                <x-text-input id="panel_url_finished" name="url_finished" type="url" class="mt-1 block w-full" value="{{ old('url_finished', $task->url_finished) }}" />
                <x-input-error :messages="$errors->get('url_finished')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="panel_description" value="Descripción" />
                <textarea id="panel_description" name="description" rows="3" class="mt-1 block w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $task->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-input-label for="panel_copy" value="Copy / instrucciones" />
                    <textarea id="panel_copy" name="copy" rows="2" class="mt-1 block w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('copy', $task->copy) }}</textarea>
                    <x-input-error :messages="$errors->get('copy')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="panel_caption" value="Caption" />
                    <textarea id="panel_caption" name="caption" rows="2" class="mt-1 block w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('caption', $task->caption) }}</textarea>
                    <x-input-error :messages="$errors->get('caption')" class="mt-1" />
                </div>
            </div>
            <div>
                <x-input-label for="panel_file" value="Archivo" />
                <input id="panel_file" name="file" type="file" class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @if($task->file_url)
                    <p class="text-xs text-gray-600 mt-1">Actual: <a href="{{ asset('storage/'.$task->file_url) }}" class="text-indigo-600 hover:underline" target="_blank" rel="noreferrer">Ver archivo</a></p>
                @endif
                <x-input-error :messages="$errors->get('file')" class="mt-1" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" @click="$dispatch('close-task-panel')" class="text-sm text-gray-600 hover:text-gray-800">Cancelar</button>
            <x-primary-button>{{ $submit ?? 'Actualizar' }}</x-primary-button>
        </div>
    </form>
</div>
