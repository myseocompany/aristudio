@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-6">Editar usuario</h1>

    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white shadow rounded p-6">
        @csrf
        @method('PUT')

        @include('users.partials.form', [
            'user' => $user,
            'roles' => $roles,
            'statuses' => $statuses,
            'submit' => 'Actualizar'
        ])
    </form>
</div>
@endsection
