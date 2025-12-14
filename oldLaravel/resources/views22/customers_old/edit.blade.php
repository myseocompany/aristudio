@extends('layout')
@section('title', 'Editar transaccion')
@section('content')
<h1>Editar transacción</h1>
<form method="POST" action="/transactions/{{$model->id}}/update">
{{ csrf_field() }}
 
  <div class="form-group">
   <label for="user_id">Cliente</label>
     <select name="user_id" id="user_id" class="form-control" >
        <option value="">Seleccione a cliente</option>
    @foreach($users as $item)
        <option value="{{$item->id}}" @if($model->user_id==$item->id) selected="selected" @endif>{{$item->name}}</option>
    @endforeach
    ?>
    </select>
  </div>
 
 
 <div class="form-group">
    <label for="name">Crédito</label>
    <input type="text" class="form-control" id="credit" name="credit" placeholder="Name" value="{{$model->credit}}">
  </div>
  
  <!-- Estado-->
  <div class="form-group">
   <label for="event_type_id">Tipos de obras</label>
     <select name="event_type_id" id="event_type_id" class="form-control" >
        <option value="">Seleccione los tipos de obras</option>
    @foreach($location_options as $item)
        <option value="{{$item->id}}" @if($model->event_type_id==$item->id) selected="selected" @endif>{{$item->name}}</option>
    @endforeach
    ?>
    </select>
  </div>

   <div class="form-group">
    <label for="name">Debito</label>
    <input type="text" class="form-control" id="debit" name="debit" placeholder="Name" value="{{$model->debit}}">
  </div>
  <div class="form-group">
   <label for="event_id">Obras</label>
     <select name="event_id" id="event_id" class="form-control" >
        <option value="">Seleccione la obras</option>
    @foreach($event_options as $item)
        <option value="{{$item->id}}" @if($model->event_id==$item->id) selected="selected" @endif>{{$item->name}}</option>
    @endforeach
    ?>
    </select>
  </div>

  <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection