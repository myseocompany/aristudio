@extends('layout')

@section('content')
<h1>Edit Task</h1>

<form method="POST" action="/tasks/update"  enctype="multipart/form-data">
{{ csrf_field() }}
<input type="hidden" id="id" name="id" value="{{$model->id}}">
<div class="form-group">
      <label for="name">Name</label>
      <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required" value="{{ $model->name}}">
    </div>
<div class="row">
  <div class="col-md">
    <div class="form-group">
      <label for="name">Due Date</label>
      
      <input type="datetime-local" class="form-control" id="due_date" name="due_date" placeholder="YYYY/MMM/DD" required="required" @if(isset($model->due_date)) value="{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $model->due_date)->format('Y-m-d\TH:i')}}" @endif>
    </div>
 
    <div class="form-group">
      <label for="project_id">Project</label>
      <select name="project_id" id="project_id" class="form-control">
      @foreach ($projects as $item)
      <?php 
        //dd($item);
      ?>
          <option value="{{$item->id}}" @if ($model->project_id == $item->id) selected="selected" @endif>{{$item->name}}</option>
      @endforeach
      </select>
    </div>

    <div class="form-group">
     <label for="status_id">Status</label>
       <select name="status_id" id="status_id" class="form-control" >
      @foreach($task_status as $item)
          <option value="{{$item->id}}" @if ($model->status_id == $item->id) selected="selected" @endif>{{$item->name}}</option>
      @endforeach
      ?>
      </select>
    </div>
    
        
  <div class="form-group ">
    <label for="name" >Not Billing</label>
    <input type="checkbox" class="form-control col-1" id="not_billing" name="not_billing" @if($model->not_billing == true) checked @endif >
  </div>
    
  </div>  
  <div class="col-md">
    <div class="form-group">
      <label for="user_id">User</label>
      <select name="user_id" id="user_id" class="form-control">
        <option value="">Please select a user</option>
      @foreach ($users as $user)
          <option value="{{$user->id}}" @if ($model->user_id == $user->id) selected="selected" @endif>{{$user->name}}</option>
      @endforeach
      </select>
    </div>
   
    <div class="form-group">
      <label for="points">Points</label>    
      <input type="text" class="form-control" id="points" name="points" placeholder="Points" value="{{ $model->points }}">
    </div>
    <div class="form-group">
            <label for="priority">Priority</label>    
            <select name="priority" id="priority" class="form-control">
              <option value="0">Select a Priority</option>
              <option value="1" @if($model->priority == 1) selected="" @endif>1</option>
              <option value="2" @if($model->priority == 2) selected="" @endif >2</option>
              <option value="3" @if($model->priority == 3) selected="" @endif >3</option>
              <option value="4" @if($model->priority == 4) selected="" @endif >4</option>
              <option value="5" @if($model->priority == 5) selected="" @endif >5</option>
              <option value="6" @if($model->priority == 6) selected="" @endif >6</option>
              <option value="7" @if($model->priority == 7) selected="" @endif >7</option>
              <option value="8" @if($model->priority == 8) selected="" @endif >8</option>
              <option value="9" @if($model->priority == 9) selected="" @endif >9</option>
              <option value="10" @if($model->priority == 10) selected="" @endif >10</option>
            </select>
          </div>
  <div class="form-group">
    <label for="points">Url Finished Task </label>
      <input class="form-control" name="url_finished" id="url_finished" placeholder="Url" value="{{ $model->url_finished }}"></input>
    </div>

</div>
  </div>
  
  


<div class="form-group row">
  <div class="form-group col-md-6">
    <label for="type_id">Type</label>
    <select name="type_id" id="type_id" class="custom-select" onchange="getSubTypes(this.value, {{$model->sub_type_id}});">
      <option value="">Select a type...</option>
      @foreach($task_types as $option)
      <option value="{{$option->id}}" @if($model->type_id==$option->id) selected="selected" @endif >{{$option->name}}</option>
      @endforeach  
    </select>
  </div>
  <input type="hidden" name="hidden_sub_type_id" id="hidden_sub_type_id" value="{{$model->sub_type_id}}">
  <div class="form-group col-md-6">
    <span id="after_type">
    </span>
  </div>
</div>

<div class="form-group row">
  <div class="form-group col-md-6">
    <label for="copy">Copy In</label>
    <textarea class="form-control" name="copy" id="copy" cols="30" rows="7">{{$model->copy}}</textarea>
  </div>
  <div class="form-group col-md-6">
    <label for="caption">Copy Out</label>
    <textarea class="form-control" name="caption" id="caption" cols="30" rows="7">{{$model->caption}}</textarea>
  </div>
</div>


  <div>  
    <label for="description">Description</label>
    

    <textarea class="form-control" name="description" id="description" cols="30" rows="10">{{ $model->description }}</textarea>

  </div>

  <div class="form-group">
            <label for="name">Archivo</label>
            <input type="file" class="form-control" id="file" name="file" placeholder="Name">
          </div>


  
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
<script type="text/javascript">

  var type_id = $("#type_id").val();
  var hidden_sub_type_id = $("#hidden_sub_type_id").val();
  console.log(hidden_sub_type_id);
  if(type_id != ""){
    $("#after_type").empty();
    endpoint = '/tasks/setType/'+type_id;
    $.ajax({
        type: 'GET', //THIS NEEDS TO BE GET
        url: endpoint,
        dataType: 'json',
        success: function (data) {
            loadSubTypes(data, hidden_sub_type_id);
            
        },
        error: function(data) { 
        }
    });
  }

  function getSubTypes(tyid, sub_type_id){
  $("#after_type").empty();
  type_id = "0";
  if (!isNaN(parseInt(tyid)))
    type_id = tyid;
    endpoint = '/tasks/setType/'+tyid;
    $.ajax({
        type: 'GET', //THIS NEEDS TO BE GET
        url: endpoint,
        dataType: 'json',
        success: function (data) {
            loadSubTypes(data, hidden_sub_type_id);
            
        },
        error: function(data) { 
        }
    });

  }

  function loadSubTypes(data, stid){
    console.log(stid);
    str = '<label for="sub_type_id">Sub Type</label><select name="sub_type_id" id="sub_type_id" class="custom-select">;';
    str += '<option value="">Select a sub  type...</option>';
    $.each(data, function(i, obj) {
      str += '<option value="'+obj.id+'" >'+obj.name+'</option>';
    });
    str += '</select>';

    $("#after_type").html(str);

    $("#sub_type_id option[value='"+stid+"']").attr("selected", true);
  }
</script>
@endsection






