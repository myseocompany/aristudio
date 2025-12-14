<!DOCTYPE html>
<html>
  <head>
    <title>Proximity</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="/css/dashboard.css?id=<?php echo rand(1, 10000); ?>">
    <script type="text/javascript" src="/js/proximity.js?<?php echo rand(1, 10000); ?>"></script>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 70%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>
    <div class="form-group mb-2">
      <div class="container">
        <div class="row">
          <form action="/proximity" method="POST">
            <div class="col">
            <h2>Acercate el Casino</h2>
          </div>
           {{ csrf_field() }}    
          
          @foreach($venues as $item)    
              <div class="col">
               Estas a&nbsp;<strong><span id="distance_label"></span>&nbsp;metros</strong> de nuestro nuevo casino. 
               <br>
               Muévete lo más cerca que puedas y cuando estés listo oprime enviar.
              </div>
              

              <div class="col">
                <button type="submit" class="btn btn-primary mb-2" id="btn-enviar">Enviar</button> 
                <script> </script>
              </div>
              
              


                <input type="hidden" id="currentLat" name="currentLat">
                <input type="hidden" id="currentLon" name="currentLon">
              <input type="hidden" id="latitude" name="latitude" value="{{$item->latitude}}">
              <input type="hidden" id="longitude" name="longitude" value="{{$item->longitude}}">
              <input type="hidden" id="distance" name="distance">
              
          @endforeach
          
          
        </form>
      </div>
    </div>

   </div>


    <div id="map"></div>
    <div class="col">
                <span id="time"></span> <span>Desarrollado por <a href="http://www.myseocompany.co">My SEO Company</a></span>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    
    
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script>
    var single = false;
  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD8w2XDhgvodr73PMTO7IF5ZsSNyiZDNDs&callback=initMap"
    async defer></script>

    <script>
      $("#btn-enviar").hide();
      var points = Array();
      @foreach ($model as $item) 
        points.push( new Array({{ $item->latitude}},{{$item->longitude }}));
      @endforeach
      console.log(points);      

      

    </script>
  </body>
</html>