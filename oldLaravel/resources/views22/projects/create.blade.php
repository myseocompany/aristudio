@extends('layout')

@section('content')
<h1>Create Project</h1>
<form method="POST" action="/projects">
{{ csrf_field() }}
  <div class="form-group">
    <label for="name"><strong>Name</strong> </label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required">
  </div>
  <div class="form-group">
    <label for="description"><strong>Description</strong> </label>    
    <textarea class="form-control" placeholder="Descripcion breve del proyecto" name="description" id="description" name="description" cols="30" rows="10"></textarea>
  </div>
  <div class="form-group">
    <label for="weight"><strong>Weight</strong> </label>
    <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight" required="required">
  </div>
  <div class="form-group">
    <label for="budget"><strong>Budget</strong> </label>
    <input type="text" class="form-control" id="budget" name="budget" placeholder="budget">    
  </div>
  <div class="form-group">
    <label for="start_date"><strong>Start Date</strong> </label>
    <input type="date" class="form-control" id="start_date" name="start_date" placeholder="YYYY-MM-DD" value="<?php echo date_create('now')->format('Y-m-d'); ?>">    
  </div>
 <div class="form-group">
    <label for="finish_date"><strong>Finish Date</strong> </label>
    <input type="date" class="form-control" id="finish_date" name="finish_date" placeholder="YYYY-MM-DD" value="<?php echo date_create('now')->format('Y-m-d'); ?>">    
  </div> 
  <div class="form-group">
   <label for="type_id">Type</label>
     <select name="type_id" id="type_id" class="form-control" >
        <option value="">Please Select a Type</option>
    @foreach($project_type as $item)
        <option value="{{$item->id}}">{{$item->name}}</option>
    @endforeach
    ?>
    </select>
  </div>
 <div class="form-group">
   <label for="status_id">Status</label>
     <select name="status_id" id="status_id" class="form-control" >
        <option value="">Please Select a status</option>
    @foreach($project_status as $item)
        <option value="{{$item->id}}">{{$item->name}}</option>
    @endforeach
    ?>
    </select>
  </div>
  <div class="form-group">
    <label for="lead_target">Lead Target</label>
    <input type="text" name="lead_target" class="form-control">
  </div>
   <div class="form-group">
    <label for="lead_target">Sales</label>
    <input type="text" name="sales" class="form-control">
  </div>
  
  <button type="submit" class="btn btn-primary my-2 my-sm-0">Submit</button>
</form>
@endsection