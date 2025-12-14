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
              @foreach($tasksGroup as $item)
              <?php 
              $sumPoints += $item->sum_points;
              $countPoints += $item->count_points;
               ?>
               @if(($count>1) && ($count % $page_length)==0)
                
            </ul>
          </div>
          <div class="no_print">
            <ul class="groupbar bb_hbox">
                
              @endif 
              <li class="groupBarGroup" style="background-color: @if(isset($item->status->background_color)&&($item->status->background_color!="")){{$item->status->background_color.';'}} @else @if(isset($item->project->color)&&($item->project->color!="")){{$item->project->color.';'}} @else #ccc; @endif @endif<?php 
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
               ?>" page="{{$page_length}}">
                <h3>{{$item->sum_points}} pts / {{$item->count_points}}</h3>
               
                <div>
                @if(isset($item->status))
                  <a href="#{{$item->status->name}}">{{$item->status->name}}</a>
                @elseif(isset($item->project))
                  <a href="#{{$item->project->name}}">{{$item->project->name}}</a><br><a href="/projects/{{$item->project->id}}"></a>
                @else
                  <a href="#null">Sin estado</a>
                @endif
                </div>
              </li>  
              <?php $count++; ?>        
              @endforeach
            </ul>
          </div>
    <?php 
        
        }
     ?>  
<div>{{$sumPoints}} puntos ( {{$countPoints}} Tareas)</div> 


<?php 
  $url_charge_account= "charge_account" . substr($_SERVER["REQUEST_URI"], 6);
?>
@if($request->status_id==56)
<a href="/{{$url_charge_account}}">Download Charge Account</a>
@endif

<?php }?>