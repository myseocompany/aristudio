@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="bg-white shadow rounded p-6">
        <div class="flex items-center gap-4 mb-4">
            @if($user->image_url)
                <img src="{{ asset('storage/'.$user->image_url) }}" class="h-16 w-16 rounded-full object-cover" alt="{{ $user->name }}">
            @endif
            <div>
                <h1 class="text-2xl font-semibold">{{ $user->name }}</h1>
                <p class="text-gray-600">{{ $user->position }}</p>
            </div>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="font-semibold text-gray-700">Email</dt>
                <dd class="text-gray-800">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-700">Teléfono</dt>
                <dd class="text-gray-800">{{ $user->phone }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-700">Documento</dt>
                <dd class="text-gray-800">{{ $user->document }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-700">Dirección</dt>
                <dd class="text-gray-800">{{ $user->address }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-700">Rol</dt>
                <dd class="text-gray-800">{{ $user->role_name }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-700">Estado</dt>
                <dd class="text-gray-800">{{ $user->status_name }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('users.edit', $user->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">Editar</a>
            <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded text-gray-700">Volver</a>
        </div>
    </div>
</div>
@endsection
