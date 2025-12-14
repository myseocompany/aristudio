@extends('layout')


<?php  function clearWP($str){
  $str = trim($str);
  $str = preg_replace('/\s+/', '', $str);
  
  $str = str_replace("+", "", $str );
  $str = str_replace("p:", "", $str );
  if(strlen($str)>10)
    return $str;
  elseif( strlen($str) == 10 )
    return "57".$str;  
} ?>

@section('content')
<h1>@if(isset($phase)) <a href="/customers">Clientes</a> | {{$phase->name}}@else Clientes | <a href="customers/phase/2">Aspirantes</a> @endif</h1>
<style>
  a:hover {
    color: #4178be;
}
</style>

<!-- Incio tareas pendientes -->
{{-- @include('customers.actions') --}}
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
  <hr>
  
  @if($customersGroup->count()>-1)

  
  <ul class="groupbar bb_hbox" id="dashboard">
  
    @foreach($customersGroup as $item)
    <li class="groupBarGroup" style="background-color: {{$item->color}}; width: <?php 
        if($customersGroup->count()!=0){
          echo 100/$customersGroup->count();
        }
     ?>%">
      
     
      <div><a href="#" onclick="changeStatus({{$item->id}})">{{$item->name}}</a></div>
      <h3>{{$item->status_count}}</h3>
    </li>          
    @endforeach
  </ul>

  <style>
      @media screen and (max-width: 992px) {
        #dashboard {
          display: none;
        }
      }

      /* On screens that are 600px wide or less, the background color is olive */
      @media screen and (max-width: 600px) {
        #dashboard {
          display: none;
        }
      }
  </style>
  

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
            <table class="table table-striped table-sm">
              
              </thead>
              <?php $lastStatus=-1 ?>
              <tbody>
               <?php $count=1; ?>
                @foreach($model as $item)
                {{-- colores --}}
		 		 
                {{-- fin colores --}}
                <!--<tr @if(Auth::user()->role_id == 1) onmouseover="showEditIcon({{$item->id}});" onmouseout="hideEditIcon({{$item->id}});" @endif>-->
                <tr>
                
                  <!--- Fecha del pedido -->
                  <td colspan="4">
                  <div class="customer_created">
                    <a href="/customers/{{ $item->id }}/show">{{ $item->name }}</a>
                    <br><!-- Link llamada -->
                    {{ clearWP($item->phone) }}      
                    <br>
                      <a href="/customers/{{ $item->id }}/show">{{$item->created_at}}</a>
                      <br>
                      <!--BOTÓN WP-->
                      <a href="https://wa.me/{{ clearWP($item->phone) }}"  class="iconwp"  target="wp_window">
                          <img src="/img/iconoWA.png" width="20">
                      </a> 
                      <a href="tel:{{ clearWP($item->phone) }}" class="tel"><img src="img/telefono.png"  width="20"></a>
 
 <!----  POP UP --->                     <!--Agregar acción-->
                      @include('customers.saveAction_popUp', ['customer'=>$item])


  
<!--- fin de POP UP  --->
                      
                      
                  </div>
                </td>
                
                
                
                  
                  

                <td class="status-badge">
                    {{-- @if (isset($item->status_id)) {{ substr($item->statuses_name,0,10) }} @endif --}}
                    
                    @if(isset($item->status_id)&&($item->status_id!="")&&(!is_null($item->status)))

                      <div class="item-media">
                        <div class="no-img">
                          <span class="badge" id="customer_status_{{$item->id}}" onclick="openStatuses();" style="background-color:{{$item->status->color}}">           
                          {{substr($item->status->name,0,10)}}
                          </span>
                        </div>
                      </div>
                    @endif 
                    
                    <!--BORRAR LEAD-->
                    
                    <td class="status-badge">

                  @if (Auth::user()->role_id == 1 )
                <a href="/customers/{{ $item->id }}/destroy"><span class="btn btn-sm btn-danger" aria-hidden="true" title="Eliminar"><img src="/img/delete.png" width="20"></span></a>                                 
              @endif</td>
              </tr>

              <tr>
                <td colspan="6">
                  <ul>
                  @php
                    $actions = [];
                    
                    if(isset($item->actions))
                      $actions = $item->actions;

                  @endphp
                  @foreach($actions as $action)
                    <li>
                      {{$action->created_at}} :: {{$action->note}} :: {{$action->type->name}}
                    </li>
                  @endforeach
                  </ul>
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


