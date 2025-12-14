 // initializate variable
  var lat2 =0;
  var lon2 = 0;
  var map;
  var markers = [];

  
 
  
  window.onload = function() {
    lat2 =Number($('#latitude').val());
    lon2 = Number($('#longitude').val());


    initMap();

    if (single) {

      updatePosition();

    }
    else{
      loadPoints(points);
    }

     
      
  };

      
  function initMap() {
    var venue = {lat: lat2, lng: lon2};
    map = new google.maps.Map(document.getElementById('map'), {
      center: {lat: lat2, lng: lon2},
      zoom: 16
    });

    addMarker(venue, "C");
  }



  function updatePosition(){
    

    var options = {timeout:5000};

    navigator.geolocation.watchPosition(showLocation, errorHandler, options);

   
  }

  function errorHandler(err){
    if(err.code == 1) {
       alert("Error: Access is denied!");
    } else if( err.code == 2) {
       alert("Error: Position is unavailable!");
    }
  }


  function showLocation(position) {
      $("#btn-enviar").show();
      
      $('#currentLat').val(position.coords.latitude);
      $('#currentLon').val(position.coords.longitude);

      var lon1=Number(position.coords.longitude);
      var lat1= Number(position.coords.latitude);
      
      
      
      var distance = calculateDistance(lat1, lon1, lat2, lon2 );
      
      $('#distance').val(distance.toFixed(2));
      $('#distance_label').html(distance.toFixed(2));

     
      console.log(lat1+ ", " + lon1+ ", " + lat2+ ", " + lon2);
      var currentdate = new Date(); 
      var datetime = "Última actualización: " 
                + (currentdate.getHours()<10?'0':'') + currentdate.getHours() + ":"  
                + (currentdate.getMinutes()<10?'0':'') + currentdate.getMinutes() + ":" 
                + (currentdate.getSeconds()<10?'0':'') + currentdate.getSeconds();

      $("#time").html(datetime);
    
      updateMap(lat1,lon1,lat2,lon2);

      updatePosition();
    
    }

  Number.prototype.toRad = function() {
    return this * Math.PI / 180;
  }
  
  // Sets the map on all markers in the array.
  function setMapOnAll(map) {
    for (var i = 0; i < markers.length; i++) {
      markers[i].setMap(map);
    }
  }

  // Removes the markers from the map, but keeps them in the array.
  function clearMarkers() {
    setMapOnAll(null);
  }

  // Shows any markers currently in the array.
  function showMarkers() {
    setMapOnAll(map);
  }

  // Deletes all markers in the array by removing references to them.
  function deleteMarkers() {
    clearMarkers();
    markers = [];
  }

  // Adds a marker to the map and push to the array.
  function addMarker(location, label) {
    var marker = new google.maps.Marker({
      position: location,
      map: map,
      label: label
    });
    markers.push(marker);
  }
  
  var http_request = false;

  function updateMap(lat1, lon1, lat2, lon2){

    var bounds = new google.maps.LatLngBounds();
    location1 = new google.maps.LatLng(lat1,lon1);
    bounds.extend(location1);
    location2 = new google.maps.LatLng(lat2,lon2);
    bounds.extend(location2);

    clearMarkers();
   
    var image = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png';
  
    addMarker(location1, "");
    addMarker(location2, "C");

    // To add the marker to the map, call setMap();
    map.fitBounds(bounds);
    //console.log(lat1 + ',' + lon1+ ',' +  lat2 + ',' +  lon2);
  }

   function calculateDistance(lat1, lon1, lat2, lon2) {
    var R = 6371000; // km
    var dLat = (lat2 - lat1).toRad();
    var dLon = (lon2 - lon1).toRad(); 
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1.toRad()) * Math.cos(lat2.toRad()) * 
            Math.sin(dLon / 2) * Math.sin(dLon / 2); 
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)); 
    var d = R * c;
    return d;
  }


  function updateMultipleMap(lat1, lon1, lat2, lon2){

    var bounds = new google.maps.LatLngBounds();
    location1 = new google.maps.LatLng(lat1,lon1);
    location2 = new google.maps.LatLng(lat2,lon2);
    bounds.extend(location1);
    bounds.extend(location2);


    var image = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png';
  
    addMarker(location1, "");
    addMarker(location2, "C");

    // To add the marker to the map, call setMap();
    map.fitBounds(bounds);
    //console.log(lat1 + ',' + lon1+ ',' +  lat2 + ',' +  lon2);
  }

  function loadPoints(points){
    var bounds = new google.maps.LatLngBounds();
     clearMarkers();
    for (var i = points.length - 1; i >= 0; i--) {
    lat1 = Number(points[i][0]);
    lon1 = Number(points[i][1]);
    updateMultipleMap(lat1,lon1,lat2,lon2);

    
    //addMarker(location,"");
    //bounds.extend(location);
    
    }
    //map.fitBounds(bounds);
  }

