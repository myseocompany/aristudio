@extends('layout')

@section('content')
<h1>Show Document</h1>
<form method="POST" action="/documents/{{$model->id}}/edit">
  {{ csrf_field() }}
  <div class="form-group">
      <label for="customer_status">Document Type:</label>
       <input type="text" class="form-control" id="email" name="type_id" placeholder="Estado"  @if (isset($model->type_id) && ($model->type_id != ''))
                value="{{$model->type->name}}" 
      @endif readonly >
 </div>
  <div class="form-group">
  	<label for="internal_id">Internal Code:</label>
  	<input type="text" class="form-control" name="internal_id" placeholder="Code" value="{{$model->internal_id}}" readonly="">
  </div>
  <div class="form-group">
  	<label for="date">Date:</label>
  	<input type="text" class="form-control" name="date" placeholder="Date" value="{{$model->date}}" readonly="">
  </div>
   <div class="form-group">
  	<label for="amount">Amount:</label>
  	<input type="text" class="form-control" name="amount" placeholder="amount" value="{{$model->amount}}" readonly="">
  </div>
  <div class="form-group">
  	<label for="description">Description:</label>
  	<textarea name="description" id="description" readonly="" cols="30" class="form-control" rows="10">{{$model->description}}</textarea>
  </div>
  {{--   <div class="form-group">
      <label for="account">Increment Account:</label>
       <input type="text" class="form-control" id="email" name="inc_account_id" placeholder="Incremento"  @if (isset($model->inc_account_id) && ($model->inc_account_id != ''))
                value="{{$model->accounts->name}}" 
      @endif readonly >
 </div> --}}
   <div class="form-group">
    <label for="budget">Increment Account:</label>
    <select class="form-control" style="background-color: #e9ecef;" onmouseover="this.disabled=true;" onmouseout="this.disabled=true;" name="inc_account_id" id="inc_account_id">
      @foreach($accounts as $item)
      <option style="background-color: #e9ecef;" value="{{$item->id}}" @if($item->id==$model->inc_account_id)selected="selected" @endif>{{$item->name}}</option>
      @endforeach
    </select>
  </div>
  <div class="form-group">
    <label for="budget">Decrement Account:</label>
    <select class="form-control" style="background-color: #e9ecef;" onmouseover="this.disabled=true;" onmouseout="this.disabled=true;" name="inc_account_id" id="dec_account_id">
      @foreach($accounts as $item)
      <option style="background-color: #e9ecef;" value="{{$item->id}}" @if($item->id==$model->dec_account_id)selected="selected" @endif>{{$item->name}}</option>
      @endforeach
    </select>
  </div>
{{--   <div class="form-group">
      <label for="customer_status">Decrement Account:</label>
       <input type="text" class="form-control" id="email" name="dec_account_id" placeholder="Decremento"  @if (isset($model->dec_account_id) && ($model->dec_account_id != ''))
                value="{{$model->account->name}}" 
      @endif readonly >
 </div> --}}
  <div class="form-group">
      <label for="customer_status">Project:</label>
       <input type="text" class="form-control" id="email" name="project" placeholder="project"  @if (isset($model->project_id) && ($model->project_id != ''))
                value="{{$model->project->name}}" 
      @endif readonly >
 </div>
    
  <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Edit</button>
</form>
@endsection