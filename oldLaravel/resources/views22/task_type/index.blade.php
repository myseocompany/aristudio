@extends('layout')

@section('content')
<h1>Task Types</h1>

<div>
  <form action="/task_types" method="POST">
    {{ csrf_field() }}
    <select id="parent_id" name="parent_id">
      <option>
        Seleccione una opcion
      </option>
      @foreach($options as $item)
      <option value="{{$item->id}}">{{$item->name}}</option>
      @endforeach
    </select>

    <input type="" name="name">
    <input type="submit" name="">
    
  </form>
</div>
<div>

  <table>
    <tr>
      <th>id</th>
      <th>name</th>
      <th>operation</th> 
    </tr>

    @foreach($model as $item)
    <tr>
      <td>{{$item->id}}</td>
      <td><a href="/task_types/?parent_id={{$item->id}}">{{$item->name}}</a></td>
      <td>
        <a href="/task_types/{{$item->id}}/edit">edit</a> - 
        <a href="/task_types">delete</a>
      </td>
    </tr>
    @endforeach
  </table>
</div>
@endsection