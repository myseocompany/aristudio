<form method="POST" action="/documents" enctype="multipart/form-data">
    
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
      <div class="form-group col-md-12" id="hidden1" >
        <label for="debit"><strong>Debit</strong></label>
        <input class="form-control" type="text" name="debit"> 
      </div>
      <div class="form-group col-md-12" id="hidden2">
      
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
        <div class="col-md-12" id="hidden3">
        <label for="internal_id"><strong>Internal ID</strong></label>
        <input class="form-control" type="text" name="internal_id"> 
        </div>
        <div class="col-md-12">
        <label for="description"><strong>Descripcion</strong></label>
        <textarea class="form-control" type="text" name="description" rows="3"></textarea>
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