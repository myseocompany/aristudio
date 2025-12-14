@extends('layout')

@section('content')
<h1>Tasks Import</h1>


<h2>File upload</h2>
	<form action="/tasks_import/bulk_store" method="post" enctype="multipart/form-data">
		  {{ csrf_field() }}

    Select a file <code>import_tasks.csv</code>
    <input type="file" name="file" id="file" class="form-control">
    <input type="submit" value="Submit" name="submit" class="btn btn-primary btn-sm">
</form>
<br>

<br>
<h2>Template</h2>

<code>
NAME,DESCRIPTION,USER,PROJECT,STATUS<br>	
Example,Description example,Nicolas,Mqe,Req
</code>
<br>
<br>
Example file
<br>
<a href="/public/files/import_tasks.csv">import_tasks.csv</a>
<br>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Users</th>
			<th>Projects</th>
			<th>Statuses</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				@foreach($users as $user)
					{{$user->name}}<br>
				@endforeach
			</td>
			<td>
				@foreach($projects as $project)
					{{$project->name}}<br>
				@endforeach
			</td>
			<td>
				@foreach($statuses as $status)
					{{$status->name}}<br>
				@endforeach
			</td>
		
		<tr>
	</tbody>
</table>

@endsection