@extends('layout')

@section('content')
<h1>Edit Project</h1>
<form method="POST" action="/projects/{{$model->id}}/update">
{{ csrf_field() }}
  
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required" value="{{$model->name}}">
  </div>
  <div class="form-group">
    <label for="description">Description</label>    
    <textarea class="form-control" name="description" id="description" name="description" cols="30" rows="10">{{$model->description}}</textarea>
  </div>
  <div class="form-group">
    <label for="weight"><strong>Weight</strong> </label>
    <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight" required="required" value="{{$model->weight}}">
  </div>
  <div class="form-group">
    <label for="budget">Budget</label>
    <input type="text" class="form-control" id="budget" name="budget" placeholder="budget" value="{{$model->budget}}">   
  </div>
  <div class="form-group">
    <label for="budget">Ads Budget</label>
    <input type="text" class="form-control" id="ads_budget" name="ads_budget" placeholder="advertise budget" value="{{$model->ads_budget}}">   
  </div>
  <div class="form-group">
    <label for="budget">Weekly Pieces</label>
    <input type="text" class="form-control" id="weekly_pieces" name="weekly_pieces" placeholder="weekly_pieces" value="{{$model->weekly_pieces}}">   
  </div>

  <div class="form-group">
    <label for="color">Color</label>
    <input type="text" class="form-control" id="color" name="color" placeholder="color" value="{{$model->color}}">   
  </div>


  <div class="form-group">
    <label for="start_date">Start Date</label>
    <input type="date" class="form-control" id="start_date" name="start_date" 
    value="{{ $model->getDateInput($model->start_date) }}">    
  </div>
    
  <div class="form-group">
    <label for="finish_date">Finish Date</label>
    <input type="date" class="form-control" id="finish_date" name="finish_date" value="{{ $model->getDateInput($model->finish_date) }}">  
  </div>
  <div class="form-group">
   <label for="type_id">Type</label>
     <select name="type_id" id="type_id" class="form-control" >
        <option value="">Please Select a Type</option>
    @foreach($project_type as $item)
        <option value="{{$item->id}}" @if ($model->type_id == $item->id) selected="selected" @endif>{{$item->name}}</option>
    @endforeach
    ?>
    </select>
  </div>
<div class="form-group">
   <label for="status_id">Status</label>
     <select name="status_id" id="status_id" class="form-control" >
        <option value="">Please Select a status</option>
    @foreach($project_status as $item)
        <option value="{{$item->id}}" @if ($model->status_id == $item->id) selected="selected" @endif>{{$item->name}}</option>
    @endforeach
    ?>
    </select>
  </div>
  <div class="form-group">
    <label for="lead_target">Lead Target</label>
    <input type="text" name="lead_target" class="form-control" value="{{$model->lead_target}}">
  </div>
  <div class="form-group">
    <label for="lead_target">Lead Target</label>
    <input type="text" name="lead_target" class="form-control" value="{{$model->lead_target}}">
  </div>
  <div class="form-group">
    <label for="lead_target">Monthly points goal</label>
    <input type="text" name="monthly_points_goal" class="form-control" value="{{$model->monthly_points_goal}}">
  </div>
   
  <button type="submit" class="btn btn-sm btn-primary my-2 my-sm-0">Submit</button>
</form>
@endsection