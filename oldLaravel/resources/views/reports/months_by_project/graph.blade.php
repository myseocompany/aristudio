<!--  
	*
	*    Tabla reportes
	*
	-->

<script src="https://cdn.zingchart.com/zingchart.min.js"></script>
  <style>
    
 
    .chart--container {
      height: 400px;
      width: 100%;
      min-height: 150px;
    }
 
    .zc-ref {
      display: none;
    }
  </style>


<!-- Inicio del gráfico -->
<div id="myChart" class="chart--container">
    
  </div>
  <?php function hasPoints($project_id, $time_span, $data){
      $res = false;
      $count = 0;
      for($i=0; $i<$time_span; $i++){
        if($data[$project_id][$i]>0)
          $res = true;
        $count += $data[$project_id][$i];
      }

      return $res;
  } ?>
  <script>

    colors = ['rgba(35, 35, 59, 1)', 'rgba(44, 66, 104, 1)', 'rgba(0, 123, 186, 1)', 'rgba(0, 169, 226, 1)', 'rgba(124, 205, 244, 1)', 'rgba(188, 227, 250, 1)', 'rgba(155, 156, 155, 1)', 'rgba(178, 176, 176, 1)', 'rgba(197, 198, 198, 1)', 'rgba(235, 235, 235, 1)'];

 
    ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "b55b025e438fa8a98e32482b5f768ff5"]; // window.onload event for Javascript to run after HTML
    // because this Javascript is injected into the document head
    window.addEventListener('load', () => {
      // Javascript code to execute after DOM content
 
      // full ZingChart schema can be found here:
      // https://www.zingchart.com/docs/api/json-configuration/
      const myConfig = {
        type: 'bar',
        title: {
          text: '',
          fontSize: 24,
          color: '#5d7d9a'
        },
        legend: {
          draggable: true,
        },

        scaleX: {
          // set scale label
          label: {
            text: 'Months'
          },<?php $i=0; ?>
          // convert text on scale indices
          labels: [@for($i=0;$i< count($projects); $i++) @if( hasPoints($i, $time_span, $data)) '{{substr($projects[$i]->name, 0, 8)}}' @if($i <(count($projects)-1)) @endif,  @endif @endfor]
          ,
        },
        scaleY: {
          // scale label with unicode character
          label: {
            text: 'Points'
          }
        },

        plot: {
          // animation docs here:
          // https://www.zingchart.com/docs/tutorials/design-and-styling/chart-animation/#animation__effect
          animation: {
            effect: 'ANIMATION_EXPAND_BOTTOM',
            method: 'ANIMATION_STRONG_EASE_OUT',
            sequence: 'ANIMATION_BY_NODE',
            speed: 275,
          }
        },
        
        series: [
          @for($i=0; $i<$time_span; $i++)
          
          {
            // plot 1 values, linear data
            values: [@for($j=0;$j< count($projects); $j++) @if( hasPoints($j, $time_span, $data)) {{$data[$j][$i]}}@if($j<(count($projects)-1)) @endif,  @endif @endfor],
            text: '{{$time_span_array[$i][0]}}',
            backgroundColor: colors[{{$i}}]
          }
          @if($i<($time_span-1)) , @endif 
          @endfor
        ]
      };
 
      // render chart with width and height to
      // fill the parent container CSS dimensions
      zingchart.render({
        id: 'myChart',
        data: myConfig,
        height: '100%',
        width: '100%'
      });
    });
  </script>

<!--- Fin del gráfico --->
<!--Chart Placement[2]-->