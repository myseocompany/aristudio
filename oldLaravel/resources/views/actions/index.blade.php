@extends('layout')

@section('content')

<h1>Acciones</h1>
@if($model instanceof \Illuminate\Pagination\LengthAwarePaginator )
Registro <strong>{{ $model->currentPage()*$model->perPage() - ( $model->perPage() - 1 ) }}</strong>  a <strong>{{ $model->getActualRows}}</strong> de <strong>{{$model->total()}}</strong>
@endif
<form action="/actions/" method="GET" id="filter_form">
  		 <select name="filter" class="custom-select" id="filter" onchange="update()">
        <option value="">Seleccione tiempo</option>
        <option value="0" @if ($request->filter == "0") selected="selected" @endif>hoy</option>
        <option value="-1" @if ($request->filter == "-1") selected="selected" @endif>ayer</option>
        <option value="thisweek" @if ($request->filter == "thisweek") selected="selected" @endif>esta semana</option>
        
        <option value="lastweek" @if ($request->filter == "lastweek") selected="selected" @endif>semana pasada</option>
        <option value="lastmonth" @if ($request->filter == "lastmonth") selected="selected" @endif>mes pasado</option>
      	<option value="currentmonth" @if ($request->filter == "currentmonth") selected="selected" @endif>este mes</option>
      	<option value="-7" @if ($request->filter == "-7") selected="selected" @endif>ultimos 7 dias</option>
        <option value="-30" @if ($request->filter == "-30") selected="selected" @endif>ultimos 30 dias</option>
        
      </select>
      <input class="input-date" type="date" id="from_date" name="from_date" onchange="cleanFilter()" value="{{$request->from_date}}">
      <input class="input-date" type="date" id="to_date" name="to_date" onchange="cleanFilter()" value="{{$request->to_date}}">

     {{-- Combo de estados --}}
<select name="type_id" class="slectpicker custom-select" id="type_id" onchange="submit();">
        <option value="">Tipo acción...</option>
        @foreach($action_options as $item)
          <option value="{{$item->id}}" @if ($request->type_id == $item->id) selected="selected" @endif>
             {{ $item->name }}
            
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

      <input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filtrar" >
  	</form>
<table class="table table-striped">
	<thead>
		<td>id</td>
		<td>Fecha</td>
		<td>Cliente</td>

    <td>Estado</td>
		<td>Tipo Accion</td>
	</thead>
	<tbody>
	@foreach ($model as $item)
		<tr>
			<td>{{$item->id}}</td>
			<td>{{$item->created_at}}</td>
			<td><a href="/customers/{{$item->customer_id}}/show">{{$item->getCustomerName()}}</a></td>
      <td>
      	@if(isset($item->customer->status))
      	{{$item->customer->status->name}}
		@endif
      </td>
      
			<td>{{$item->getTypeName()}}</td>
		</tr>
	@endforeach
	</tbody>
</table>
@if($model instanceof \Illuminate\Pagination\LengthAwarePaginator )

{{ $model->appends(request()->input())->links() }}
@endif
@endsection