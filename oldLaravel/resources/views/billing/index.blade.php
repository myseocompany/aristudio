@extends('layout')


@section('content') 
<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>Codigo</th>
      <th>Descripcion</th>
      <th>Documentos</th>
      <th>Proyect</th>
      <th>Debitos</th>
      <th>Créditos</th>
      <th>Acciones</th>

    </tr>

  </thead>
  <tbody>
    <?php $cont=0; ?>
    @foreach($model as $item)
    <tr>
      <td>{{$item->internal_id}}</td>
      <td>{{$item->project->name}}</td>
      <td>{{$item->account->name}}</td>
      <td>{{$item->description}}</td>
      <td>@if(isset($item->url))<a href="{{$item->url}}">Enlace</a>@endif</td>
      
      <td class="text-right">{{ number_format($item->debit, 0)}}</td>
      <td class="text-right">{{ number_format($item->credit, 0)}}</td>
      <td>
        <a class="btn btn-sm btn-primary my-2 my-sm-0" href="/project_documents/{{$item->id}}/edit">Editar</a> | 
        <a class="btn btn-sm btn-primary my-2 my-sm-0" href="/project_documents/{{$item->id}}/delete">Eliminar</a></td>
    <?php $cont += $item->credit ?>
    </tr>
    @endforeach
  </tbody>
</table>

{{$cont}}

<h2>Documents</h2>
<form method="POST" action="/billing" enctype="multipart/form-data">
    
  <div class="row">{{ csrf_field() }}
    <div class="col-md-6">
      <div class="form-group col-md-12">
        <label for="account_id" class=""><strong>Account</strong></label>
        <select name="account_id" id="account_id" class="col-md-12 form-control" >

          <option value="">Select account...</option>
          @foreach ($accounts as $item)
          <option value="{{$item->id}}">{{$item->name}}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group col-md-12">
        <label for="document_type_id" class=""><strong>Document Type</strong></label>
        <select name="document_type_id" id="document_type_id" class="col-md-12 form-control" >

          <option value="">Select document type...</option>
          @foreach ($document_types as $item)
          <option value="{{$item->id}}">{{$item->name}}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group col-md-12">
        <label for="debit"><strong>Debit</strong></label>
        <input class="form-control" type="text" name="debit"> 
      </div>
      <div class="form-group col-md-12">
      
        <label for="credit"><strong>Credit</strong></label>
        <input class="form-control" type="text" name="credit"> 
      </div>

      <div class="form-group col-md-12">
        <label for="project_id" class=""><strong>Project</strong></label>
        <select name="project_id" id="project_id" class="col-md-12 form-control" >

          <option value="">Select project...</option>
          @foreach ($projects as $item)
          <option value="{{$item->id}}">{{$item->name}}</option>
          @endforeach
        </select>
      </div>
         
    </div>
    <div class="col-md-6">
      <div class="form-group">
    <div class="container">
      <div class="row">
        
        <div class="col-md-12">
          <label for="file" ><strong>Select file</strong></label>
        </div>

        <div class="col-md-12">
          <input type="file" class="form-control" id="file" name="file" placeholder="email" >
        </div>
        <div class="col-md-12">
        <label for="date"><strong>Date</strong></label>
        <input class="form-control" type="date" name="date"> 
        </div>
        <div class="col-md-12">
        <label for="internal_id"><strong>Internal ID</strong></label>
        <input class="form-control" type="text" name="internal_id"> 
        </div>
        <div class="col-md-12">
        <label for="description"><strong>Descripcion</strong></label>
        <input class="form-control" type="text" name="description"> 
        </div>
      </div>
    </div>
  </div>
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md-12">
      
      <input type="submit" class="btn btn-sm btn-primary glyphicon glyphicon-pencil" aria-hidden="true" value="submit">
    </div>
  </div>

</form>



@endsection



