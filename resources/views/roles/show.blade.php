<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="text-xs text-gray-500 mb-1 space-x-1">
                    <a href="{{ route('roles.index') }}" class="hover:text-indigo-600">Roles</a>
                    <span>/</span>
                    <span class="text-gray-700">{{ $role->name }}</span>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Permisos de {{ $role->name }}</h2>
            </div>
            <a href="{{ route('roles.edit', $role) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Editar nombre</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="mb-4 text-green-700 bg-green-100 border border-green-200 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('roles.permissions', $role) }}" method="POST" class="bg-white shadow-sm rounded border border-gray-100 p-4 space-y-4">
                @csrf
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Role Modules</p>
                        <p class="text-lg font-semibold text-gray-800">Permisos por módulo</p>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Guardar</button>
                </div>

                <style>
                    .toggle-input {
                        position: absolute;
                        opacity: 0;
                        width: 0;
                        height: 0;
                    }
                    .toggle-label {
                        position: relative;
                        display: inline-flex;
                        width: 44px;
                        height: 24px;
                        border-radius: 9999px;
                        background: #e5e7eb;
                        transition: background-color 0.2s ease;
                        align-items: center;
                        cursor: pointer;
                    }
                    .toggle-knob {
                        position: absolute;
                        width: 20px;
                        height: 20px;
                        border-radius: 9999px;
                        background: #ffffff;
                        box-shadow: 0 1px 2px rgba(0,0,0,0.12);
                        left: 2px;
                        transition: transform 0.2s ease;
                    }
                    .toggle-input:checked + .toggle-label {
                        background: #6366f1;
                    }
                    .toggle-input:checked + .toggle-label .toggle-knob {
                        transform: translateX(20px);
                    }
                </style>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600 uppercase tracking-wide">
                            <tr>
                                <th class="px-3 py-2">Módulo</th>
                                <th class="px-3 py-2 text-center">Create</th>
                                <th class="px-3 py-2 text-center">Read</th>
                                <th class="px-3 py-2 text-center">Update</th>
                                <th class="px-3 py-2 text-center">Delete</th>
                                <th class="px-3 py-2 text-center">List</th>
                                <th class="px-3 py-2 text-center">Alcance lectura</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($modules as $module)
                                @php
                                    $perm = $permissions[(int)$module->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="px-3 py-2 font-medium text-gray-900">{{ $module->name }}</td>
                                    @foreach(['create','read','update','delete','list'] as $field)
                                        <td class="px-3 py-2 text-center">
                                            @php
                                                $column = match($field) {
                                                    'create' => 'created',
                                                    'read' => 'readed',
                                                    'update' => 'updated',
                                                    'delete' => 'deleted',
                                                    default => 'list'
                                                };
                                                $isChecked = $perm && (int)($perm->$column ?? 0) === 1;
                                                $checkboxId = "perm_{$module->id}_{$field}";
                                            @endphp
                                            <input type="checkbox"
                                                   name="permissions[{{ $module->id }}][{{ $field }}]"
                                                   value="1"
                                                   id="{{ $checkboxId }}"
                                                   class="toggle-input"
                                                   @checked($isChecked)>
                                            <label for="{{ $checkboxId }}" class="toggle-label">
                                                <span class="toggle-knob"></span>
                                            </label>
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-2 text-center">
                                        @php
                                            $scopeName = "permissions[{$module->id}][scope]";
                                            $currentScope = $perm->view_scope ?? 0;
                                        @endphp
                                        <select name="{{ $scopeName }}" class="rounded border-gray-300 text-sm px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="0" @selected((int)$currentScope === 0)>Solo asignados</option>
                                            <option value="1" @selected((int)$currentScope === 1)>Todos</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
