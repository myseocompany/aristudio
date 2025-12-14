@extends('layouts.agile_modi')
@section('content')
<div class="page add-tasks" data-name="add-lead">

  <!-- Header -->
  <div class="navbar">
    <div class="navbar-inner align-items-center">
      @section('content-back-page')
        <div class="left back-page">
          <a href="#" onclick="openView('/');" >
            <i class="fas fa-angle-left"></i>
          </a>
        </div>
      @endsection
      <div class="page-header-title">
        <h4>Parrilla</h4>
      </div>
    </div>
  </div>
  <!-- /Header -->
  
  <div class="page-content">
    <div class="block"> 
      <div class="container">


		<table class="table table-hover">
		
			<thead>
				<tr>
					<th></th>
					<th>Visual detail / Text</th>
					<th>Copy</th>
					<th>Caption ( en la publicación )</th>
				</tr>
			</thead>
			<tbody>
				@foreach($tasks as $task)
				<tr>
					<td>
						@if(isset($task->file_url))
							<?php
								$mystring = $task->file_url;
								$findme   = 'mp4';
								$pos = strpos($mystring, $findme);
								if ($pos === false) {
							?>
								<img width="200" src="/laravel/storage/app/public/files/{{$task->file_url}}">
							<?php } ?>

						@endif
					</td>
					<td><label >{{$task->description}}</label></td>
					<td><label >{{$task->copy}}</label></td>
					<td><label >{{$task->caption}}</label></td>
				</tr>
				@endforeach
			</tbody>
		</table>
	  </div>
    </div>
  </div>
  <!-- Page Content end -->

</div>
<!-- App end -->


<style type="text/css">
	.form-control{
		height: 100% !important; 
	}

	.table td, .table th {
		padding: 0.75rem;
		vertical-align: top;
		border-top: 1px solid #e9ecef;
		overflow: hidden;
	}
</style>
@endsection