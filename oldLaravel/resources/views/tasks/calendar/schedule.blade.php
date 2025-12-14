@extends('layout')

@section('content')
<br>
<h1>Tasks</h1>
@include('tasks.calendar.filter')
@include('tasks.createForm')


<link rel="stylesheet" href="{{ asset('/laravel/resources/views/tasks/calendar/core/main.css')}}">
<link rel="stylesheet" href="{{ asset('/laravel/resources/views/tasks/calendar/daygrid/main.css')}}">
<link rel="stylesheet" href="{{ asset('/laravel/resources/views/tasks/calendar/list/main.css')}}">
<link rel="stylesheet" href="{{ asset('/laravel/resources/views/tasks/calendar/timegrid/main.css')}}">

<script src="{{ asset('/laravel/resources/views/tasks/calendar/core/main.js')}}" defer></script>

<script src="{{ asset('/laravel/resources/views/tasks/calendar/interaction/main.js')}}" defer></script>

<script src="{{ asset('/laravel/resources/views/tasks/calendar/daygrid/main.js')}}" defer></script>
<script src="{{ asset('/laravel/resources/views/tasks/calendar/list/main.js')}}" defer></script>
<script src="{{ asset('/laravel/resources/views/tasks/calendar/timegrid/main.js')}}" defer></script>

<!--Funcionalidad y uso de Fullcalendar-->
<?php $date = Carbon\Carbon::now();
?>
<script>
        document.addEventListener('DOMContentLoaded', function() {
          var calendarEl = document.getElementById('calendar');
      
          var calendar = new FullCalendar.Calendar(calendarEl, {
            defaultDate: new Date("{{$date->format('Y-m-d')}}"),
            plugins: [ 'dayGrid', 'interaction', 'timeGrid', 'list'],
            //defaultView:'timeGridWeek'

            header:{
                left:'prev, next, today',
                center: 'title',
                right: 'dayGridMonth, timeGridWeek, timeGridDay'
            },
            /*Click en las sesiones*/
            eventClick:function(info){
              $("#btnAdd").css("display","none");
              $("#btnUpdate").css("display","block");
              $("#btnDelete").css("display","block");

              $("#task_name").val(info.event.extendedProps.name);
              $("#task_project_id").val(info.event.extendedProps.project_id);
              $("#task_status_id").val(info.event.extendedProps.status_id);
              $("#task_user_id").val(info.event.extendedProps.user_id);
              $("#task_priority").val(info.event.extendedProps.priority);

              $("#task_not_billing").val(info.event.extendedProps.not_billing);
              $("#task_points").val(info.event.extendedProps.points);
              $("#task_file").val(info.event.extendedProps.file);
              $("#task_url_finished").val(info.event.extendedProps.url_finished);
              $("#task_description").val(info.event.extendedProps.task_description);

              $("#id").val(info.event.id);
              $("#task_name").val(info.event.title);

              var date = new Date(info.event.extendedProps.due_date);
              month = date.getMonth()+1;
              day = date.getDate();
              year = date.getFullYear();

              //minuts = date.getMinutes();
              //hour = date.getHours();

              month = (month<10)?"0"+month:month;
              day = (day<10)?"0"+day:day;

              //minuts = (minuts<10)?"0"+minuts:minuts;
              //hour = (hour<10)?"0"+hour:hour;

              //hourrio = (hour+":"+minuts);

              $("#task_due_date").val(year + "-" + month + "-" + day);
              //$("#hour").val(horario);
              $("#exampleModal").modal();
            },

            
            /*Click en las fechas*/
            dateClick:function(info){
              limpiarFormulario();

              $("#task_due_date").val(info.dateStr);

              $("#btnAdd").css("display","block");
              $("#btnUpdate").css("display","none");
              $("#btnDelete").css("display","none");

              $("#exampleModal").modal('toggle');
              $("#date").val(info.dateStr);

            },
            editable:true,
              eventDrop:function(calEvent){
                console.log("hola");
                $("#task_name").val(calEvent.event.extendedProps.name);
                $("#task_project_id").val(calEvent.event.extendedProps.project_id);
                $("#task_status_id").val(calEvent.event.extendedProps.status_id);
                $("#task_user_id").val(calEvent.event.extendedProps.user_id);
                $("#task_priority").val(calEvent.event.extendedProps.priority);

                $("#task_not_billing").val(calEvent.event.extendedProps.not_billing);
                $("#task_points").val(calEvent.event.extendedProps.points);
                $("#task_file").val(calEvent.event.extendedProps.file);
                $("#task_url_finished").val(calEvent.event.extendedProps.url_finished);
                $("#task_description").val(calEvent.event.extendedProps.task_description);

                $("#id").val(calEvent.event.id);
                $("#task_name").val(calEvent.event.title);

                var date = new Date(calEvent.event.start);
                month = date.getMonth()+1;
                day = date.getDate();
                year = date.getFullYear();
                month = (month<10)?"0"+month:month;
                day = (day<10)?"0"+day:day;

                $("#task_due_date").val(year + "-" + month + "-" + day);
                //$("#task_due_date").val(calEvent.dateStr);
                objEvent = getDataGUI();
                sendRequest('/'+$("#id").val(),objEvent, "POST", true);
            },
            events: [
              <?php
                foreach($model as $item){
                  //dd($item);
              ?>
              {
                extendedProps: {
                  name: "{{ $item['name']}}",
                  project_id: "{{ $item['project_id']}}",
                  user_id: "{{ $item['user_id']}}",
                  status_id: "{{ $item['status_id']}}",

                  priority: "{{ $item['priority']}}",
                  due_date: "{{ $item['due_date']}}",
                  not_billing: "{{ $item['not_billing']}}",
                  points: "{{ $item['points']}}",
                  file: "{{ $item['file']}}",
                  url_finished: "{{ $item['url_finished']}}"
                },

                id: "{{ $item['id']}}",
                title: "{{ $item['name']}}",
                start: "{{ $item['due_date']}}",
                
                textColor: "black",
                color:@if(isset($item->status))'{{$item->status->background_color}}' @else "white" @endif
              },
              <?php
                }
              ?>
            ]
          });
          calendar.render();


          $("#btnAdd").click(function(){
            objEvent = getDataGUI();
            sendRequest('',objEvent, "GET");
          })
          $("#btnDelete").click(function(){
            objEvent = getDataGUI();
            sendRequest('/'+$("#id").val(),objEvent, "GET");
          })
           $("#btnUpdate").click(function(){
            objEvent = getDataGUI();
            sendRequest('/'+$("#id").val(),objEvent, "POST");
          })
          function getDataGUI(){
            newEvent={
              name : $("#task_name").val(),
              status_id : $("#task_status_id").val(),
              project_id : $("#task_project_id").val(),
              user_id : $("#task_user_id").val(),
              priority : $("#task_priority").val(),
              due_date : $("#task_due_date").val(),
              not_billing : $("#task_not_billing").val(),
              points : $("#task_points").val(),
              file : $("#task_file").val(),
              url_finished : $("#task_url_finished").val(),
              description : $("#task_description").val(),
              '_token':$("input[name=_token]").val()
            }
            return newEvent;
          }

          function sendRequest(action, objEvent, method, modal){
            console.log(objEvent);
            $.ajax({
              type : method,
              url : "{{url('/task_from_calendar')}}" + action,
              data:objEvent,
              success:function(msg){
                calendar.refetchEvents();
                if(!modal){
                  $("#exampleModal").modal('toggle');
                }
              },
              error:function(){
                alert("Error");
              }
            });
          }
          function limpiarFormulario(){
            $("#task_name").val("");
            $("#task_status_id").val("");
            $("#task_project_id").val("");
            $("#task_user_id").val("");
            $("#task_priority").val("");
            $("#task_due_date").val("");
            $("#task_not_billing").val("");
            $("#task_points").val("");
            $("#task_file").val("");
            $("#task_url_finished").val("");
            $("#task_description").val("");
          }
        });
      </script>
      
