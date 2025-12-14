@extends('layout')

@section('content')
<br>
<h1>Tasks</h1>
{{-- @include('tasks.search') --}}
<script>




</script>


<div id="accordion" role="tablist" id="headingOne">
    <div class="card">
      <div class="card-header" role="tab" id="headingOne">
        <h5 class="mb-0">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            Create Task
          </a>
        </h5>
      </div>
    
    </div>

  <div id="collapseOne" class="collapse " role="tabpanel" aria-labelledby="headingOne">
      <div class="card-block">  

        <h2>Create Task</h2>
          <form method="POST" action="/tasks" enctype="multipart/form-data">
        {{ csrf_field() }}
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required">
          </div>
          <div class="form-group">
            <label for="user_id">Project</label>
            <select name="project_id" id="project_id" class="form-control" required="required">
              <option value="">Select a Project</option>
            @foreach ($projects as $project)
                <option value="{{$project->id}}"@if ($project->id== $request->project_id) selected="selected" @endif>{{$project->name}}</option>
            @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="user_id">User</label>
            <select name="user_id" id="user_id" class="form-control">
              <option value="">Select a User</option>
            @foreach ($users as $user)
                <option value="{{$user->id}}" @if ($user->id == $request->user_id) selected="selected" @endif>{{$user->name}}</option>
            @endforeach
            </select>
          </div>

          <div class="form-group">
            <label for="name">Due Date</label>
            <input type="date" class="form-control" id="due_date" name="due_date" placeholder="YYYY/MMM/DD" required="required" value="<?php echo date('Y-m-d');?>">
          </div>

          <div class="form-group">
            <label for="points">Points</label>    
            <input type="text" class="form-control" id="points" name="points" placeholder="Points" >
            <label for="points">Description</label>

            <textarea class="form-control" name="description" id="description" cols="30" rows="10"></textarea>



            <input type="hidden" name="from" id="from" class="form-control" value="project">
          </div>


          <div class="form-group">
           <label for="status_id">Status</label>
             <select name="status_id" id="status_id" class="form-control" >
            @foreach($task_status as $item)
                <option value="{{$item->id}}">{{$item->name}}</option>
            @endforeach
            ?>
            </select>
          </div>

          <div class="form-group">
            <label for="name">Archivo</label>
            <input type="file" class="form-control" id="file" name="file" placeholder="Name">
          </div>

          
          <button type="submit" class="btn brn-sum btn-primary my-2 my-sm-0">Submit</button>
        </form>
    </div>
  </div>
</div>
<div>
    <form action="/tasks/" method="GET">
      <select name="filter" class="custom-select" id="filter" onchange="update()">
        <option value="">select time</option>

        <option value="7" @if ($request->filter == "7") selected="selected" @endif>next 7 days</option>
        <option value="0" @if ($request->filter == "0") selected="selected" @endif>today</option>
        <option value="thisweek" @if ($request->filter == "thisweek") selected="selected" @endif>this week</option>
        <option value="currentmonth" @if ($request->filter == "currentmonth") selected="selected" @endif>this month</option>
        
        <option value="-1" @if ($request->filter == "-1") selected="selected" @endif>yesterday</option>
        <option value="lastweek" @if ($request->filter == "lastweek") selected="selected" @endif>last week</option>
        <option value="lastmonth" @if ($request->filter == "lastmonth") selected="selected" @endif>last month</option>
      	
      	<option value="-7" @if ($request->filter == "-7") selected="selected" @endif>last 7 days</option>
        <option value="-30" @if ($request->filter == "-30") selected="selected" @endif>last 30 days</option>
        
      </select>
      
    <input class="input-date" type="date" id="from_date" name="from_date" onchange="cleanFilter()" value="{{$request->from_date}}">
    <input class="input-date" type="date" id="to_date" name="to_date" onchange="cleanFilter()" value="{{$request->to_date}}">

<!--  
*
*    Combo de proyectos
*
-->
      <select name="project_id" class="custom-select" id="project_id" onchange="submit();">
        <option value="">select a project</option>
       @foreach($projects as $project)
          <option value="{{$project->id}}" @if ($request->project_id == $project->id) selected="selected" @endif>
          <?php echo substr($project->name, 0, 10); ?>
          </option>
        @endforeach
      </select>



<!--  
*
*    Combo de usuarios
*
-->
      <select name="user_id" class="custom-select" id="user_id" onchange="submit();">
        <option value="">select a user</option>
        @foreach($users as $user)
          <option value="{{$user->id}}" @if ($request->user_id == $user->id) selected="selected" @endif>
             <?php echo substr($user->name, 0, 10); ?>
            
          </option>
        @endforeach
      </select>

<!--  
*
*    Combo de estatus
*
-->

      
      <select multiple="" name="status_id" class="slectpicker custom-select" id="status_id"">
        <option value="">select a status</option>


        @foreach($task_status as $item)
         
          <?php
            $selected = false;
            for($i=0; $i<count($statuses_id); $i++){
              if($statuses_id[$i] == $item->id){
                ;
                $selected = true;
              }
            }
          ?>

          <option value="{{$item->id}}"  @if ($selected)  selected="selected" @endif >{{$item->name}}</option>
        @endforeach
       
      </select>
    <input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filter" >

    </form>
  </div>



<!--- FIN DEL CREATE -->



<?php $totalPoints = 0; 
      $totalTask = 1;
      $max_items = 5;
      if($tasksGroup->count()!=0){
        $numGroups = ceil($tasksGroup->count()/ $max_items);
        if($numGroups>1){
          $page_length = round($tasksGroup->count()/round($numGroups));
        }else
        {
          $page_length = $tasksGroup->count();
        }
        //dd(ceil($numGroups));
        $count = 0;
        $page = 0;

        while($count < $tasksGroup->count()){
?>
         
          <div>
            <ul class="groupbar bb_hbox">
              @foreach($tasksGroup as $item)
               @if(($count>1) && ($count % $page_length)==0)
                
            </ul>
          </div>
          <div>
            <ul class="groupbar bb_hbox">
                
              @endif 
              <li class="groupBarGroup" style="background-color: @if(isset($item->color)&&($item->color!="")){{$item->color.';'}} @else #ccc; @endif<?php 
                  if($tasksGroup->count()!=0){
                    $with = 100/($page_length);
                    echo "width:".$with."%";
                    /*
                    //var_dump($with);
                    if($with>20)
                      echo "width:".$with.";";
                    else
                      echo "width:200px;";
                    */
                  }
               ?>" page="{{$page_length}}">
                <h3>{{$item->sum_points}}</h3>
               
                <div><a href="#{{$item->name}}">{{$item->name}}</a></div>
              </li>  
              <?php $count++; ?>        
              @endforeach
            </ul>
          </div>
    <?php 
        
        }}
     ?>   





	<div class="table-responsive">
            <table class="table table-striped table-hover table-responsive" id="taskTable">
              <thead class="thead-white">
                <tr>
                  <th>#</th>
                  <th>Image</th>
                  <th>Name</th>
                  <th>Points</th>
                  <th>User</th>
                  @if(!isset($request->from_date))
                    <th>Actual Due</th>
                  @endif
                    <th>Status</th>
                  
                  <th></th>
                  
                  @if(isset($request->from_date))
                    @foreach ($days as $key => $value) 
                      <th>{{\Carbon\Carbon::parse($value)->day}}</th>
                    @endforeach 
                  @endif
                  
                </tr>
              </thead>

