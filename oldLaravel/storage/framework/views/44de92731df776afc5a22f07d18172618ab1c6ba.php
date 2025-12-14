<?php 
  $sumPoints = 0; 
  $countTask = 0;
  $max_items = 5;
  $totalTask = 0;


      if($tasksGroup->count()!=0){
        $numGroups = ceil($tasksGroup->count()/ $max_items);
        if($numGroups>1){
          $page_length = round($tasksGroup->count()/round($numGroups));
        }else
        {
          $page_length = $tasksGroup->count();
        }
        //dd(ceil($numGroups));
        $count = 0;
        $page = 0;
      

        $sumPoints = 0;
        $countPoints = 0; ?>

        <?php while($count < $tasksGroup->count()){ ?> 
          <div  class="no_print">
            <ul class="groupbar bb_hbox">
              <?php $__currentLoopData = $tasksGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php 
              $sumPoints += $item->sum_points;
              $countPoints += $item->count_points;
               ?>
               <?php if(($count>1) && ($count % $page_length)==0): ?>
                
            </ul>
          </div>
          <div class="no_print">
            <ul class="groupbar bb_hbox">
                
              <?php endif; ?> 
              <li class="groupBarGroup" style="background-color: <?php if(isset($item->status->background_color)&&($item->status->background_color!="")): ?><?php echo e($item->status->background_color.';'); ?> <?php else: ?> <?php if(isset($item->project->color)&&($item->project->color!="")): ?><?php echo e($item->project->color.';'); ?> <?php else: ?> #ccc; <?php endif; ?> <?php endif; ?><?php 
                  if($tasksGroup->count()!=0){
                    $with = 100/($page_length);
                    echo "width:".$with."%";
                    /*
                    //var_dump($with);
                    if($with>20)
                      echo "width:".$with.";";
                    else
                      echo "width:200px;";
                    */
                  }
               ?>" page="<?php echo e($page_length); ?>">
                <h3><?php echo e($item->sum_points); ?> pts / <?php echo e($item->count_points); ?></h3>
               
                <div>
                <?php if(isset($item->status)): ?>
                  <a href="#<?php echo e($item->status->name); ?>"><?php echo e($item->status->name); ?></a>
                <?php elseif(isset($item->project)): ?>
                  <a href="#<?php echo e($item->project->name); ?>"><?php echo e($item->project->name); ?></a><br><a href="/projects/<?php echo e($item->project->id); ?>"></a>
                <?php else: ?>
                  <a href="#null">Sin estado</a>
                <?php endif; ?>
                </div>
              </li>  
              <?php $count++; ?>        
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
    <?php 
        
        }
     ?>  
<div><?php echo e($sumPoints); ?> puntos ( <?php echo e($countPoints); ?> Tareas)</div> 


<?php 
  $url_charge_account= "charge_account" . substr($_SERVER["REQUEST_URI"], 6);
?>
<?php if($request->status_id==56): ?>
<a href="/<?php echo e($url_charge_account); ?>">Download Charge Account</a>
<?php endif; ?>

<?php }?>