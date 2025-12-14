@extends('layout')

@section('content')
<h1>Edit Project Document</h1>
<form method="POST" action="/project_documents/{{$model->id}}/update" enctype="multipart/form-data">
    {{ csrf_field() }}
  <div class="row">
    <div class="col-md-6">
      <div class="form-group col-md-12">
        <label for="account_id" class=""><strong>Account</strong></label>
        <select name="account_id" id="account_id" class="col-md-12 form-control" >

          
          @foreach ($accounts as $item)         
          <option value="{{$item->id}}" @if($item->id==$model->account_id) selected="selected" @endif>{{$item->name}}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group col-md-12">
        <label for="document_type_id" class=""><strong>Document Type</strong></label>
        <select name="document_type_id" id="document_type_id" class="col-md-12 form-control" >

          @foreach ($document_types as $item)
          <option value="{{$item->id}}" @if($item->id==$model->type_id) selected="selected" @endif>{{$item->name}}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group col-md-12">
        <label for="debit"><strong>Debit</strong></label>
        <input class="form-control" type="text" name="debit" value="{{$model->debit}}">
      </div>
      <div class="form-group col-md-12">
      
        <label for="credit"><strong>Credit</strong></label>
        <input class="form-control" type="text" name="credit" value="{{$model->credit}}">
      </div>
         
    </div>
    <div class="col-md-6">
      <div class="form-group">
    <div class="container">
      <div class="row">
        
         <div class="col-md-12">
          <label for="file" ><strong>Seleccione el archivo</strong></label>
        </div>

        <div class="col-md-12">
          <input type="file" class="form-control" id="file" name="file" placeholder="email" >
        </div>

        <div class="col-md-12">
        <label for="date"><strong>Date</strong></label>
        <input class="form-control" type="date" name="date" value="{{$model->date}}">
        </div>
        <div class="col-md-12">
        <label for="internal_id"><strong>Internal ID</strong></label>
        <input class="form-control" type="text" name="internal_id" value="{{$model->internal_id}}">
        </div>
        <div class="col-md-12">
        <label for="description"><strong>Descripcion</strong></label>
        <input class="form-control" type="text" name="description" value="{{$model->description}}">
        </div>


        <input type="hidden" id="project_id" name="project_id" value="{{$model->project_id}}">
        <input type="hidden" id="document_id" name="document_id" value="{{$model->id}}">

      </div>
    </div>
  </div>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection