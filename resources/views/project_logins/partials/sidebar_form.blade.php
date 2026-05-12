<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 space-y-4">
    <div>
        <h3 class="text-lg font-semibold text-gray-900">Nuevo acceso</h3>
        <p class="text-sm text-gray-500">Crea rápidamente un acceso o plataforma para un proyecto.</p>
    </div>

    @if($projects->isEmpty())
        <p class="text-sm text-gray-500">No tienes proyectos disponibles para crear logins.</p>
    @else
        @if ($errors->any())
            <div class="p-2 text-sm text-red-700 bg-red-50 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('logins.quick-store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Proyecto</label>
                <select name="project_id" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">Selecciona uno</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected(old('project_id', $filters['project_id'] ?? '') == $project->id)>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Usuario</label>
                <input type="text" name="user" value="{{ old('user') }}" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="text" name="password" value="{{ old('password') }}" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">URL</label>
                <input type="text" name="url" value="{{ old('url') }}" class="mt-1 w-full rounded border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="https://">
            </div>
            <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', true)) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Activo
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="is_paid" value="0">
                    <input type="checkbox" name="is_paid" value="1" @checked((bool) old('is_paid', false)) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Pago
                </label>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-500">Crear acceso</button>
        </form>
    @endif
</div>
