@php
    $action = $login->exists
        ? route('projects.logins.update', [$project, $login])
        : route('projects.logins.store', $project);
    $method = $login->exists ? 'PUT' : 'POST';
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

<form action="{{ $action }}" method="POST" class="space-y-5">
    @csrf
    @if($login->exists)
        @method('PUT')
    @endif

    <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-5 space-y-4">
        <div>
            <h3 class="font-semibold text-gray-900">Acceso</h3>
            <p class="text-sm text-gray-500">Credenciales asociadas al proyecto.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if ($login->exists && isset($projects) && $projects->count())
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Proyecto</label>
                    <select name="project_id" class="mt-1 w-full border rounded px-3 py-2" required>
                        @foreach ($projects as $projectOption)
                            <option value="{{ $projectOption->id }}" @selected((int) old('project_id', $login->project_id) === $projectOption->id)>
                                {{ $projectOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="name" value="{{ old('name', $login->name) }}" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Usuario</label>
                <input type="text" name="user" value="{{ old('user', $login->user) }}" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="text" name="password" value="{{ old('password', $login->password) }}" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">URL</label>
                <input type="text" name="url" value="{{ old('url', $login->url) }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="https://">
            </div>
        </div>
    </div>

    <div class="flex gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded shadow hover:bg-indigo-500">
            {{ $login->exists ? 'Actualizar' : 'Crear' }}
        </button>
        <a href="{{ route('projects.show', $project) }}" class="px-5 py-2.5 border rounded text-gray-700 hover:bg-gray-50">Cancelar</a>
    </div>
</form>
