<?php 



header("Location: https://docs.google.com/forms/d/e/1FAIpQLSfMqdqmtY0Ht6Ic5ZcQuYfY15K9dGVXtA-lqQChzwbnzC8ydA/viewform");
exit();
?>
@extends('layout_metadata')
@section('content')
<br>

<div class="container mt-4">
	
<form method="POST"  action="/metadata/1/save/project" enctype="multipart/form-data">
	 {{ csrf_field() }}
	  

	<table class="table">
	<tbody>
		<tr><td><img src="https://myseo.com.co/images/logo_my_seo_company.png" width="150" alt=""></td></tr>
		<tr>
			<td>
			Hola, Bienvenid@ a nuestro servicio de marketing digital. Primero de todo queremos darte las gracias por haber tomado este paso y que hayas confiado en nosotros para poderte arrojar claridad y estrategia para tu negocio.
<br>
Con el fin que el servicio sea lo más provechoso posible para ti, necesitamos que nos respondas una serie de preguntas para poder tener la mayor información posible, y poder aportarte el máximo valor.
<br>
Si no sabes responder alguna pregunta, ¡no te preocupes! No pretendemos que tengas toda la información, solo que puedas darnos más contexto para poder ir a la llamada con información suficiente para poderte ayudar al máximo.

			</td>
		</tr>
		<tr>
			<td>
			<table class="table table-hover">
				 <strong>Nombre de la empresa</strong>
			     <input required  class="form-control" type="text" name="name" placeholder="Escribe el nombre de tú empresa">
		    </table>

			</td>
		</tr>
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
							<?php //dd($item->projectMetaDataChildren); ?>
							@foreach($item->projectMetaDataChildren as $projectMetaData)<!-- hijos -->
							<?php //dd($projectMetaData); ?>
								@if($item->id==$projectMetaData->parent_id)

									<tr>
										<td>

										@if($item->isMultiple())
											<input type="{{$item->getInputType()}}" 
											name="file_{{$projectMetaData->id}}"  >
												 {{$projectMetaData->value}}
											  
											 @else
											  	<strong>{{$projectMetaData->value}}</strong>
											  	@if($item->getInputType()=='text')
											  		@if($projectMetaData->parent_id==31)

															<script type="text/javascript">
																		var nextinput = 0;
																		function AgregarCampos(id){
																		nextinput++;
																		password = '<div class="col-md-6"><div class="form-group" ><label>Password</label><input class="form-control" type="text" size="20" name="login_'+id+'[]"; /></div></div>';
																		url = '<div class="col-md-6"><div class="form-group" ><label>Url</label><input class="form-control" type="text" size="20" name="login_'+id+'[]"; /></div></div>';
																		user = '<div class="col-md-6"><div class="form-group" ><label>User</label><input class="form-control" type="text" size="20"  name="login_'+id+'[]"; /></div></div>';
																		name ='<div class="col-md-6"><div class="form-group" ><label>Name</label><input class="form-control" type="text" size="20"  name="login_'+id+'[]"; /></div></div>';

																		$("#"+id).prepend(password);
																		$("#"+id).prepend(url);
																		$("#"+id).prepend(user);
																		$("#"+id).prepend(name);
																		}
															</script>

															  <div class="row">
																  <div class="col-md-6">
																
																    <div class="form-group" >
																      <label for="name">Nombre</label>
																      <input class="form-control" type="text" name="login_{{$projectMetaData->id}}[]" placeholder="red social, hosting, dominio, herramienta">
																    </div>

																    <div class="form-group" >
																      <label for="url">URL</label>
																      <input class="form-control" type="text" name="login_{{$projectMetaData->id}}[]" placeholder="Ej: https://www.instagram.com/miempresa/">
																    </div>
																  </div>

																  <div class="col-md-6">
																    <div class="form-group" >
																      <label for="user">Usuario</label>
																      <input class="form-control" type="text" name="login_{{$projectMetaData->id}}[]">
																    </div>

																    <div class="form-group" >
																      <label for="password">Contraseña</label>
																      <input class="form-control" type="text" name="login_{{$projectMetaData->id}}[]">
																    </div>
																  </div>
															</div>
																										
															<div class="row" id="{{$projectMetaData->id}}">
															</div>
											  			<a onclick="AgregarCampos({{$projectMetaData->id}});">
											  				<img  style="max-width: 2%;" src="https://myseo.com.co/images/anadir.png">	

											  			</a>
											  		@elseif($projectMetaData->parent_id==51)
											  			@if($projectMetaData->id==55)
											  				<input type="file" name="meta_{{$projectMetaData->id}}">
											  			@else
												  			<div class="row">
																<div class="col-md-12">
																	<div class="form-group" >
																	  <input class="form-control" type="text" name="meta_{{$projectMetaData->id}}[]">
																	</div>
																</div>
															</div>
											  			@endif
											  		@else	
												 		<input required type="{{$item->getInputType()}}"  name="meta_{{$projectMetaData->id}}" class="input" value="" >
												 	@endif

												 				
												@else
												{{$projectMetaData->value}}

												@if($item->getInputType()=='file')
												<!--
												<input type="{{$item->getInputType()}}" name="file" class="form-control" >
												-->
												@else
													<textarea id="msg" name="meta_{{$projectMetaData->id}}" class="textinput" value=""  ></textarea>
												 @endif	
											 @endif		
										@endif
											
										</td>
									</tr>
								@endif
						
							@endforeach	
							@endif
									  <?php
			                    if(isset($_POST['submit'])){
			                     if(!empty($_POST['meta_{{$projectMetaData->id}}_name[]'])){
			                 
			                        foreach($_POST['meta_{{$projectMetaData->id}}_name[]'] as $selected){
			                        
			                         }
			                     }
			                    }

			                  

			                    ?>
				 
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
	.table {
		background: white;
	}
	section#main-content {
    margin: 0px 0px 0px 0px !important;
}

	
	</style>
@endsection


