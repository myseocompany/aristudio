@extends('layout_metadata')
@section('content')
<br>
<center><h1>{{$projects->name}}</h1></center>

<div class="container mt-4">
<form method="POST"  action="/metadata/{{$projects->id}}/save">
	 {{ csrf_field() }}

	<table class="table">
		<tbody>
			<input type="hidden" name="campaign_id" value="{{$campaign->id}}">

			@if(isset($campaign->projectMetaData))
			@php
			@endphp
			@foreach($campaign->projectMetaData as $item)
		
			<tr>
				<td>
					<table class="table table-hover">
								<tr>			
									<td><h2>{{$item->value}}</h2></td>

								</tr>
						<tr>

							@if(true)
							<?php //dd($item->customerMetaDataChildren); ?>
							@foreach($item->projectMetaDataChildren as $projectMetaData)<!-- hijos -->
							
								@if($item->id==$projectMetaData->parent_id)

									<tr>
										<td>

										@if($item->isMultiple())
											<input type="{{$item->getInputType()}}" 
											name="meta_{{$projectMetaData->id}}"  >
												 {{$projectMetaData->value}}
											  
											 @else
											  	<strong>{{$projectMetaData->value}}</strong>
											  	@if($item->getInputType()=='text')
											  		@if($projectMetaData->parent_id==31)
																										
																 <div class="row" id="{{$projectMetaData->id}}">

															 	</div>
											  				
											  			@else	

												 		<input type="{{$item->getInputType()}}"  required="required" name="meta_{{$projectMetaData->id}}" class="input" value="" >
												 	@endif	
												 				
												@else
												{{$projectMetaData->value}}

												@if($item->getInputType()=='file')

												@else
													<textarea id="msg" required="required" name="meta_{{$projectMetaData->id}}" class="textinput" value=""  ></textarea>
												 @endif	
											 @endif		
										@endif
											
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

	<center>
			<input type="submit" class="btn btn-primary" value="Guardar">
	</center>
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


