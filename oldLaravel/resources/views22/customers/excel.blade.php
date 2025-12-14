@extends('layoutExcel')

@section('content')
@if (count($model) > 0)
id;Nombre;pais; ciudad; celular1; celular2; email;Fuente;Estado;Fecha de Creación
@foreach($model as $item)
{{ $item->id }};{!! html_entity_decode($item->name) !!};{{ $item->country}}; {{ $item->city }};{{ $item->phone }};{{ $item->phone }};{{$item->email}}@if(isset($item->source));{{$item->source->name}}@endif;@if(isset($item->status_id)&&($item->status_id!="")&&(!is_null($item->status))){{$item->status->name}}@endif;{{ $item->created_at }}
@endforeach @endif  @endsection