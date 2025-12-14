@extends('layout_metadata')
@section('content')
<br>
<center><h1>{{$project->name}}</h1></center>

<div class="container mt-4">
	<table class="table" border="1">
		@if(isset($campaign->ProjectMetaData))
		@foreach($campaign->ProjectMetaData as $main_question)
			<tr>
				<td><h2>{{$main_question->value}}</h2></td>
				
			</tr>
			@if($main_question->ProjectMetaDataChildren)
				<tr>
					<td>
						<table>
					@foreach($main_question->ProjectMetaDataChildren as $sub_question)
						<tr>
							<td><strong>{{$sub_question->value}}</strong>
							</td>
							
							<td>{{$sub_question->getAnswer( $project->id, $sub_question )}}</td>
						</tr>
						@endforeach
					</table>
					</td>
				</tr>	
			@else
				<tr>
					<td>{{$sub_question->getAnswer( $project->id, $main_question )}}</td>
				</tr>	
			@endif
		@endforeach
		@endif
	</table>
</div>
					
<style>
	.table td, .table th {
    border-top: 1px solid #f4f4f8 !important;
	}

	body {
    background-color: #f4f4f8;
	}
		
		.textinput {
			width: 100%;
			min-height: 75px;
			outline: none;
			resize: none;
		}
		.input {
			width: 100%;
			min-height: 40px;
			outline: none;
			resize: none;
			
		}
		tr.tr_31 {
    display: none;
	}
tr.tr_21 {
    display: none;
}
	section#main-content {
    margin: 0px 0px 0px 0px !important;
}

	</style>
@endsection


