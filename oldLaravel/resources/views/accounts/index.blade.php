@extends('layout')

@section('content')

<div>Home / <a href="/accounts">Accounts</a> </div>
<h1>Accounts</h1>
<div id="task-table" class="table-wrapper-scroll-y my-custom-scrollbar">
  
 	<div class="table-responsive">
		<table class="table table-striped">
			@foreach($model as $item)
			<tr>
				<td>{{$item->id}}</td>
				<td><a href="/accounts/?parent_id={{$item->id}}">{{$item->name}}</a></td>
			</tr>
			@endforeach
		</table>
	</div>
</div>
@endsection