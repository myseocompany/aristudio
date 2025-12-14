<div class="table-responsive" id="">
              
                

<?php $last_task_status_id=-1;
      $last_project_id = -1;
 ?>
 <script>
var row;
var positionMouse;
var coordenadaXini;
var coordenadaYini;
var coordenadaXfin;
var coordenadaYfin;
var mouseMoveY;
var mouseMoveX;
function dragStart(){
  row = event.target;
  coordenadaXini = event.pageX + document.documentElement.scrollLeft;
  coordenadaYini = event.pageY + document.documentElement.scrollLeft;
  console.log("las coordenadas iniciales son: en x = " + coordenadaXini + " en Y = " + coordenadaYini);
}
function dragOver(){
  var e = event;
  e.preventDefault();
  coordenadaXfin = event.pageX + document.documentElement.scrollLeft;
  coordenadaYfin = event.pageY + document.documentElement.scrollLeft;
  mousemoveX = coordenadaXini - coordenadaXfin;
  mousemoveY = coordenadaYini - coordenadaYfin;
  console.log( "Cambio x " + mousemoveX + " cambio Y " + mousemoveY);


  let children= Array.from(e.target.parentNode.parentNode.children);
  if(children.indexOf(e.target.parentNode)>children.indexOf(row))
    e.target.parentNode.after(row);
  else
    e.target.parentNode.before(row);
} 


</script>


<?php     $title = "Tareas de hoy";     ?>
<?php echo $__env->make("tasks.data_table_head", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
   <?php $__currentLoopData = $model; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(\Carbon\Carbon::parse($item->due_date)->isToday()  && $item->type_id != 119 ): ?>  
          <?php echo $__env->make("tasks.data_table_body", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
        <?php endif; ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php echo $__env->make("tasks.data_table_footer", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 


<!-- OTRA TABLA -->
<?php  $title = "Tareas";  ?>
<?php echo $__env->make("tasks.data_table_head", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
  <?php $__currentLoopData = $model; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(!\Carbon\Carbon::parse($item->due_date)->isToday() && $item->type_id != 119 ): ?> 
      <?php echo $__env->make("tasks.data_table_body", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
    <?php endif; ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php echo $__env->make("tasks.data_table_footer", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 




<!-- FIN TABLE OTRAS -->

    <div><?php echo e($sumPoints); ?> ptos/ <?php echo e($countTask); ?></div>


    
  </div>

  <?php echo $__env->make("tasks.modal_update", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>