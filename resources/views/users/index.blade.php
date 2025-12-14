<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Usuarios
            </h2>
            <a href="{{ route('users.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Crear</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="mb-4 text-green-700 bg-green-100 border border-green-200 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded border border-gray-100">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-3">
                    <div class="flex-1">
                        <p class="text-sm text-gray-500">Listado de usuarios</p>
                        <p class="text-lg font-semibold text-gray-800">{{ count($users) }} registros</p>
                    </div>
                    <form method="get" class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Estado</label>
                        <select name="status" onchange="this.form.submit()" class="border-gray-300 rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" @selected((string)$statusFilter === (string)$status->id)>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600 uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3">Usuario</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Rol</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Último login</th>
                                <th class="px-4 py-3">Teléfono</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($users as $user)
                                @php
                                    $imgPath = $user->image_url
                                        ? (str_contains($user->image_url, '/') ? $user->image_url : 'files/users/'.$user->image_url)
                                        : null;
                                    $initials = collect(explode(' ', trim($user->name)))
                                        ->filter()
                                        ->map(fn($part) => mb_substr($part, 0, 1))
                                        ->take(2)
                                        ->implode('');
                                    $palette = [
                                        'bg-amber-100 text-amber-800',
                                        'bg-indigo-100 text-indigo-800',
                                        'bg-emerald-100 text-emerald-800',
                                        'bg-sky-100 text-sky-800',
                                        'bg-pink-100 text-pink-800',
                                        'bg-slate-100 text-slate-800',
                                        'bg-purple-100 text-purple-800',
                                        'bg-teal-100 text-teal-800',
                                    ];
                                    $colorClass = $palette[$user->id % count($palette)];
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        <div class="flex items-center gap-3">
                                            @if($imgPath)
                                                <img src="{{ asset('storage/'.$imgPath) }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-100" alt="{{ $user->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full flex items-center justify-center text-xs font-semibold {{ $colorClass }}">
                                                    {{ $initials ?: '?' }}
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('users.show', $user->id) }}" class="hover:underline">{{ $user->name }}</a>
                                                <div class="text-xs text-gray-500">{{ $user->position }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        <div>{{ $user->email }}</div>
                                        @if($user->address)
                                            <div class="text-xs text-gray-500">{{ $user->address }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-medium">
                                            {{ $user->role_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $isActive = ($user->status_id ?? null) == 1;
                        $statusColor = $isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                            {{ $user->status_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        @php
                                            $lastLogin = $user->last_login ? \Illuminate\Support\Carbon::parse($user->last_login)->format('Y-m-d') : '—';
                                        @endphp
                                        {{ $lastLogin }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $user->phone }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">Editar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
