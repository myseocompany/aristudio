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

<?php $__env->startSection('content'); ?>
<h1><?php if(isset($phase)): ?> <a href="/customers">Clientes</a> | <?php echo e($phase->name); ?><?php else: ?> Clientes | <a href="customers/phase/2">Aspirantes</a> <?php endif; ?></h1>
<style>
  a:hover {
    color: #4178be;
}
</style>

<!-- Incio tareas pendientes -->

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
  </a> | <a href="/leads/excel<?php echo e(requestToStr($request)); ?>">Excel</a> </div>
  <br>




  <div>
    <?php echo $__env->make('customers.filter', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
  </div>

<div>
  <hr>
  
  <?php if($customersGroup->count()>-1): ?>

  
  <ul class="groupbar bb_hbox" id="dashboard">
  
    <?php $__currentLoopData = $customersGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <li class="groupBarGroup" style="background-color: <?php echo e($item->color); ?>; width: <?php 
        if($customersGroup->count()!=0){
          echo 100/$customersGroup->count();
        }
     ?>%">
      
     
      <div><a href="#" onclick="changeStatus(<?php echo e($item->id); ?>)"><?php echo e($item->name); ?></a></div>
      <h3><?php echo e($item->status_count); ?></h3>
    </li>          
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </ul>

  <style>
      @media  screen and (max-width: 992px) {
        #dashboard {
          display: none;
        }
      }

      /* On screens that are 600px wide or less, the background color is olive */
      @media  screen and (max-width: 600px) {
        #dashboard {
          display: none;
        }
      }
  </style>
  

  <?php else: ?>
    Sin Estados
  <?php endif; ?>
</div>


  <?php if(session('status')): ?>
          <div class="alert alert-primary alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <?php echo html_entity_decode(session('status')); ?>

        </div>
  <?php endif; ?>
    <?php if(session('statusone')): ?>
          <div class="alert alert-warning alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <?php echo html_entity_decode(session('statusone')); ?>

        </div>
  <?php endif; ?>
  <?php if(session('statustwo')): ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <?php echo html_entity_decode(session('statustwo')); ?>

        </div>
  <?php endif; ?>
  

  
 <br>
 Registro <strong><?php echo e($model->currentPage()*$model->perPage() - ( $model->perPage() - 1 )); ?></strong>  a <strong><?php echo e($model->getActualRows); ?></strong> de <strong><?php echo e($model->total()); ?></strong>
 <br>

<br>
<!-- Prueba boton metodo zero actions -->

<!-- <a href="/emails/zeroActions">cero actions</a> -->

<!-- Fin Prueba boton metodo zero actions -->

  <div class="">
            <?php if(count($model) > 0): ?>
            <table class="table table-striped table-sm">
              
              </thead>
              <?php $lastStatus=-1 ?>
              <tbody>
               <?php $count=1; ?>
                <?php $__currentLoopData = $model; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
		 		 
                
                <!--<tr <?php if(Auth::user()->role_id == 1): ?> onmouseover="showEditIcon(<?php echo e($item->id); ?>);" onmouseout="hideEditIcon(<?php echo e($item->id); ?>);" <?php endif; ?>>-->
                <tr>
                
                  <!--- Fecha del pedido -->
                  <td colspan="4">
                  <div class="customer_created">
                    <a href="/customers/<?php echo e($item->id); ?>/show"><?php echo e($item->name); ?></a>
                    <br><!-- Link llamada -->
                    <?php echo e(clearWP($item->phone)); ?>      
                    <br>
                      <a href="/customers/<?php echo e($item->id); ?>/show"><?php echo e($item->created_at); ?></a>
                      <br>
                      <!--BOTÓN WP-->
                      <a href="https://wa.me/<?php echo e(clearWP($item->phone)); ?>"  class="iconwp"  target="wp_window">
                          <img src="/img/iconoWA.png" width="20">
                      </a> 
                      <a href="tel:<?php echo e(clearWP($item->phone)); ?>" class="tel"><img src="img/telefono.png"  width="20"></a>
 
 <!----  POP UP --->                     <!--Agregar acción-->
                      <?php echo $__env->make('customers.saveAction_popUp', ['customer'=>$item], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


  
<!--- fin de POP UP  --->
                      
                      
                  </div>
                </td>
                
                
                
                  
                  

                <td class="status-badge">
                    
                    
                    <?php if(isset($item->status_id)&&($item->status_id!="")&&(!is_null($item->status))): ?>

                      <div class="item-media">
                        <div class="no-img">
                          <span class="badge" id="customer_status_<?php echo e($item->id); ?>" onclick="openStatuses();" style="background-color:<?php echo e($item->status->color); ?>">           
                          <?php echo e(substr($item->status->name,0,10)); ?>

                          </span>
                        </div>
                      </div>
                    <?php endif; ?> 
                    
                    <!--BORRAR LEAD-->
                    
                    <td class="status-badge">

                  <?php if(Auth::user()->role_id == 1 ): ?>
                <a href="/customers/<?php echo e($item->id); ?>/destroy"><span class="btn btn-sm btn-danger" aria-hidden="true" title="Eliminar"><img src="/img/delete.png" width="20"></span></a>                                 
              <?php endif; ?></td>
              </tr>

              <tr>
                <td colspan="6">
                  <ul>
                  <?php 
                    $actions = [];
                    
                    if(isset($item->actions))
                      $actions = $item->actions;

                   ?>
                  <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                      <?php echo e($action->created_at); ?> :: <?php echo e($action->note); ?> :: <?php echo e($action->type->name); ?>

                    </li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ul>
                </td>
              </tr>
                <?php $count++;
                  $lastStatus = $item->status_id;
                ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php $count--;?>
                
              </tbody>
                 <?php 
          
          if(isset( $item->points )){$total_tools += $item->points; }
            $count++;
          ?>
            </table>
            
                  <?php endif; ?>
                  
                  <?php echo e($model->appends(request()->input())->links()); ?>

                  <div>
             
             
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>