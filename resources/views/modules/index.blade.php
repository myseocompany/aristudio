<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Módulos
            </h2>
            <a href="{{ route('modules.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Nuevo módulo</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-4">
                    @if (session('status'))
                        <div class="mb-4 text-green-700 bg-green-100 border border-green-200 px-4 py-3 rounded">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm" id="modules-table">
                            <thead class="bg-gray-50 text-gray-600 uppercase tracking-wide">
                                <tr>
                                    <th class="px-4 py-2 text-left w-12"></th>
                                    <th class="px-4 py-2 text-left">Nombre</th>
                                    <th class="px-4 py-2 text-left">Slug</th>
                                    <th class="px-4 py-2 text-left">Peso</th>
                                    <th class="px-4 py-2 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="modules-body">
                                @forelse($modules as $module)
                                    <tr data-id="{{ $module->id }}" draggable="true" class="drag-row">
                                        <td class="px-4 py-2 text-gray-400 cursor-move">&#x2630;</td>
                                        <td class="px-4 py-2 font-medium text-gray-900">{{ $module->name }}</td>
                                        <td class="px-4 py-2 text-gray-600">{{ $module->slug }}</td>
                                        <td class="px-4 py-2 text-gray-600">{{ $module->weight }}</td>
                                        <td class="px-4 py-2 text-right space-x-2 whitespace-nowrap">
                                            <a href="{{ route('modules.show', $module) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-semibold">Ver</a>
                                            <a href="{{ route('modules.edit', $module) }}" class="text-blue-600 hover:text-blue-500 text-sm font-semibold">Editar</a>
                                            <form action="{{ route('modules.destroy', $module) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este módulo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-500 text-sm font-semibold">Borrar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No hay módulos creados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">Arrastra las filas para reordenar el peso en el menú.</p>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            const tbody = document.getElementById('modules-body');
            if (!tbody) return;
            let dragEl = null;

            tbody.addEventListener('dragstart', (e) => {
                dragEl = e.target.closest('tr');
                if (!dragEl) return;
                dragEl.classList.add('opacity-60');
                e.dataTransfer.effectAllowed = 'move';
            });

            tbody.addEventListener('dragend', () => {
                if (dragEl) dragEl.classList.remove('opacity-60');
                dragEl = null;
            });

            tbody.addEventListener('dragover', (e) => {
                e.preventDefault();
                const target = e.target.closest('tr');
                if (!dragEl || !target || dragEl === target) return;
                const rect = target.getBoundingClientRect();
                const before = (e.clientY - rect.top) < (rect.height / 2);
                tbody.insertBefore(dragEl, before ? target : target.nextSibling);
            });

            tbody.addEventListener('drop', () => {
                const order = Array.from(tbody.querySelectorAll('tr')).map(tr => tr.dataset.id);
                fetch("{{ route('modules.reorder') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({order})
                }).catch(() => {});
            });
        })();
    </script>
</x-app-layout>
