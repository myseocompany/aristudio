<?php $__env->startSection('content'); ?>

<?php 
function number_words($valor, $sep, $desc_decimal) {
  $arr = explode(".", $valor);
  $entero = $arr[0];
  if (isset($arr[1])) {
  $decimos = strlen($arr[1]) == 1 ? $arr[1] . '0' : $arr[1];
  }

  $fmt = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
  if (is_array($arr)) {
    $num_word = ($arr[0]>=1000000) ? "{$fmt->format($entero)} pesos" : "{$fmt->format($entero)} pesos m/cte";
    if (isset($decimos) && $decimos > 0) {
      $num_word .= " $sep {$fmt->format($decimos)} $desc_decimal";
    }
  }
  return $num_word;

}

  $date = date("F j, Y"); 
  $sumPoints = 0; 
  $countTask = 0;
  $max_items = 5;
  $totalTask = 0;
  $amount = 0;
  if($tasksGroup->count()!=0){
    $numGroups = ceil($tasksGroup->count()/ $max_items);
    if($numGroups>1){
      $page_length = round($tasksGroup->count()/round($numGroups));
    }else{
      $page_length = $tasksGroup->count();
    }
    $count = 0;
    $page = 0;
  }
?>
 
<?php 
  $sumPoints = 0;
  $countPoints = 0;
  $bond = 0 ; 
?>

<?php $__currentLoopData = $tasksGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php 
    $amount += $item->sum_points;

  ?>      
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<table>
    <tbody>
      <tr>
        <td class="table-body">Manizales, <?php echo e($date); ?></td>
      </tr>
    <tr>
      <td class="header">
          MY SEO COMPANY <br>
          NIT. 900.489.574-1 <br>
          DEBE A: <br>
          <?php echo e($user->name); ?><br> 
          C.C <?php echo e($user->document); ?> <br>
          LA SUMA DE: <br>
          <br>
          <?php 
          if(isset($user->hourly_rate))
            $amount = $amount * $user->hourly_rate;
          else
            $amount = $amount * 10000;
          
           ?>
        
          <?php echo e($val = number_words($amount,"pesos","y","centavos")); ?> ($<?php echo e(number_format($amount,0)); ?>)
      </td>

  <?php while($count < $tasksGroup->count()): ?>
    <?php $__currentLoopData = $tasksGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php 
        $sumPoints += $item->sum_points;
        $countPoints += $item->count_points;
      ?>
      <tr>
        <td class="table-body">
          <?php if(isset($item->status)): ?>
            <?php echo e($item->status->name); ?>: <?php echo e($item->sum_points); ?>

          <?php elseif(isset($item->project)): ?>
            <?php echo e($item->project->name); ?>: <?php echo e($item->sum_points); ?>

          <?php else: ?>
            Sin estado: <?php echo e($item->sum_points); ?>

          <?php endif; ?>
        </td>
      </tr>  
      <?php 
        $count++; 
      ?>        
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  <?php endwhile; ?>
  </tbody>
</table>
<table>
  <tbody>
    <tr>
      <td class="table-body"><?php echo e($sumPoints); ?> puntos ( <?php echo e($countPoints); ?> Tareas)</td>
    </tr>
   
  </tbody>
</table>
<br>
<table>
<tbody>
  <tr>
    <td class="table-body">
      Declaro que: <br>
      • No soy responsable de IVA <br>
      • No estoy obligado a expedir factura de venta según el artículo 616-2 del Estatuto Tributario. <br>

      Cordialmente, <br> <br> <br> <br>
      <?php echo e($user->name); ?><br>
      C.C <?php echo e($user->document); ?> <br> 
      Dirección: <?php echo e($user->address); ?> <br>
      Celular: <?php echo e($user->phone); ?> <br>

    </td>
  </tr>
</tbody>
</table>



<style type="text/css">
  .header{
    text-align: center;
  }

  th, td {
    padding: 0px;
  }

  .table-body{
    padding-left: 15%; 
  }
</style>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layout_charge_account', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>