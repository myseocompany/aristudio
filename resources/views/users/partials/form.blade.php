@php
    $selectedRole = old('role_id', $user->role_id ?? null);
    $selectedStatus = old('status_id', $user->status_id ?? 1);
    $imgPath = $user->image_url ?? null;
    if ($imgPath && !str_contains($imgPath, '/')) {
        $imgPath = 'files/users/'.$imgPath;
    }
    $initials = collect(explode(' ', trim($user->name ?? '')))
        ->filter()
        ->map(fn($part) => mb_substr($part, 0, 1))
        ->take(2)
        ->implode('');
    $selectedProjects = old('project_ids', $selectedProjects ?? []);
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Avatar y foto --}}
    <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 flex flex-col items-center gap-4">
        <div class="h-36 w-36 rounded-lg overflow-hidden ring-4 ring-white shadow">
            @if($imgPath)
                <img src="{{ asset('storage/'.$imgPath) }}" class="h-full w-full object-cover" alt="avatar">
            @else
                <div class="h-full w-full flex items-center justify-center text-3xl font-semibold bg-gradient-to-br from-indigo-500 to-pink-500 text-white">
                    {{ $initials ?: '?' }}
                </div>
            @endif
        </div>
        <div class="w-full">
            <label class="block text-sm font-medium text-gray-700">Foto</label>
            <label class="mt-2 inline-flex items-center gap-2 px-3 py-2 bg-gray-50 text-sm text-gray-700 border border-dashed border-gray-300 rounded cursor-pointer hover:border-indigo-200 hover:bg-indigo-50 transition">
                <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6h.1a5 5 0 011 9.9M12 12v4m0 0l-2-2m2 2l2-2"/>
                </svg>
                <span>Seleccionar archivo</span>
                <input type="file" name="image_url" class="hidden">
            </label>
            @if(isset($user) && $user->image_url)
                <p class="text-xs text-gray-500 mt-1">Deja vacío para mantener la imagen actual.</p>
            @endif
        </div>
    </div>

    {{-- Datos personales --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-4">
            <div>
                <h3 class="font-semibold text-gray-900">Datos personales</h3>
                <p class="text-sm text-gray-500">Información básica y contacto.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cargo</label>
                    <input type="text" name="position" value="{{ old('position', $user->position ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Documento</label>
                    <input type="text" name="document" value="{{ old('document', $user->document ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dirección</label>
                    <input type="text" name="address" value="{{ old('address', $user->address ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                    <select name="role_id" class="mt-1 w-full border rounded px-3 py-2">
                        <option value="">Seleccione</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected($selectedRole == $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <select name="status_id" class="mt-1 w-full border rounded px-3 py-2">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" @selected($selectedStatus == $status->id)>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de nacimiento</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', isset($user->birth_date) ? \Illuminate\Support\Carbon::parse($user->birth_date)->format('Y-m-d') : '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contraseña @if(!isset($user))<span class="text-xs text-gray-500">(opcional)</span>@else<span class="text-xs text-gray-500">(dejar vacío para mantener)</span>@endif</label>
                    <input type="password" name="password" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-4">
            <div>
                <h3 class="font-semibold text-gray-900">Contrato y disponibilidad</h3>
                <p class="text-sm text-gray-500">Condiciones laborales.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tarifa por hora</label>
                    <input type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', $user->hourly_rate ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Disponibilidad</label>
                    <input type="number" name="availability" value="{{ old('availability', $user->availability ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo de contrato</label>
                    <input type="text" name="contract_type" value="{{ old('contract_type', $user->contract_type ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de ingreso</label>
                    <input type="date" name="entry_date" value="{{ old('entry_date', isset($user->entry_date) ? \Illuminate\Support\Carbon::parse($user->entry_date)->format('Y-m-d') : '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de salida</label>
                    <input type="date" name="termination_date" value="{{ old('termination_date', isset($user->termination_date) ? \Illuminate\Support\Carbon::parse($user->termination_date)->format('Y-m-d') : '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Horas contratadas</label>
                    <input type="number" name="contracted_hours" value="{{ old('contracted_hours', $user->contracted_hours ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-4">
            <div>
                <h3 class="font-semibold text-gray-900">Salud</h3>
                <p class="text-sm text-gray-500">EPS, ARL y sangre.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">EPS</label>
                    <input type="text" name="eps" value="{{ old('eps', $user->eps ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ARL</label>
                    <input type="text" name="arl" value="{{ old('arl', $user->arl ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo de sangre</label>
                    <input type="text" name="blood_type" value="{{ old('blood_type', $user->blood_type ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" maxlength="5">
                </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-5 space-y-4" id="projects">
        <div>
            <h3 class="font-semibold text-gray-900">Proyectos</h3>
            <p class="text-sm text-gray-500">Asigna o quita proyectos vinculados al usuario.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-72 overflow-y-auto pr-1">
            @foreach($projects as $project)
                <label class="flex items-center gap-3 border rounded-lg px-3 py-2 hover:border-indigo-200">
                    <input type="checkbox" name="project_ids[]" value="{{ $project->id }}" class="rounded text-indigo-600"
                        @checked(in_array($project->id, $selectedProjects))>
                    <span class="h-9 w-9 flex items-center justify-center rounded-lg text-white font-semibold text-sm" style="background-color: {{ $project->color ?? '#e5e7eb' }};">
                        {{ mb_substr($project->name,0,1) }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $project->name }}</p>
                        <p class="text-xs text-gray-500">ID: {{ $project->id }}</p>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

        <div class="pt-2 flex gap-3">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded shadow hover:bg-indigo-500">{{ $submit }}</button>
            <a href="{{ route('users.index') }}" class="px-5 py-2.5 border rounded text-gray-700 hover:bg-gray-50">Cancelar</a>
        </div>
    </div>
</div>
