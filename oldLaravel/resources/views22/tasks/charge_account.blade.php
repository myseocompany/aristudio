@extends('layout_charge_account')

@section('content')

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

@foreach($tasksGroup as $item)
  <?php 
    $amount += $item->sum_points;

  ?>      
@endforeach
<table>
    <tbody>
      <tr>
        <td class="table-body">Manizales, {{$date}}</td>
      </tr>
    <tr>
      <td class="header">
          MY SEO COMPANY <br>
          NIT. 900.489.574-1 <br>
          DEBE A: <br>
          {{$user->name}}<br> 
          C.C {{$user->document}} <br>
          LA SUMA DE: <br>
          <br>
          @php
          if(isset($user->hourly_rate))
            $amount = $amount * $user->hourly_rate;
          else
            $amount = $amount * 10000;
          
          @endphp
        
          {{$val = number_words($amount,"pesos","y","centavos")}} (${{number_format($amount,0)}})
      </td>

  @while($count < $tasksGroup->count())
    @foreach($tasksGroup as $item)
      <?php 
        $sumPoints += $item->sum_points;
        $countPoints += $item->count_points;
      ?>
      <tr>
        <td class="table-body">
          @if(isset($item->status))
            {{$item->status->name}}: {{$item->sum_points}}
          @elseif(isset($item->project))
            {{$item->project->name}}: {{$item->sum_points}}
          @else
            Sin estado: {{$item->sum_points}}
          @endif
        </td>
      </tr>  
      <?php 
        $count++; 
      ?>        
    @endforeach
  @endwhile
  </tbody>
</table>
<table>
  <tbody>
    <tr>
      <td class="table-body">{{$sumPoints}} puntos ( {{$countPoints}} Tareas)</td>
    </tr>
   
  </tbody>
</table>
<br>
<table>
<tbody>
  <tr>
    <td class="table-body">
      Declaro que: <br>
      • Pertenezco al Régimen Simplificado, por tanto no estoy obligado a cobrar el Impuesto sobre la Ventas <br>
      • No estoy obligado a expedir factura de venta según el artículo 616-2 del Estatuto Tributario. <br>

      Cordialmente, <br> <br> <br> <br>
      {{$user->name}}<br>
      C.C {{$user->document}} <br> 
      Dirección: {{$user->address}} <br>
      Celular: {{$user->phone}} <br>

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
@endsection



