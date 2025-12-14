@extends('layout_metadata')
@section('content')
<center><h1>{{$projects->name}}</h1></center>
<div class="container mt-4">
<form method="POST"  action="/metadata/{{$projects->id}}/save/2">
	 {{ csrf_field() }}

	<table class="table">
		<tbody>
			<input type="hidden" name="campaign_id" value="{{$campaign->id}}">

			@if(isset($campaign->projectMetaData))
			@foreach($campaign->projectMetaData as $item)
		
			<tr>
				<td>
					<table class="table table-hover">
								<tr>			
									<td><strong>{{$item->value}}</strong></td>

								</tr>
						<tr>

							@if(true)
							<?php //dd($item->customerMetaDataChildren); ?>
							@foreach($item->projectMetaDataChildren as $projectMetaData)<!-- hijos -->
							
								@if($item->id==$projectMetaData->parent_id)

									<tr>
										<td>
										 <strong>{{$projectMetaData->value}}</strong>
											 @foreach($project_meta as $projectMeta)
								  				@if($projectMeta->meta_data_id==$projectMetaData->id)
								  				<br>
								  				<label><!--respuestas-->
								  					{{$projectMeta->value}}
							  					</label>
								  				@endif
									       @endforeach

										</td>
									</tr>
								@endif
						
							@endforeach	
							@endif
							
						</tr>
					</table>
				</td>
			</tr>
			@endforeach
				@endif
		</tbody>
	</table>
</form>
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
	section#main-content {
    margin: 0px 0px 0px 0px !important;
}

	</style>
@endsection


