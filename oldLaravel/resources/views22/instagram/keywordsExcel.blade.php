@extends('layoutExcel')

@section('content')
@foreach ($keyResults as $keyword)
{{$keyword[0]}},{{$keyword[1]}}
@endforeach
@endsection
