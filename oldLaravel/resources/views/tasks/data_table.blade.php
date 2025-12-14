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


@php    $title = "Tareas de hoy";    @endphp
@include("tasks.data_table_head") 
   @foreach($model as $item)
        @if(\Carbon\Carbon::parse($item->due_date)->isToday()  && $item->type_id != 119 )  
          @include("tasks.data_table_body") 
        @endif
  @endforeach
@include("tasks.data_table_footer") 


<!-- OTRA TABLA -->
@php $title = "Tareas"; @endphp
@include("tasks.data_table_head") 
  @foreach($model as $item)
    @if(!\Carbon\Carbon::parse($item->due_date)->isToday() && $item->type_id != 119 ) 
      @include("tasks.data_table_body") 
    @endif
  @endforeach
@include("tasks.data_table_footer") 




<!-- FIN TABLE OTRAS -->

    <div>{{ $sumPoints }} ptos/ {{ $countTask }}</div>


    
  </div>

  @include("tasks.modal_update")