@extends('layout')

@section('content')

<script>


Date.prototype.GetFirstDayOfWeek = function(date) {
    date = new Date(date);
    var day = date.getDay() || 7;  
    if( day !== 1 ) 
        date.setHours(-24 * (day - 1)); 
    return date;
    
}

Date.prototype.GetLastDayOfWeek = function(date) {
    date = new Date(date);
    var day = date.getDay() || 7;  
    if( day !== 1 ) 
        date.setHours(-24 * (day - 1));
    date.setDate(date.getDate() + 6); 
    return date;
}

function getThisWeek(){
  
  var firstday = new Date().GetFirstDayOfWeek(new Date());
  var lastday = new Date().GetLastDayOfWeek(new Date());
  
  $('#from_date').val(dateToString(firstday));
  $('#to_date').val(dateToString(lastday));

}


function getLastWeek(){
  var date = new Date();
  console.log(date);
  date.setDate(date.getDate()-7);  
  console.log(date);
  var firstday = new Date().GetFirstDayOfWeek(date);
  var lastday = new Date().GetLastDayOfWeek(date);
  
  $('#from_date').val(dateToString(firstday));
  $('#to_date').val(dateToString(lastday));

}

function dateToString(date){

  var dd = date.getDate();
  var mm = date.getMonth()+1; //January is 0!
  var yyyy = date.getFullYear();

  if(dd<10) {dd = '0'+dd} 

  if(mm<10) {mm = '0'+mm} 

  
  //alert(yyyy + '-' + mm + '-' + dd);
  return yyyy + '-' + mm + '-' + dd;
}

function getDate(interval){
  var date = new Date();

  console.log(date);

  date.setDate(date.getDate() + interval);
  console.log(date);

  return dateToString(date);
}


function getLastMonth(){
  var date = new Date();
  date.setDate(0);
  $('#to_date').val(dateToString(date));

  date.setDate(1);
  $('#from_date').val(dateToString(date));
    
}
  function update(){
    filter = $( "#filter option:selected" ).val();
    

    switch (filter){
      case "": $('#from_date').val(""); $('#to_date').val(""); break;
      case "0": $('#from_date').val(getDate(0)); $('#to_date').val(getDate(0)); break;
      case "-1": $('#from_date').val(getDate(-1)); $('#to_date').val(getDate(-1)); break;
      case "thisweek":getThisWeek(); break;
      case "-7":$('#from_date').val(getDate(-6)); $('#to_date').val(getDate(0)); break;
      case "-30":$('#from_date').val(getDate(-29)); $('#to_date').val(getDate(0)); break;
      case "lastweek":getLastWeek(); break;
      case "lastmonth":getLastMonth();break;

    }
  }
  </script>
	<h1>Projects</h1>                                   <!--     m_id,c,e,u,d,l) -->
  @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,3,1,0,0,0,0) == 1)
	 <div><a href="/projects/create">Create +</a></div>
  @endif

<div>
    <form action="/tasks/" method="GET">
      <select name="filter" class="custom-select" id="filter" onchange="update()">
        <option value="">select time</option>
        <option value="0" @if ($request->filter == "0") selected="selected" @endif>today</option>
        <option value="-1" @if ($request->filter == "-1") selected="selected" @endif>yesterday</option>
        <option value="thisweek" @if ($request->filter == "thisweek") selected="selected" @endif>this week</option>
        
        <option value="lastweek" @if ($request->filter == "lastweek") selected="selected" @endif>last week</option>
        <option value="lastmonth" @if ($request->filter == "lastmonth") selected="selected" @endif>last month</option>
        <option value="-7" @if ($request->filter == "-7") selected="selected" @endif>last 7 day</option>
        <option value="-30" @if ($request->filter == "-30") selected="selected" @endif>last 30 days</option>
        
      </select>
      
      <input class="input-date" type="date" id="from_date" name="from_date">
    <input class="input-date" type="date" id="to_date" name="to_date">

<!--  
*
*    Combo de proyectos
*
-->
      <select name="project_id" class="custom-select" id="project_id" onchange="submit();">
        <option value="">select a project</option>
       @foreach($model as $project)
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
          <option value="{{$item->id}}"  @if ($request->status_id == $item->id) onclick="" selected="selected" @endif>
            {{$item->name}}</option>
        @endforeach
       
      </select>
    <input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filter" >

    </form>
  </div>

	<div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead class="thead-dark">
                <tr>
                  <th>#</th>
                  <th>Proyect</th>
                  <th>Status</th>
                  <th>Fee</th>
                  <th>Limited</th>
                  <th>Weekly Pieces</th>
                  <th>Planned</th>
                  <th>Finished</th>
                  <th>No Finished</th>
                @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,3,0,0,1,0,0) == 1)  
                  <th>Edit</th>
                @endif
                </tr>
              </thead>
              <tbody>

                <?php 

                $sid=""?>
                @foreach($model as $item)
                @if($sid!==$item->status_id)
                <tr>
                  <td colspan="8">@if(isset($item->status_id))<strong>{{$item->getStatusName($sid)}}</strong>@endif</td>
                </tr>
                @endif
                <tr>
                  <td>{{ $item->id }}</td>
                  <td>
                    @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,3,0,1,0,0,0) == 1)  
                    <a class="proyect-{{$item->id}}" href="/projects/{{ $item->id }}">{{ $item->name }}</a>
                        @if ( $item->data_studio != null) 
                          <a class="badge" target="blank" href="{{ $item->data_studio }}" >Data studio</a>
                        @endif
                    @else
                    <a class="proyect-{{$item->id}}">{{ $item->name }}</a>
                    @endif 
<style type="text/css">
  .badge {
    background-color: #c1c1b2;
    color: black;
    display: inline-block;
    padding: .25em .4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

</style>
                  </td>
                  <td>@if(isset($item->status_id)){{ $item->status->name }}@endif</td>
                  <td>@if($item->type_id==1)$ {{ number_format( $item->budget, 0 ) }}@endif</td>
                  <td>@if($item->type_id==2)$ {{ number_format( $item->budget, 0 ) }}@endif</td>
                  
                  <td>{{ $item->weekly_pieces }}</td>
                  <td>{{ $item->getPlannedTask("this_month") }}</td>
                  <td>{{ $item->getFinishedTask("this_month") }}</td>


                @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,3,0,0,1,0,0) == 1)  
                  <td><a class="btn btn-sm btn-primary my-2 my-sm-0" href="/projects/{{ $item->id }}/edit">Edit</a></td>
                @endif 
                 <td><a class="btn btn-sm btn-primary my-2 my-sm-0" href="/metadata/{{ $item->id }}/create/3">onboarding</a></td>

                </tr>
                <?php $sid= $item->status_id;?>
 				@endforeach
              </tbody>
            </table>
          </div>
  
@endsection