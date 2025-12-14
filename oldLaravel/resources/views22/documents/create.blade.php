@extends('layout')

@section('content')
<h1>Create Document</h1>
<form method="POST" action="/documents">
{{ csrf_field() }}
{{-- type_id --}}
  <div class="form-group">
    <label for="document_types">Document Type:</label>
    <select name="type_id" id="type_id" class="form-control">
      <option value="">Select...</option>
      @foreach ($document_types as $item)
      <option value="{{ $item->id }}">{{  $item->name }}</option>
      @endforeach
    </select>
  </div>
{{-- end --}}
  <div class="form-group">
    <label for="name">Internal Code</label>
    <input type="text" class="form-control" id="internal_id" name="internal_id" placeholder="Code" required="required">
  </div>
  <div class="form-group">
    <label for="date">Date</label>    
    <input type="date" class="form-control" id="date" name="date" required="required">
  </div>
  <div class="form-group">
    <label for="budget">Amount</label>
    <input type="text" class="form-control" id="amount" name="amount" placeholder="Amount">    
  </div>
  <div class="">
    <label for="description">Description</label>
    <textarea name="description" id="description" class="form-control" cols="30" rows="10"></textarea>
  </div>
  {{-- Inc_account_id --}}
    <div class="form-group">
    <label for="accounts">Increment Account:</label>
    <select name="inc_account_id" id="inc_account_id" class="form-control">
      <option value="">Select...</option>
      @foreach ($accounts as $item)
      <option value="{{ $item->id }}">{{  $item->name }}</option>
      @endforeach
    </select>
  </div>
  {{-- end --}}
  {{-- Dec_account_id --}}
  <div class="form-group">
    <label for="accounts">Decrement Account:</label>
    <select name="dec_account_id" id="dec_account_id" class="form-control">
      <option value="">Select...</option>
      @foreach ($accounts as $item)
      <option value="{{ $item->id }}">{{  $item->name }}</option>
      @endforeach
    </select>
  </div>
  {{-- end --}}
  {{-- project --}}
    <div class="form-group">
    <label for="project_id">Project</label>
    <select name="project_id" id="project_id" class="form-control" required="">
      <option value="">Select...</option>
    @foreach ($projects as $item)
        <option value="{{$item->id}}">{{$item->name}}</option>
    @endforeach
    </select>
  </div>
  {{-- end --}}
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection