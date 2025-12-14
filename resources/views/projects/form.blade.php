@php
    $selectedStatus = old('status_id', $project->status_id ?? null);
    $selectedType = old('type_id', $project->type_id ?? null);
    $selectedUsers = old('user_ids', $selectedUsers ?? ($project->users->pluck('id')->toArray() ?? []));
@endphp

@if ($errors->any())
    <div class="p-3 rounded bg-red-100 text-red-700 mb-4">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $project->name ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo</label>
            <select name="type_id" class="mt-1 w-full border rounded px-3 py-2">
                <option value="">Seleccione</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" @selected($selectedType == $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Descripción</label>
            <textarea name="description" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('description', $project->description ?? '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Estado</label>
            <select name="status_id" class="mt-1 w-full border rounded px-3 py-2">
                <option value="">Seleccione</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" @selected($selectedStatus == $status->id)>{{ $status->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Color</label>
            <input type="text" name="color" value="{{ old('color', $project->color ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="#hex o nombre">
        </div>
    </div>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-4">
    <div>
        <h3 class="font-semibold text-gray-900">Fechas</h3>
        <p class="text-sm text-gray-500">Planifica inicio y fin.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Inicio</label>
            <input type="date" name="start_date" value="{{ old('start_date', $project->start_date ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Fin</label>
            <input type="date" name="finish_date" value="{{ old('finish_date', $project->finish_date ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>
    </div>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-4" id="team">
    <div>
        <h3 class="font-semibold text-gray-900">Equipo asignado</h3>
        <p class="text-sm text-gray-500">Selecciona los usuarios vinculados al proyecto.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto pr-1">
        @foreach($users as $user)
            @php
                $initials = collect(explode(' ', trim($user->name)))
                    ->filter()
                    ->map(fn($part) => mb_substr($part, 0, 1))
                    ->take(2)
                    ->implode('');
                $img = $user->image_url ? (str_contains($user->image_url, '/') ? $user->image_url : 'files/users/'.$user->image_url) : null;
            @endphp
            <label class="flex items-center gap-3 border rounded-lg px-3 py-2 hover:border-indigo-200">
                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="rounded text-indigo-600"
                    @checked(in_array($user->id, $selectedUsers ?? []))>
                <div class="h-9 w-9 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-pink-500 text-white flex items-center justify-center text-sm font-semibold">
                    @if($img)
                        <img src="{{ asset('storage/'.$img) }}" class="h-full w-full object-cover" alt="{{ $user->name }}">
                    @else
                        {{ $initials ?: '?' }}
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                </div>
            </label>
        @endforeach
    </div>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-4">
    <div>
        <h3 class="font-semibold text-gray-900">Presupuestos y metas</h3>
        <p class="text-sm text-gray-500">Números clave del proyecto.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Presupuesto</label>
            <input type="number" step="0.01" name="budget" value="{{ old('budget', $project->budget ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Presupuesto Ads</label>
            <input type="number" step="0.01" name="ads_budget" value="{{ old('ads_budget', $project->ads_budget ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Weight</label>
            <input type="number" step="0.01" name="weight" value="{{ old('weight', $project->weight ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Weekly pieces</label>
            <input type="number" name="weekly_pieces" value="{{ old('weekly_pieces', $project->weekly_pieces ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Lead target</label>
            <input type="number" name="lead_target" value="{{ old('lead_target', $project->lead_target ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Monthly points goal</label>
            <input type="number" name="monthly_points_goal" value="{{ old('monthly_points_goal', $project->monthly_points_goal ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Sales</label>
            <input type="number" step="0.01" name="sales" value="{{ old('sales', $project->sales ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>
    </div>
</div>

<div class="pt-2 flex gap-3">
    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded shadow hover:bg-indigo-500">{{ $submit }}</button>
    <a href="{{ route('projects.index') }}" class="px-5 py-2.5 border rounded text-gray-700 hover:bg-gray-50">Cancelar</a>
</div>