<div id="calendar"></div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Task</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

          <div class="row">
            <div class="col-md">
              <div class="form-group">
                <input type="hidden" name="id" id="id">
                <label for="task_name">Name</label>
                <input type="text" class="form-control" id="task_name" name="task_name" placeholder="Name" required="required">
              </div>
              <div class="form-group">
                <label for="user_id">Project</label>
                <select name="task_project_id" id="task_project_id" class="form-control" required="required">
                  <option value="">Select a Project</option>
                
                @foreach ($projects as $project)
                    <option value="{{$project->id}}"@if ($project->id == $request->project_id) selected="selected" @endif>{{$project->name}}</option>
                @endforeach
                </select>
              </div>

              <div class="form-group">
                <label for="task_priority">Priority</label>    
                <input type="text" class="form-control" id="task_priority" name="task_priority" placeholder="Priority" >
              </div>

              <div class="form-group">
                <label for="task_due_date">Due Date</label>
                <input type="date" class="form-control" id="task_due_date" name="task_due_date" placeholder="YYYY/MMM/DD" required="required" value="<?php echo date('Y-m-d h:m:s');?>">
              </div>

              <div class="form-group row">
                <label for="task_not_billing" class="col-6">Not Billing</label>
                <input type="checkbox" class="form-control col-1" id="task_not_billing" name="task_not_billing">
              </div> 


            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="task_user_id">User</label>
                <select name="task_user_id" id="task_user_id" class="form-control">
                  <option value="">Select a User</option>
                @foreach ($users as $user)
                    <option value="{{$user->id}}" @if ($user->id == $request->user_id) selected="selected" @endif>{{$user->name}}</option>
                @endforeach
                </select>
              </div>

              <div class="form-group">
               <label for="task_status_id">Status</label>
                 <select name="task_status_id" id="task_status_id" class="form-control" >
                @foreach($task_status as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
                ?>
                </select>
              </div>

              <div class="form-group">
                <label for="task_points">Points</label>    
                <input type="text" class="form-control" id="task_points" name="task_points" placeholder="Points">
              </div>



              <div class="form-group">
                <label for="task_file">File</label>
                <input type="file" class="form-control" id="task_file" name="task_file" placeholder="Name">
              </div>

              <div class="form-group">
              <label for="task_url_finished">Url Finished Task </label>
                <input class="form-control" name="task_url_finished" id="task_url_finished" placeholder="Url" value="" >
              </div>
            </div>
            <div class="form-group col-md-12">
              <label for="task_description">Description</label>
              <textarea class="form-control" name="task_description" id="task_description" cols="30" rows="5"></textarea>
            </div>
            <input type="hidden" name="from" id="from" class="form-control" value="project">

        </div>
      </div>
      <div class="modal-footer">
        <button id="btnCancel" class="btn btn-warning" data-dismiss="modal">Cancel</button>
        <button id="btnAdd" class="btn btn-primary">Add</button>
        <button id="btnUpdate" class="btn btn-success">Update</button>
        <button id="btnDelete" class="btn btn-danger">Delete</button>
        
      </div>
    </div>
  </div>
</div>

@endsection