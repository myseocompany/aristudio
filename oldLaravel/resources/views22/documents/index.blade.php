  @extends('layout')

  @section('content')

  <!-- Form -->


  <h2>Enter a new document</h2>
  @include('documents.createForm')

  <!-- End form -->


  <h1>Documents</h1>
  @if (session('status'))
  <div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
    {!! html_entity_decode(session('status')) !!}
  </div>
  @endif
  @if (session('statustwo'))
  <div class="alert alert-warning alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
    {!! html_entity_decode(session('statustwo')) !!}
  </div>
  @endif
  @if (session('statusthree'))
  <div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
    {!! html_entity_decode(session('statusthree')) !!}
  </div>
  @endif
  <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" id="documents-table">

   @if(count($model)>0)
   <table class="table table-striped">
    <thead>
      <tr>
        <th>Code</th>
        <th>Proyect</th>
        <th>Account</th>
        <th>Date</th>
        <th>Description</th>
        <th>Link</th>
        <th>Debits</th>
        <th>Credits</th>
        <th>Actions</th>
      </tr> 
    </thead>
    <tbody>
      <?php $count=0;
      $fee_sum=0;
      $project_sum=0; ?>
      @foreach ($model as $item)
      <tr>
        <td><a href="/documents/{{$item->id}}">{{$item->internal_id}}</td></a>

        <td>
          @if (isset ($item->project) )
          <a href="projects/{{$item->project->id}}">
            {{$item->project->name}}
          </a>
          @endif
        </td>
        <td>
          @if (isset ($item->account))
          {{$item->account->name}}
          @endif
        </td>

        <td>{{$item->date}}</td>
        <td>{{$item->description}}</td>  
        <td>@if(isset($item->url))<a href="{{$item->url}}">Enlace</a>@endif</td>
        <td>{{ number_format($item->debit, 0)}}</td>
        <td>{{ number_format($item->credit, 0)}}</td>
        <td>
         <a href="/project_documents/{{ $item->id }}/edit">
          <span class="btn btn-sm btn-warning my-2 my-sm-0" title="Edit" aria-hidden="true">
            <span class="fa fa-pencil"></a>
              <a href="/documents/{{ $item->id }}/show"><span class="btn btn-sm btn-success my-2 my-sm-0" aria-hidden="true"><span class="fa fa-eye" title="Consult"></span></span></a>
              {{-- delete --}}
              <a href="/project_documents/{{$item->id}}/delete"><span class="btn btn-sm btn-danger fa fa-trash-o" aria-hidden="true" title="Eliminar"></span></a>
            </td>
          </tr>
          <?php 
          $fee_sum += $item->fee_budget;
          $project_sum += $item->project_budget;
          $count++;
          ?>
          @endforeach

        </tbody>
      </table>

      @endif

    </div>
    @endsection