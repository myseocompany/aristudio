@extends('layout')

@section('content')
<br>
<h1>Tasks</h1>
{{-- @include('tasks.search') --}}




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


  <script>
    colors = new Array();
    text = new Array();
    
    @foreach ($task_status as $status)
      colors[{{$status->id}}] = "{{$status->color}}"; 
      text[{{$status->id}}] = "{{$status->name}}"; 
    @endforeach
    
    </script>


	<div class="table-responsive">
            <table class="table table-responsive" id="taskTable">
              <thead class="thead-white">
                <tr>
                  <th>Image</th>
                  <th>Name</th>
                  <th>User</th>
                  <th>Status</th>
                  
                 
                  
                </tr>
              </thead>

<?php $last_task_status_id=-1;
      $last_project_id = -1;
 ?>

              <tbody>
                @foreach($model as $item)
                  @if($last_project_id!=$item->project_id)
                  <tr style="background-color:#fff" class="title_status">
                    <td colspan="4" class="project-row">
                      <h3>
                        {{$item->project->name}}
                        <h3></td>
                  </tr>
                  <?php $last_task_status_id=-1; ?>
                  @endif
                 
                  
                

                <form action="" method="POST" name="updateTaskForm{{$item->id}}" id="updateTaskForm{{$item->id}}">
                  {{ csrf_field() }}
                <tr >
                    <input type="hidden" name="token" id="token_id_{{$item->id}}" value="{{csrf_token()}}">
                    <input type="hidden" name="id" id="task_id_{{$item->id}}" value="{{$item->id}}">

                    
                    <td>
                    @if (isset($item->file_url))
    <!--
    <img src="{{Storage::disk('local')->url('files/'.$item->file_url)}}" alt="" width="50" onmouseenter="showImage({{$item->id}})" onmouseleave="hideImage({{$item->id}})">
  -->
     <img src="{{'/laravel/storage/app/public/files/'.$item->file_url}}" alt="" class="task_image" onmouseenter="showImage({{$item->id}})" onmouseleave="hideImage({{$item->id}})">
    <div class="bigimage" id="{{$item->id}}">
      <!--
      <img src="{{Storage::disk('local')->url('files/'.$item->file_url)}}" alt="" height="100%"> 
    -->
    <img src="{{'/laravel/storage/app/public/files/'.$item->file_url}}" alt="" height="100%" class="task_image_big"> 
    </div>
    <script>hideImage({{$item->id}});</script>
  @endif
                  </td>
                 
                  <td>
                    
          
                  
                  @if(isset($item->due_date)) 
                  <span class="no_print">
          {{ date('M-d', strtotime($item->due_date)) }}
          </span> @if(isset($item->user)&&isset($item->user_id)&&($item->user_id!=""))
          {{$item->user->name}}@endif
        @else N/A @endif
                    

                    <h6>{{$item->name}}</h6>
                    {{$item->descriptcion}} 
                    </td>

                  
                  
                  
                  <td>
                  <div class="user_image_container">
                  @if(isset($item->user)&&!isset($item->user->image_url)&&isset($item->user_id)&&($item->user_id!=""))
                      <img src="/laravel/storage/app/public/files/users/{{$item->user->image_url}}" alt="" class="user_image" id="task_user_image_{{$item->id}}">
                  
                  @else
                    @if(isset($item->user->image_url))
                  
                    <img src="/laravel/storage/app/public/files/users/{{$item->user->image_url}}" alt="" class="user_image" id="task_user_image_{{$item->id}}">
                  
                  @endif
                  
                  @endif
                  </div>
                   </td>
                  
                  
                  
                  <td id="task_cel_{{$item->id}}" style="background-color: @if(isset($item->status->color)) {{$item->status->color}}  @endif">
                    <div class="task_container">
                    <!--
                    <select name="status_id" id="status_id_{{$item->id}}" onchange="updateStatusAjax({{$item->id}});" class="custom-select">
                    @foreach ($task_status as $status)
                      <option value="{{$status->id}}" @if($status->id==$item->status_id) selected="selected"@endif>
                        {{$status->name}}
                      </option>
                    @endforeach
                    </select>
                    -->
                    <div>
                      <strong>{{ $item->points }} </strong>
                    </div>
                    <a id="#ref_{{$item->id}}" href="#ref_{{$item->id}}" onclick="updateNextStatusAjax({{$item->id}});">
                      {{$item->status->name}}
                    </a>
                    <div>
                      
                    </div>
                    </div>
                  </td>
                  
                  
                

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
                  <td>Total Points</td>
                  <td></td>
                  <td></td>
                  <td class="task_container">{{ $totalPoints }}</td>
                  
                  

                  
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


function showTeam(){
  if($('.toggle').css("display")=="none")
    $('.toggle').show();
  else
    $('.toggle').hide();

}  
</script>
@endsection

