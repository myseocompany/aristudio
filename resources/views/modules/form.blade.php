@csrf
<div class="space-y-4">
    <div>
        <x-input-label for="name" value="Nombre" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $module->name) }}" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="slug" value="Slug" />
        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" value="{{ old('slug', $module->slug) }}" required />
        <p class="text-xs text-gray-500 mt-1">Se usará como ruta en el menú.</p>
        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="weight" value="Peso (orden en menú)" />
        <x-text-input id="weight" name="weight" type="number" class="mt-1 block w-full" value="{{ old('weight', $module->weight ?? 0) }}" />
        <x-input-error :messages="$errors->get('weight')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('modules.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
        <x-primary-button>{{ $submitLabel ?? 'Guardar' }}</x-primary-button>
    </div>
</div>
