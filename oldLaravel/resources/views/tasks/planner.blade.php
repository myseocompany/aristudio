@extends('layout')




@section('content')
<nav class="navbar navbar-expand-md navbar-white fixed-top bg-white" id="center-nav">
  <div class="container">
          
    <div id="row-form">
      <div id="name-field">
        <input type="text" name="timer-name" id="timer-name" class="timer-input form-control" placeholder="What are you working on?" @if(isset($actual_task)) value="{{$actual_task->name}}" @endif >
      </div>
      
        <div id="timer-project">
          <div class="row">
            <div class="form-group col-md-6">
              @if(isset($projects))
                <select name="timer-project_id" class="form-control" id="timer-project_id" >
                  <option value="">select a project</option>
                 @foreach($projects as $item)
                    <option value="{{$item->id}}" @if(isset($actual_task)&&($actual_task->project_id==$item->id)) selected @endif >
                    <?php echo $item->name; ?>
                    </option>
                  @endforeach
                </select>
              @endif
            </div>
            
            <div class="form-group col-md-6">
            @if(isset($users))
              <select name="timer-user_id" class="form-control" id="timer-user_id" >
                <option value="">select a user</option>
               @foreach($users as $item)
                  <option value="{{$item->id}}" @if(isset($actual_task)&&($actual_task->user_id==$item->id)) selected @endif >
                  <?php echo $item->name; ?>
                  </option>
                @endforeach
              </select>
            @endif
            </div>
          </div>
        </div>

      <div id="button-field">
        

        <svg id="program-button" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 40 40" version="1"><g fill-rule="evenodd" fill="none"><g fill="#4bc800">
          <path onclick="programTask();" d="M20 0C9 0 0 9 0 20 0 31 9 40 20 40 31 40 40 31 40 20 40 9 31 0 20 0ZM17 23.4L13.1 19.4C12.5 18.9 11.5 18.9 10.9 19.4 10.4 20 10.4 21 10.9 21.6L15.9 26.6C16.5 27.1 17.5 27.1 18.1 26.6L29.1 15.6C29.6 15 29.6 14 29.1 13.4 28.5 12.9 27.5 12.9 26.9 13.4L17 23.4Z"></path></g>
        </g>
        </svg>
      </div>
      <div id="timer-status">
      	


      </div>
      <div id="chronotime"></div>
  </div>
          

          
  </div>
</nav>
<br>
<script type="text/javascript">
  var id_task = "";
  @if(isset($actual_task))
  	id_task = {{$actual_task->id}}
  @endif

  function programTask(){
    $("#program-button").css("display","none");

    end_point = "/planner";
    
    name = $("#timer-name").val();
    if(name!=""){
    	
	    data = {
	        "name": name,
	        "user_id": $("#timer-user_id").val(),
	        "project_id": $("#timer-project_id").val()
	    };

	    console.log(data);
	    var posting = $.post(end_point, data);
	    //$('#timer-status').html('...enviando');
	    posting.done(function( data ) {
	        console.log("enviado");
	        console.log(data);
          showTask(data);
	        id_task = data;
          $("#program-button").css("display","block");
	        //$('#timer-status').html(data);
	      });
	    posting.fail(function(XMLHttpRequest, textStatus, errorThrown){
	      console.log(XMLHttpRequest.responseText);
	      console.log(textStatus);
	      console.log(errorThrown);
	      $("#program-button").css("display","block");
	      //$('#timer-status').html(XMLHttpRequest.responseText);
	    });
    }
  }
  listenEnter();
  function listenEnter(){
    var input = document.getElementById("timer-name");

    // Execute a function when the user releases a key on the keyboard
    input.addEventListener("keyup", function(event) {
      // Number 13 is the "Enter" key on the keyboard
      if (event.keyCode === 13) {
        // Cancel the default action, if needed
        event.preventDefault();
        // Trigger the button element with a click
        programTask();
      }
    });
  }

  

  function showTask(task){
    list = $("#timer-list");
    str = "<div class='row'>";

    str += "<div class='col-sm-12 col-md-3'>";
    str += task.due_date.date.substring(0,19);
    str += "</div>";
    
    str += "<div class='col-sm-12 col-md-4'>";
    str += task.name;
    str += "</div>";
    
    str += "<div class='col-sm-12 col-md-1'>[";
    str += "";
    str += "]</div>";
    str += "<div class='col-sm-12 col-md-2'>";
    str += task.project_name;
    str += "</div>";

    str += "<div class='col-sm-12 col-md-1'>";
    str += "<span class='planner-status' style='background-color:"+task.status_background_color+"'>";
    str += task.status_name;
    str += "</span>";
    str += "</div>";

    str += "<div class='col-sm-12 col-md-1'>";
    str += "<a href='/tasks/"+task.id+"/edit'>edit</a>"
    str += "</div>";

    str += "</div>";
    
    list.prepend(str);

  }


var startTime = 0
var start = 0
var end = 0
var diff = 0
var timerID = 0

function chrono(){
	end = new Date()
	diff = end - start
	diff = new Date(diff);
	
	//console.log("ini " + start +" end " + end + " diff " + diff);
	


	var msec = diff.getMilliseconds();
	var sec = diff.getSeconds();
	var min = diff.getMinutes();
	var hr = diff.getHours();
	if (hr==19) hr=0;

	if (min < 10){
		min = "0" + min
	}
	if (sec < 10){
		sec = "0" + sec
	}
	if(msec < 10){
		msec = "00" +msec
	}
	else if(msec < 100){
		msec = "0" +msec
	}
	document.getElementById("chronotime").innerHTML = hr + ":" + min + ":" + sec + ":" + msec
	timerID = setTimeout("chrono()", 10)
}
function chronoStart(){
	
	start = new Date()
	chrono()
}
function chronoContinue(){
	
	start = new Date()-diff
	start = new Date(start)
	chrono()
}
function chronoReset(){
	document.getElementById("chronotime").innerHTML = "0:00:00:000"
	start = new Date()
}
function chronoStopReset(){
	document.getElementById("chronotime").innerHTML = "0:00:00:000"
	
}
function chronoStop(){
	
	clearTimeout(timerID)
}
 
function restartButton(tid, name, pid){
  //$('#task_'+tid).hide();
  $('#timer-name').val(name);
  $('#timer-project_id').val(pid);
  starTask();
}

//-->
</script>
<h1>Timer</h1>
<div id="timer-list">
	@foreach($model as $item)
	    <div class='row' id="task_{{$item->id}}">
		    <div class='col-sm-12 col-md-3'>{{$item->due_date}}</div>
		    <div class='col-sm-12 col-md-4'>{{$item->name}}</div>
		    <div class='col-sm-12 col-md-1'>[{{round($item->points,4)}}]</div>
		    <div class='col-sm-12 col-md-2'>@if(isset($item->project)){{$item->project->name}}@endif </div>
		    
        <div class='col-sm-12 col-md-1'>
              

          @if(isset($item->status))
            <span class="planner-status" style="background-color:{{$item->status->background_color}}"> 
          {{$item->status->name}}
            </span>
          @endif 
          
        </div>
        <div class='col-sm-12 col-md-1'>
          
          <a href='/tasks/{{$item->id}}/edit'>edit</a>
        </div>
	    </div>
	    @endforeach
</div>

@endsection