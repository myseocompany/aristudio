<div class="space-y-6" x-data="{ showAdvanced: false }">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $task->name) }}" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="project_id" value="Proyecto" />
            <select id="project_id" name="project_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Sin proyecto</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" @selected((string)old('project_id', $task->project_id) === (string)$project->id)>{{ $project->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="user_id" value="Responsable" />
            <select id="user_id" name="user_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Sin asignar</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string)old('user_id', $task->user_id) === (string)$user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="url_finished" value="URL entregable" />
            <x-text-input id="url_finished" name="url_finished" type="url" class="mt-1 block w-full" value="{{ old('url_finished', $task->url_finished) }}" />
            <x-input-error :messages="$errors->get('url_finished')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-center justify-between border border-dashed border-gray-200 rounded px-4 py-3 bg-gray-50">
        <div>
            <p class="text-sm font-semibold text-gray-800">Datos adicionales</p>
            <p class="text-xs text-gray-500">Fechas, estado, puntos, tipos y adjuntos opcionales.</p>
        </div>
        <button type="button" @click="showAdvanced = !showAdvanced" class="px-3 py-2 text-sm bg-white border border-gray-200 rounded hover:bg-gray-100">
            <span x-show="!showAdvanced">Mostrar</span>
            <span x-show="showAdvanced">Ocultar</span>
        </button>
    </div>

    <div class="space-y-6" x-show="showAdvanced" x-transition x-cloak>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="status_id" value="Estado" />
                <select id="status_id" name="status_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" @selected((string)old('status_id', $task->status_id ?? $defaultStatusId) === (string)$status->id)>{{ $status->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
            </div>
            <div class="grid grid-cols-2 gap-3">
            <div>
                <x-input-label for="priority" value="Prioridad (1 a 10)" />
                <input id="priority" name="priority" type="range" min="1" max="10" step="1" value="{{ old('priority', $task->priority ?? 5) }}" class="mt-2 w-full accent-indigo-600">
                <div class="text-xs text-gray-600 mt-1 flex justify-between">
                    <span>Baja</span>
                    <span class="font-semibold">{{ old('priority', $task->priority ?? 5) }}</span>
                    <span>Alta</span>
                </div>
                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
            </div>
                <div>
                    <x-input-label for="points" value="Puntos" />
                    <x-text-input id="points" name="points" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('points', $task->points) }}" />
                    <x-input-error :messages="$errors->get('points')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="estimated_points" value="Estimado" />
                <x-text-input id="estimated_points" name="estimated_points" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('estimated_points', $task->estimated_points) }}" />
                <x-input-error :messages="$errors->get('estimated_points')" class="mt-2" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="due_date" value="Fecha de vencimiento" />
                <x-text-input id="due_date" name="due_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d\TH:i')) }}" />
                <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="delivery_date" value="Fecha de entrega" />
                <x-text-input id="delivery_date" name="delivery_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('delivery_date', optional($task->delivery_date)->format('Y-m-d\TH:i')) }}" />
                <x-input-error :messages="$errors->get('delivery_date')" class="mt-2" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="type_id" value="Tipo" />
                <select id="type_id" name="type_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Sin tipo</option>
                    @foreach($parentTypes as $type)
                        <option value="{{ $type->id }}" @selected((string)old('type_id', $task->type_id) === (string)$type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('type_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="sub_type_id" value="Subtipo" />
                <select id="sub_type_id" name="sub_type_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Sin subtipo</option>
                    @foreach($subTypes as $type)
                        <option value="{{ $type->id }}" @selected((string)old('sub_type_id', $task->sub_type_id) === (string)$type->id)>{{ $type->label ?? $type->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('sub_type_id')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="description" value="Descripción" />
            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $task->description) }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="copy" value="Copy / instrucciones" />
                <textarea id="copy" name="copy" rows="2" class="mt-1 block w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('copy', $task->copy) }}</textarea>
                <x-input-error :messages="$errors->get('copy')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="caption" value="Caption" />
                <textarea id="caption" name="caption" rows="2" class="mt-1 block w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus-border-indigo-500">{{ old('caption', $task->caption) }}</textarea>
                <x-input-error :messages="$errors->get('caption')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="file" value="Archivo" />
            <input id="file" name="file" type="file" class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            <x-input-error :messages="$errors->get('file')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('tasks.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
        <x-primary-button>{{ $submit ?? 'Guardar' }}</x-primary-button>
    </div>
</div>
