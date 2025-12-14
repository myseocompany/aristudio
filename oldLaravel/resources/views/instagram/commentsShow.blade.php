@extends('contests.layout')

@section('title', $title)

@section('content')

			<?php $i=1; ?>
			<?php  ?>	

			@foreach ($data as $post)
                    <div class="container comentario fbwhitebox">
						<div class="row">
							<div class="foto">
								
								<imgs src="http://graph.facebook.com/{{$post->from->username}}/picture?type=square">
								
							</div>
	                        <div class="detalles">
	                        	<div>
	                        		<a href="#" id="comment_{{$i}}" name="commnet_{{$i}}"></a><strong class="codigo"><?php echo $i; ?></strong>
	                        	</div>
	                        	<div class="name">
	                            </div>
								<div class="fbsmall">
									{{date('m/d/Y H:i:s', $post->created_time)}}
								</div>
	                           	<div class="message">
									
									{{$post->from->username}}
									<a href="http://fb.com/{{$post->id}}"  target="_blank">{{$post->text}}</a>
								</div>

							</div>
						</div>
						
                   	</div>
                   	<?php $i++; ?>
			@endforeach
			<div>
				<h2>Numero aleatorio</h2>
				<div><button onclick="pickWinner({{( $i - 1 )}});">Generar número aletaorio</button></div>
				<div id="res"></div>
			</div>
			<script>
				function pickWinner(max){
					num = Math.floor(Math.random() * max) + 1;
					$('#res').html("<a href='#comment_"+num+"'>"+num+"</a>");
				}

			</script>

@endsection