@extends('layout')

@section('content')

<h1>Buscador de palabras claves</h1>

@foreach ($keyResults as $keyword)
<div>Palabra clave: <span>{{$keyword[0]}}</span></div>

<div>Cantidad de resultados: <span>{{$keyword[1]}}</span></div>
@endforeach

@endsection
