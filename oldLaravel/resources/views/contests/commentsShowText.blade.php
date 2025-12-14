@extends('contests.layout')

@section('title', $title)

@section('content')

			<?php $i=1; ?>
			@foreach ($data as $post)
                    <div class="container comentario fbwhitebox">
						<div class="row">
							<div class="detalles">
								<a href="http://fb.com/{{$post['id']}}"  target="_blank">{{$post['from_name']}}</a>
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