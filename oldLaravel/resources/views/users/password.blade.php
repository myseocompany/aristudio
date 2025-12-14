@extends('layout')

@section('content')
<h1>Change Password</h1>
<form method="POST" action="/users/{{$model->id}}/updatePassword" enctype="multipart/form-data">
  {{ csrf_field() }}
    <div class="form-group">
        <label for="password">New Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Password</button>
</form>

@endsection
