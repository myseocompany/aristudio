@extends('layout')


@section('content')


<div class="">
    <h2>Change User Password</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update', $user->id) }}">

        <div class="mb-3">
            <label for="">{{ $user->name }}</label>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Nueva contraseña</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
                <div class="text-danger">{{ $message }}</div>
        </div>

        <div class="mb-3">
            <label for="new_password_confirmation" class="form-label">Confirmar nueva contraseña</label>
            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
    </form>
</div>
@endsection
