@extends('layout')

@section('content')

<h1>Points by Week</h1>

@include('reports.weeks_by_team.graph') 
@include('reports.weeks_by_team.filter')  
  <!--  
  *
  *    Tabla reportes
  *
  -->

<table class="table table-striped table-hover table-responsive">
  <tr>
    <th>Week</th>
    @for($i=0; $i<count($users_graph); $i++)
        
    <th>
      {{$users_graph[$i]->name}}
    </th>
    @endfor
  </tr>
  @for($j=0; $j<count($weeks_array); $j++)
    
    <tr>
    <td>{{$weeks_array[$j][0]}} - {{$weeks_array[$j][1]}}</td>
    @for($i=0; $i<count($users_graph); $i++)
  
    <td>{{$data[$j][$i]}}</td>
    @endfor
  </tr> 
  @endfor
  
</table>


@endsection