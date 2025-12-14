
function mostrarReferencia(){
	//Si la opcion con id Conocido_1 (dentro del documento > formulario con name fcontacto >     y a la vez dentro del array de Conocido) esta activada
	if (document.fcontacto.Conocido[1].checked == true) {
	//muestra (cambiando la propiedad display del estilo) el div con id 'desdeotro'
	document.getElementById('desdeotro').style.display='block';
	//por el contrario, si no esta seleccionada
	} else {
	//oculta el div con id 'desdeotro'
	document.getElementById('desdeotro').style.display='none';
	}
}



console.log("myseo.js");