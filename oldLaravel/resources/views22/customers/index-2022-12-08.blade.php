@extends('layout')
<?php  function clearWP($str){
  $str = trim($str);
  $str = str_replace("+", "", $str );
  return $str;
} ?>

@section('content')
<h1>@if(isset($phase)){{$phase->name}}@else Clientes @endif</h1>
<style>
  a:hover {
    color: #4178be;
}
</style>

<!-- Incio tareas pendientes -->
<h2>Acciones pendientes</h2>
<div>
  @foreach($pending_actions as $item)
  <div class="pending-action">
    @if(isset($item->customer))<h3>{{$item->customer->name}}</h3>@endif
    <div class="note-action">{{$item->note}}</div>
    <a href="/customers/{{$item->customer_id}}/show/?pending_action_id={{$item->id}}#pedding-action" class="due-date-action">
       Finalizar accion programada para {{$item->due_date}} 

    </a>
  </div>
  @endforeach
</div>

<!-- Find tareas pendientes -->


<?php 
  function requestToStr($request){
    $str = "?";
    $url = $request->fullUrl();
    $parsedUrl = parse_url($url);
    
    if(isset($parsedUrl['query'] ))
      $str .= $parsedUrl['query']; 

    return $str; 
  }
 ?>

  <div><a style="color: #4178be;" href="/customers/create">Crear
        <i class="fa fa-plus" aria-hidden="true"></i>
  </a> | <a href="/leads/excel{{ requestToStr($request) }}">Excel</a> </div>
  <br>
{{-- obteber datos del tiempo --}}



  <div>
    @include('customers.filter')
  </div>

<div>
  @if($customersGroup->count()!=0)
  <ul class="groupbar bb_hbox">
  
    @foreach($customersGroup as $item)
    <li class="groupBarGroup" style="background-color: {{$item->color}}; width: <?php 
        if($customersGroup->count()!=0){
          echo 100/$customersGroup->count();
        }
     ?>%">
      <h3>{{$item->count}}</h3>
     
      <div><a href="#" onclick="changeStatus({{$item->id}})">{{$item->name}}</a></div>
    </li>          
    @endforeach
  </ul>
  @else
    Sin Estados
  @endif
</div>

{{-- Alertas --}}
  @if (session('status'))
          <div class="alert alert-primary alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            {!! html_entity_decode(session('status')) !!}
        </div>
  @endif
    @if (session('statusone'))
          <div class="alert alert-warning alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            {!! html_entity_decode(session('statusone')) !!}
        </div>
  @endif
  @if (session('statustwo'))
          <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            {!! html_entity_decode(session('statustwo')) !!}
        </div>
  @endif
  {{-- fin alertas --}}

  {{-- tabla resumen --}}
 <br>
 Registro <strong>{{ $model->currentPage()*$model->perPage() - ( $model->perPage() - 1 ) }}</strong>  a <strong>{{ $model->getActualRows}}</strong> de <strong>{{$model->total()}}</strong>
 <br>
{{-- <div>{{$model->total()}} Registro(s)</div> --}}
<br>
<!-- Prueba boton metodo zero actions -->

<!-- <a href="/emails/zeroActions">cero actions</a> -->