<?php $last_task_status_id=-1;
      $last_project_id = -1;
 ?>

              <tbody>
                @foreach($model as $item)
                  @if($last_project_id!=$item->project_id)
                  <tr style="background-color:#fff" class="title_status">
                    <td colspan="@if(sizeof($days)){{ sizeof($days)+8 }} @else 8 @endif" class="project-row">
                      <h3>
                        {{$item->project->name}}
                        <h3></td>
                  </tr>
                  <?php $last_task_status_id=-1; ?>
                  @endif
                  @if($last_task_status_id!=$item->status_id)
                    <tr style="background-color: @if(isset($item->status->color)) {{$item->status->color}}  @endif" class="title_status">
                    <td colspan="@if(sizeof($days)){{ sizeof($days)+8 }} @else 8 @endif"  ot class="status-row"><a id="@if(isset($item->status->name)) {{$item->status->name}} @endif">@if(isset($item->status->name)) {{$item->status->name}} @endif</a></td>
                  </tr>
                  @endif
                  
                

                <form action="" method="POST" name="updateTaskForm{{$item->id}}" id="updateTaskForm{{$item->id}}">
                  {{ csrf_field() }}
                <tr>

                  <td>
                    <input type="hidden" name="token" id="token_id_{{$item->id}}" value="{{csrf_token()}}">
                    <input type="hidden" name="id" id="task_id_{{$item->id}}" value="{{$item->id}}">
                    {{ $totalTask }}</td>
                    
                    <td>
                    @if (isset($item->file_url))
    <!--
    <img src="{{Storage::disk('local')->url('files/'.$item->file_url)}}" alt="" width="50" onmouseenter="showImage({{$item->id}})" onmouseleave="hideImage({{$item->id}})">
  -->
     <img src="{{'/laravel/storage/app/public/files/'.$item->file_url}}" alt="" width="50" onmouseenter="showImage({{$item->id}})" onmouseleave="hideImage({{$item->id}})">
    <div class="bigimage" id="{{$item->id}}">
      <!--
      <img src="{{Storage::disk('local')->url('files/'.$item->file_url)}}" alt="" height="100%"> 
    -->
    <img src="{{'/laravel/storage/app/public/files/'.$item->file_url}}" alt="" height="100%"> 
    </div>
    <script>hideImage({{$item->id}});</script>
  @endif
                  </td>
                 
                  <td><a href="/tasks/{{ $item->id }}">@if (strlen($item->name)>=30){{ substr($item->name,0, 29) }}...@else {{ $item->name }} @endif</a></td>
                   <td>{{ $item->points }}</td>
                  <td><?php 
                 
                  
                  if(isset($item->user_id)&&($item->user_id!="")){
                     echo substr($item->user_name,0,10);
                  }
                  
                  ?> </td>
                  @if(!isset($request->from_date))
                  

                  <td>{{ $item->due_date}}</td>
                  @endif

                  
                  <td>
                    <select name="status_id" id="status_id_{{$item->id}}" onchange="updateStatusAjax({{$item->id}});" class="custom-select">
                    @foreach ($task_status as $status)
                      <option value="{{$status->id}}" @if($status->id==$item->status_id) selected="selected"@endif>
                        {{$status->name}}
                      </option>
                    @endforeach
                    </select>
                    
                  </td>
                  
                  <td>

                    <a href="/tasks/{{ $item->id }}/edit">

                      <span class="btn btn-sm btn-warning my-2 my-sm-0" title="Edit" aria-hidden="true">
                      <span class="fa fa-pencil"></a>
                    <a href="/tasks/{{ $item->id }}"><span class="btn btn-sm btn-success my-2 my-sm-0" aria-hidden="true"><span class="fa fa-eye" title="Consult"></span></span></a>
                  </td>
                  
                  @if(isset($request->from_date))
                    @foreach ($days as $key => $value) 

                      <th @if($value==$item->due_date && isset($item->user->color)) style="background-color:{{ $item->user->color }}" @endif>
                        @if($value==$item->due_date) {{$item->points}} @endif
                      </th>
                    
                    @endforeach 
                  @endif

                </tr>
                <?php 
                  $last_task_status_id = $item->status_id;
               
                  $last_project_id = $item->project_id;
                
                if(isset( $item->points )){$totalPoints += $item->points; }
                  $totalTask++;
                ?>
              </form>
 				@endforeach
        </tbody>
        <thead class="thead-dark">
          <tr>
                  <td></td>
                  
                  <td></td>
                  <td>Total Points</td>
                  <td>{{ $totalPoints }}</td>
                  <td></td>
                   <td></td>
                   <td></td>

                   @if(!isset($request->from_date))
                   
                 
                  
                  
                  
                  <td></td>
    @endif
                  </tr>
        </thead>
      
    </table>
  </div>

<div>
     
</div>


@endsection

@section('footerjs')
<script>
    
$(document).ready(function(){
  update();
});
  
</script>
@endsection

