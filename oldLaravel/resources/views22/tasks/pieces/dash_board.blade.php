<?php $countPoints = 0; ?>

        <?php while($count < $tasksGroup->count()){ ?> 
          <div  class="no_print">
            <ul class="groupbar bb_hbox">
              @foreach($tasksGroup as $item)
              <?php 
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
                  }
               ?>" page="{{$page_length}}">
                <h3>{{$item->count_points}} Piezas</h3>
               
                <div>
                @if(isset($item->status))
                  <a href="#{{$item->status->name}}" onclick="openView('/pieces?status_id={{$item->status_id}}');">{{$item->status->name}}</a>
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
<div>{{$countPoints}} Piezas</div> 