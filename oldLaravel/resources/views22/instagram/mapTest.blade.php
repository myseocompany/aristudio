@extends('layout')

@section('content')
<style>
#map { 
	height: 400px; /* The height is 400 pixels */ 
	width: 100%; /* The width is the width of the web page */ 
	}
	
.storesDiv{
	float: left;
	border-top: 1px #e5e5e5 solid;
	border-bottom: 1px #e5e5e5 solid;
	height: 11rem;
	padding-top: 1rem;
	padding-bottom: 1rem;
	}

#storeContainer .storesDiv .storeDistance{
	display: inline-block;
	float: right;
	font-weight: bold;
	color: gray;
	}

#storeContainer .storesDiv .storeName{
	text-transform:uppercase;
	display: inline-block;
	font-weight: bold;
	}

</style>


<h1> Páginas de prueba para mapa de Tiendas BATA</h1>

<div id="map"></div>

<div id="searchBar">
	<input type="text" id="myInput" onkeyup="searchStore()" placeholder="Busca una tienda en tu ciudad..">
	<button onclick="searchStore()">Filtrar</button>
</div>

<div id="stores"></div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzt-h56B68qq5KNyoSySHSUiRs0qNDMDE&callback=initMap"> </script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>		
		var map;
		var arrayStores = [];
		
		function getDistance(userLng, userLat, storeLng, storeLat){
			var xDistance = (storeLng - userLng)*(storeLng-userLng);
			var yDistance = (storeLat - userLat)*(storeLat- userLat);			
			var storeDistance =Math.sqrt(xDistance+yDistance);
			return storeDistance;
		}
		function toRadians(degrees){
		  var pi = Math.PI;
		  return degrees * (pi/180);
		}
		function distFrom(lng1, lat1, lng2, lat2) {
		    earthRadius = 6371000; //meters
		    dLat = toRadians(lat2-lat1);
		    dLng = toRadians(lng2-lng1);
		    a = Math.sin(dLat/2) * Math.sin(dLat/2) +
		               Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
		               Math.sin(dLng/2) * Math.sin(dLng/2);
		    c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
		    dist = (earthRadius * c);

		    return dist;
		}
		
		function drawStoresDiv(data){
			//console.log(data);
			$('#stores').empty();
			$('#stores').append('<div id="storeContainer" class=container></div>');
			for (var i = 0; i < data.length; i++) {
			//for (var i = 0; i < 10; i++) {
				$('#storeContainer').append('<div id="storeDiv'+i+'" class="col-xs-12 col-md-6 storesDiv"></div>');
				$('#storeDiv'+i).append("<div class='storeName'> <a href='#map' onclick=showStore("+data[i].index+")>"+data[i].name+"</a></div>");
				$('#storeDiv'+i).append("<div class='storeDistance'>"+((data[i].distance)/1000).toFixed(2)+"km </div>");
				$('#storeDiv'+i).append("<div class='storeAddress'>"+data[i].address+"</div>");
				$('#storeDiv'+i).append("<div class='storeCellphone'> tel: "+data[i].cellphone+" </div");
				$('#storeDiv'+i).append("<div class='storeCity'>"+data[i].city+"</div>");
				
			}
		}


		function drawStores(map, userLng, userLat){
			//var arrayStores = [];
			console.log("dibujando tiendas");
			var zoom =$.getJSON("/js/BataStores.json", function(data ){
				console.log(this.arrayStores );
				arrayStores = data;
				
				for (storeId in arrayStores){
					var storeLng = parseFloat(arrayStores[storeId]["longitude"]);
					var storeLat  = parseFloat(arrayStores[storeId]["latitude"]);
					var posStore = {lng:storeLng,lat:storeLat};
					var marker = new google.maps.Marker({position: posStore, map: map, title:storeId}); 
					var storeDistance = distFrom(userLng, userLat, storeLng, storeLat);
					arrayStores[storeId].distance = storeDistance;
					arrayStores[storeId].index = storeId;
					arrayStores[storeId].mark=marker;


				}
				var arrayStoresSort = arrayStores.slice();
				arrayStoresSort.sort(function (a, b){
					return (a.distance - b.distance)
				});
				drawStoresDiv(arrayStoresSort);				
			});

		}
		
		function getZoomFactor(lng1, lat1, lng2, lat2){
			var sw = new google.maps.LatLng(lat1, lng1);
			var ne = new google.maps.LatLng(lat2, lng2);
			var bounds = new google.maps.LatLngBounds(sw, ne);
			var GLOBE_WIDTH = 256; // a constant in Google's map projection
			var west = sw.lng();
			var east = ne.lng();
			var angle = east - west;
			if (angle < 0) {
			  angle += 360;
			}
			var zoom = Math.round(Math.log(pixelWidth * 360 / angle / GLOBE_WIDTH) / Math.LN2);
			return zoom;
		}
			
		function centerMap(location){
			const userLng = location.coords.longitude;
			const  userLat = location.coords.latitude;
			var uluru = {lat:  userLat, lng: userLng};
			var userMarker = new google.maps.Marker({position: uluru, map: map});
			
			//map = new google.maps.Map( document.getElementById('map'), {zoom: 10, center: uluru});
			
			drawStores(map,userLng, userLat);
			//console.log( zoom );
			map.setZoom( 14 );
			map.setCenter(uluru);
	
		}
				
		function initMap() { // The location of Uluru 
			
			var uluru = {lat:  4.6097100, lng: -74.0817500}; //center at Bogota
			// The map, centered at Uluru
			map = new google.maps.Map( document.getElementById('map'), {zoom: 5, center: uluru});
			// The marker, positioned at Uluru
			drawStores(map,uluru.lng,uluru.lat);

			if (navigator.geolocation) {
				
				 var ubicacion = navigator.geolocation.getCurrentPosition(centerMap, errorGeo);
			}else{
				console.log("no tiene autorizacion para ver la localización");
			}

			//var marker = new google.maps.Marker({position: uluru, map: map}); 
		}

		function errorGeo(error){

			console.log("no cargo la ubicaicion del usuario");
		}

		function searchStore() {
			// Declare variables
			var input, filter, i;
			var storesFiltered=[];
			var uluru = {lat:  4.6097100, lng: -74.0817500};
			input = document.getElementById('myInput');
			filter = input.value.toUpperCase();
			if(arrayStores.length>0){
				
				// Loop through all list items, and hide those who don't match the search query
				for (i = 0; i < arrayStores.length; i++) {
					storeJSON = arrayStores[i];
					if(storeJSON.city.toUpperCase().indexOf(filter)>-1){
						storesFiltered.push(storeJSON);
					}
					else{
						storeJSON.mark.setMap(null);
					}
				}
			drawStoresDiv(storesFiltered);
			uluru={lat:parseFloat(storesFiltered[0].latitude),lng:parseFloat(storesFiltered[0].longitude)};
			map.setZoom(14);
			map.setCenter(uluru);
			}else{console.log("Aun no hay tiendas cargadas"); }
		}  
		
		function showStore(storeIndex){
			var store = arrayStores[storeIndex];
			var storeLng = parseFloat(store.longitude);
			var storeLat = parseFloat(store.latitude);
			var posStore = {lng:storeLng,lat:storeLat};
			var contentString = "<h3>"+store.name+"</h3>"+
			"<div>"+store.address+"</div>"+
			"<div>"+store.city+"</div>"+
			"<div> tel:"+store.cellphone+"</div>";
			map.setCenter(posStore);
			map.setZoom(14);
			var infowindow = new google.maps.InfoWindow({content: contentString});
			//var markerClick = new google.maps.Marker({position: posStore, map: map, title:store.name, icon:'https://developers.google.com/maps/documentation/javascript/examples/full/images/info-i_maps.png'});
			var markerClick = store.mark;
			infowindow.open(map, markerClick);
			}
</script>

         <!-- Vozy Widget -->

        <script>
        var cred;
        (function() {
                cred = {
                "accountId": "f639f93d0293e96c06928c28f46bd57c",
                "c2cId": "6fd1426116855471ff0cb12d4ad777e2"
            }
            var script_tag = document.createElement('script');
            script_tag.setAttribute("type", "text/javascript");
            script_tag.setAttribute("src", "https://web.vozy.co/lib/widget/vozy.js");
            (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
        })();
        </script>

        <!-- End Vozy Widget -->


@endsection
