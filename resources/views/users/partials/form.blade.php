@php
    $selectedRole = old('role_id', $user->role_id ?? null);
    $selectedStatus = old('status_id', $user->status_id ?? 1);
@endphp

@if ($errors->any())
    <div class="p-3 rounded bg-red-100 text-red-700">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
        <label class="block text-sm font-medium text-gray-700">Documento</label>
        <input type="text" name="document" value="{{ old('document', $user->document ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
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
        <label class="block text-sm font-medium text-gray-700">Dirección</label>
        <input type="text" name="address" value="{{ old('address', $user->address ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Contraseña @if(!isset($user))<span class="text-xs text-gray-500">(opcional: genera una si se deja vacío)</span>@else<span class="text-xs text-gray-500">(dejar vacío para mantener)</span>@endif</label>
        <input type="password" name="password" class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Foto</label>
        <input type="file" name="image_url" class="mt-1 w-full border rounded px-3 py-2">
        @if(isset($user) && $user->image_url)
            <div class="mt-2">
                <img src="{{ asset('storage/'.$user->image_url) }}" class="h-12 w-12 rounded-full object-cover" alt="avatar">
            </div>
        @endif
    </div>
</div>

<div class="pt-4 flex gap-3">
    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">{{ $submit }}</button>
    <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded text-gray-700">Cancelar</a>
</div>
