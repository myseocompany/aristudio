@if ($errors->any())
    <div class="p-3 rounded bg-red-100 text-red-700 mb-4">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white shadow-sm rounded border border-gray-100 p-6 space-y-4">
    <div>
        <h3 class="font-semibold text-gray-900">Datos del rol</h3>
        <p class="text-sm text-gray-500">Define el nombre del rol.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" required>
        </div>
    </div>
</div>

<div class="pt-2 flex gap-3">
    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded shadow hover:bg-indigo-500">{{ $submit }}</button>
    <a href="{{ route('roles.index') }}" class="px-5 py-2.5 border rounded text-gray-700 hover:bg-gray-50">Cancelar</a>
</div>