<!-- Fin Prueba boton metodo zero actions -->

  <div class="">
            @if (count($model) > 0)
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  {{-- <th></th> --}}
                  <th>Nombre</th>
                  
                  
                  <th>Datos</th>
                  <th>KPI</th>
                  <th>Fuente</th>
                  <th>Estado</th>
                  <th>Fecha de creacion</th>
                  @if (Auth::user()->role_id == 1)
                    <th>Fecha de @if ( (isset($request->created_updated) &&  ($request->created_updated=="updated")) ) Actualización @else Creación @endif</th>
                  @endif
                </tr>
              </thead>
              <?php $lastStatus=-1 ?>
              <tbody>
               <?php $count=1; ?>
                @foreach($model as $item)
                {{-- colores --}}
		 		 @if( $lastStatus != $item->status_id && !is_null($item->status))
         
		        <tr style="background-color: {{$item->status->color}}" class="title_status">
		          <td colspan="9">
                
                <a id="{{$item->status->name}}">{{$item->status->name}}
                  
                </a>
                
              </td>
		        </tr>
		        @endif 
                {{-- fin colores --}}
                <tr>
                  <td>
                    <a href="/customers/{{ $item->id }}/show">{{ $count + $model->perPage() * (($model->currentPage() -1)) }}</a>
                  </td>

                  <td><a href="/customers/{{ $item->id }}/show">{{ substr($item->name,0,20) }}@if (strlen($item->name)>20) ...@endif</a><br>

                  {{ substr($item->country,0,15)}}@if (strlen($item->country)>15) ...@endif, {{ substr($item->city,0,15)}}@if (strlen($item->city)>15) ...@endif<br>
                  @if (isset($item->user_id) && ($item->user_id != '') && ($item->user_id != 0))
                  {{$item->user->name}}
                  @endif
                  @if (is_null($item->user_id) || ($item->user_id == 0))
                  Sin asignar
                  @endif
                  <br>
                    {{ $item->position }}
                  </td>
                  <!-- Link whatsapp -->
                  <td><a hresf="/customers/{{ $item->id }}/show" href="https://wa.me/57{{ clearWP($item->phone) }}" target="_blank">

                  	{{ $item->phone }}
                  	@if(isset($item->phone2))
                  	, {{ $item->phone2 }}
                    <br>

                  	@endif
                    {{$item->email}}
                    @if(isset($item->project))
                    , {{ $item->project->name }}
                    @endif
                    <br>

                  </a></td>
                  
                  <!--
                  <td>
                         <a href="/customers/{{ $item->id }}/show">
                    {{ substr($item->notes,0,35)."..."}} @if (count($item->notes)>35) ...@endif
                      </a>
                  </td> 
                -->
                <td>
                  {{ $item->countActions() }} Acc.<br>
                  @if(!is_null($item->count_empanadas) && $item->count_empanadas>0)
                  {{$item->count_empanadas}} Emp. <br>
                  @endif
                </td>
                 <td>@if(isset($item->source)){{$item->source->name}}@endif</td> 
                 <td>
                 	  {{-- @if (isset($item->status_id)) {{ substr($item->statuses_name,0,10) }} @endif --}}
                       <?php 
                         if(isset($item->status_id)&&($item->status_id!="")&&(!is_null($item->status))){
                               echo $item->status->name;
                            }
                        ?> 
                 </td>
                 <td>@if ( (isset($request->created_updated) &&  ($request->created_updated=="updated")) ) 
                  {{ $item->updated_at }}
                  @else
                  {{ $item->created_at }}
                  @endif
                 </td> 
                 @if (Auth::user()->role_id == 1 )
                   <td>
                   {{-- Delete --}}
                    <a href="/customers/{{ $item->id }}/destroy"><span class="btn btn-sm btn-danger fa fa-trash-o" aria-hidden="true" title="Eliminar"></span></a>
                 </td>
                 @endif
                 
                  
                  <td>
                  <a href="/customers/{{ $item->id }}/show"><span class="btn btn-sm btn-success fa fa-eye fa-3" aria-hidden="true" title="Consultar"></span></a>
                    <a href="/customers/{{ $item->id }}/edit"><span class="btn btn-sm btn-warning fa fa-pencil-square-o" aria-hidden="true" title="Editar"></span></a>
                    {{-- Delete --}}
                    <a href="/customers/{{ $item->id }}/destroy"><span class="btn btn-sm btn-danger fa fa-trash-o" aria-hidden="true" title="Eliminar"></span></a>
                  </td>
                  </td>
                
                </tr>
                <?php $count++;
                  $lastStatus = $item->status_id;
                ?>
        @endforeach
                <?php $count--;?>
                
              </tbody>
                 <?php 
          
          if(isset( $item->points )){$total_tools += $item->points; }
            $count++;
          ?>
            </table>
            
                  @endif
                  {{-- {{$model->links()}} --}}
                  {{ $model->appends(request()->input())->links() }}
                  <div>
             {{--  Registro {{ $model->currentPage()*$model->perPage() - ( $model->perPage() - 1 ) }}  a {{ $model->currentPage()*$model->perPage()}} de {{ $model->total()}} --}}
             
            </div>
          </div>
@endsection