<style>
  table{
    table-layout: fixed;
  }
  .info{
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  padding: 6px 38px;
  gap: 10px;

  position: relative;
  width: 200px;
  height: 45px;

  background: #17A2B8;
  }

  td.status-badge {
      /*width: 30%;*/
      text-align-last: center;
      vertical-align: middle;
  }

  td.actions {
    text-align: right;
    width: 10%;
  }
  .item-media{
    /*
    position: relative;
    top:5vw
    */
  }
  td.created{
    width: 30%;
  }

  .tel{
    margin:5px;
  }
  .iconwp{
    margin:1vw;
  }

  

</style>
  <script type="text/javascript">

  function showEditIcon(id){
    console.log("show_edit_icon_"+id);
    $("#edit_icon_"+id).css("display", "inline");
    $("#edit_icon_campaings_"+id).css("display", "inline");
  }
  function hideEditIcon(id){
    console.log("hide_edit_icon_"+id);
    $("#edit_icon_"+id).css("display", "none");
    $("#edit_icon_campaings_"+id).css("display", "none");
  }

  function nav(value,id) {
    var message = encodeURI(value);
    if (value != "") { 
      endpoint = '/campaigns/'+id+'/getPhone/setMessage/'+message;
        $.ajax({
            type: 'GET',
            url: endpoint,
            dataType: 'json',
            success: function (data) {
                var phone = data;
                   url = "https://api.whatsapp.com/send/?phone="+phone+"&text="+encodeURI(value);
                   window.open(url,'_blank');
            },
            error: function(data) { 
            }
        });

       }
  }

  function setStatus(id){
    console.log(id);
    var status_id = $("#status_id_"+id).val();
    var parameters = {
        id: id,
        status_id: status_id
    };
    $.ajax({
        data:  parameters,
        url:   '/set-customer-status',
        type:  'get',
        beforeSend: function () {
        },
        success:  function (response) { 
          $("#customer_status_12162").attr('style',  'background-color:'+response.color);
          var short_name = response.name.substring(0, 3);
          $("#customer_status_12162").text(short_name);
        }
    });
  }


      function getMessages(id){
        $("#messages_"+id).empty();
        $("#div_campaign_button_"+id).empty();
        var campaign_id = $("#campaign_id_"+id).val();

          endpoint = '/campaigns/'+campaign_id;
        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: endpoint,
            dataType: 'json',
            success: function (data) {
                loadMessages(data, id);
            },
            error: function(data) { 
            }
        });
      }

        function loadMessages(data, id){

          str = '<label for="message_id">Mensajes:</label><br>';
          str += '<select name="message_id_'+id+'" id="message_id_'+id+'" class="custom-select" onchange="loadButton('+id+');">;';
          str += '<option value="">Seleccione un mensaje</option>';
          $.each(data, function(i, obj) {
            str += '<option value="'+obj.text+'" >'+(obj.text).substr(0,20)+'</option>';
          });
          str += '</select>';

          $("#messages_"+id).prepend(str);
        }

      function loadButton(id){
        console.log(getSelectedMessage());
        $("#div_campaign_button_"+id).html('<br><a href="'+getSelectedMessage('if(isset($item)){$item->getPhone()}endif',id)+')" name="campaign_button" id="campaign_button" class="btn btn-sm btn-primary my-2 my-sm-0" target="_blanck"> Enviar</a>')
      }

      function getSelectedMessage(phone, id){
         var msg = $('select[name="message_id_'+id+'"] option:selected').val();

         var url = "https://api.whatsapp.com/send/?phone="+phone+"&text="+encodeURI(msg);
         return url;
      }
  </script>

@endsection
