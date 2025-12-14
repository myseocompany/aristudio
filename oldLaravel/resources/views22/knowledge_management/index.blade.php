@extends('layout')

@section('content')
<h1 class="text-center">SOPs, recipes and templates</h1>
  
    <!--<div><a href="/knowledge_managementes/create">Crear <i class="fa fa-plus-square"></i></a></div>-->
    <div class="table-responsive">
      <div id="pdf-preview"></div>
      <table class="table table-striped">
        <thead>
        </thead>
        <tbody>
          @foreach($types as $type)
          <tr>
            <td colspan="12"><strong>{{ $type->name }}</strong></td>
          </tr>
            @foreach($model as $knowledge_management)
              @if($type->id==$knowledge_management->type_id)
                <tr>
                  <td>{{ $knowledge_management->id }}</td>
                  <td>{{ $knowledge_management->name }}</td>
                  <td><a href="{{$knowledge_management->url}}" class="btn btn-primary">Open</a></td>
                </tr>
              @endif
            @endforeach
          @endforeach
        </tbody>
      </table>
    </div>
@endsection