@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Usuarios</h1>
        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">Crear</a>
    </div>

    @if (session('status'))
        <div class="mb-4 text-green-700 bg-green-100 border border-green-200 px-4 py-3 rounded">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600 uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Foto</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Rol</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Teléfono</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                    <tr>
                        <td class="px-4 py-3 text-gray-700">{{ $user->id }}</td>
                        <td class="px-4 py-3">
                            @if($user->image_url)
                                <img src="{{ asset('storage/'.$user->image_url) }}" class="h-10 w-10 rounded-full object-cover" alt="{{ $user->name }}">
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            <a href="{{ route('users.show', $user->id) }}" class="hover:underline">{{ $user->name }}</a>
                            <div class="text-xs text-gray-500">{{ $user->position }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            <div>{{ $user->email }}</div>
                            <div class="text-xs text-gray-500">{{ $user->address }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $user->role_name }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $user->status_name }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $user->phone }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-500 text-sm">Editar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
