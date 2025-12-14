
<form action="/customers/" method="GET" id="filter_form" class="row">
  <div class="col-md-3">
    <div>
      <select name="filter" class="custom-select" id="filter" onchange="update()">
        <option value="">Seleccione tiempo</option>
        <option value="0" <?php if($request->filter == "0"): ?> selected="selected" <?php endif; ?>>Hoy</option>
        <option value="-1" <?php if($request->filter == "-1"): ?> selected="selected" <?php endif; ?>>Ayer</option>
        <option value="lastweek" <?php if($request->filter == "lastweek"): ?> selected="selected" <?php endif; ?>>semana pasada</option>
        <option value="lastmonth" <?php if($request->filter == "lastmonth"): ?> selected="selected" <?php endif; ?>>mes pasado</option>
        <option value="-7" <?php if($request->filter == "-7"): ?> selected="selected" <?php endif; ?>>últimos 7 dias</option>
        <option value="-30" <?php if($request->filter == "-30"): ?> selected="selected" <?php endif; ?>>últimos 30 dias</option>
        
        <option value="thisweek" <?php if($request->filter == "thisweek"): ?> selected="selected" <?php endif; ?>>esta semana</option>
        <option value="currentmonth" <?php if($request->filter == "currentmonth"): ?> selected="selected" <?php endif; ?>>este mes</option>
        <option value="nextweek" <?php if($request->filter == "nextweek"): ?> selected="selected" <?php endif; ?>>proxima semana</option>
        <option value="nextmonth" <?php if($request->filter == "nextmonth"): ?> selected="selected" <?php endif; ?>>próximo mes</option>
        <option value="+7" <?php if($request->filter == "+7"): ?> selected="selected" <?php endif; ?>>próximos 7 dias</option>
        <option value="+30" <?php if($request->filter == "+30"): ?> selected="selected" <?php endif; ?>>próximos 30 dias</option>
        
      </select>
    </div>
    <div>
      <input class="input-date" type="date" id="from_date" name="from_date" onchange="cleanFilter()" value="<?php echo e($request->from_date); ?>">
      <input class="input-date" type="date" id="to_date" name="to_date" onchange="cleanFilter()" value="<?php echo e($request->to_date); ?>">
    </div>
  </div>
  <div class="col-md-2">
    <select name="status_id" class="slectpicker custom-select" id="status_id" onchange="submit();">
      <option value="">Estado...</option>
      <?php $__currentLoopData = $customer_options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($item->id); ?>" <?php if($request->status_id == $item->id): ?> selected="selected" <?php endif; ?>>
           <?php echo e($item->name); ?>

          
        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>


    <!--  
*
*    Combo de usuarios
*
-->
      <select name="user_id" class="custom-select" id="user_id" onchange="submit();">
        <option value="">Usuario...</option>
        <option value="null">Sin asignar</option>
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($user->id); ?>" <?php if($request->user_id == $user->id): ?> selected="selected" <?php endif; ?>>
             <?php echo substr($user->name, 0, 10); ?>
            
          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
  </div>
  <div class="col-md-2">
      <!--  
*
*    Combo de fuentes
*
-->
      <select name="source_id" class="custom-select" id="source_id" onchange="submit();">
        <option value="">Fuente...</option>
        <?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($item->id); ?>" <?php if($request->source_id == $item->id): ?> selected="selected" <?php endif; ?>>
             <?php echo substr($item->name, 0, 15); ?>
            
          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>


      <?php if(isset($projects)): ?>
      <select name="project_id" class="custom-select" id="project_id" onchange="submit();">
        <option value="">Proyecto...</option>
        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($item->id); ?>" <?php if($request->project_id == $item->id): ?> selected="selected" <?php endif; ?>>
             <?php echo substr($item->name, 0, 15); ?>
            
          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <?php endif; ?>
  </div> 

       
      
     
     <div class="col-md-4">
      <?php 
          $search = str_replace("&#x202C;", "", $request->search);
       ?>
      <input type="text" name="search" id="search" <?php if(isset($search) && $search != ""): ?> value="<?php echo e($search); ?>" <?php endif; ?> placeholder="Busca o escribe">

      <div>
        <?php $cu = $request->created_updated; ?>
        <label class="radio"> Fecha de creación 
          <input type="radio" name="created_updated" value="created" 
          <?php if((isset($cu)&& ($cu == 'created'))||(!isset($cu))): ?>  checked <?php endif; ?> onchange="submit();"> </label> 
        <label class="radio" > o actualizacion 
          <input type="radio" name="created_updated" value="updated" <?php if(isset($request->created_updated)&& ($request->created_updated == "updated")): ?> checked <?php endif; ?> onchange="submit();"> </label>
    </div>  
     </div>
      <div class="col-md-1">
        <input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filtrar" > 
      </div>
  
     
      
    </form>
  