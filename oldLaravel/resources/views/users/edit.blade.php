@extends('layout')

@section('content')
<h1>Edit Users</h1>
<form method="POST" action="/users/{{$model->id}}/update" enctype="multipart/form-data">
  {{ csrf_field() }}

  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required value="{{$model->name}}">
      </div>
      <div class="form-group">
        <label for="document">Document</label>
        <input type="text" class="form-control" id="document" name="document" placeholder="Document" required value="{{$model->document}}">
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required value="{{$model->email}}">
      </div>
      <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required value="{{$model->phone}}">
      </div>
      <div class="form-group">
        <label for="birth_date">Birth Date</label>
        <input type="date" name="birth_date" id="birth_date" class="form-control" value="{{$model->birth_date}}">
      </div>
      <div class="form-group">
        <label for="blood_type">Blood Type</label>
        <input type="text" name="blood_type" id="blood_type" class="form-control" value="{{$model->blood_type}}">
      </div>
      <div class="form-group">
        <label for="address">Address</label>
        <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="{{$model->address}}">
      </div>
      <div class="form-group">
        <label for="position">Position</label>
        <input type="text" class="form-control" id="position" name="position" placeholder="Position" required value="{{$model->position}}">
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label for="daily_goal">Daily Goal</label>
        <input type="number" class="form-control" id="daily_goal" name="daily_goal" value="{{$model->daily_goal}}">
      </div>
      <div class="form-group">
        <label for="hourly_rate">Hourly Rate</label>
        <input type="number" class="form-control" id="hourly_rate" name="hourly_rate"  value="{{$model->hourly_rate}}">
      </div>
      <div class="form-group">
        <label for="contract_type">Contract Type</label>
        <input type="text" class="form-control" id="contract_type" name="contract_type" value="{{$model->contract_type}}">
      </div>
      <div class="form-group">
        <label for="eps">E.P.S</label>
        <input type="text" class="form-control" id="eps" name="eps" value="{{$model->eps}}">
      </div>
      <div class="form-group">
        <label for="arl">A.R.L</label>
        <input type="text" class="form-control" id="arl" name="arl" value="{{$model->arl}}">
      </div>
      <div class="form-group">
        <label for="role_id">Role</label>
        <select class="form-control" name="role_id" id="role_id">
          @foreach($role as $item)
          <option value="{{$item->id}}" @if($item->id==$model->role_id) selected @endif>{{$item->name}}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="status_id">Status</label>
        <select class="form-control" name="status_id" id="status_id">
          @foreach($user_statuses as $item)
          <option value="{{$item->id}}" @if($item->id==$model->status_id) selected @endif>{{$item->name}}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="image_url">Photo</label>
        <input type="file" id="image_url" name="image_url" class="form-control">
      </div>
    </div>
  </div>

  <div class="form-group mt-4">
    <button type="submit" class="btn btn-primary">Submit</button>
  </div>
</form>
@endsection
