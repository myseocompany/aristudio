@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-6">Crear usuario</h1>

    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white shadow rounded p-6">
        @csrf

        @include('users.partials.form', [
            'user' => null,
            'roles' => $roles,
            'statuses' => $statuses,
            'submit' => 'Crear'
        ])
    </form>
</div>
@endsection
