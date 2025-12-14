


Date.prototype.GetFirstDayOfWeek = function(date) {
  date = new Date(date);
  var day = date.getDay();  // Obtén el día de la semana (0 es domingo, 6 es sábado)
  if (day !== 0)
      date.setDate(date.getDate() - day);  // Si no es domingo, retrocede hasta el domingo
  return date;
}

Date.prototype.GetLastDayOfWeek = function(date) {
  date = new Date(date);
  var day = date.getDay();
  if (day !== 0)
      date.setDate(date.getDate() - day + 6);  // Encuentra el primer día de la semana y añade 6 días para llegar al sábado
  else
      date.setDate(date.getDate() + 6);  // Si es domingo, simplemente añade 6 días
  return date;
}

function getNextWeekOld(){
  console.log("next week");
  var date = new Date();
  date.setDate(date.getDate()+7);  
  //console.log(date);
  var firstday = new Date().GetFirstDayOfWeek(date);
  var lastday = new Date().GetLastDayOfWeek(date);
  
  $('#from_date').val(dateToString(firstday));
  $('#to_date').val(dateToString(lastday));

}

function getNextWeek(){
  console.log("next week");
  var date = new Date();
  date.setDate(date.getDate()+7);  // Get the date 7 days from now
  
  // Get the first day of that week (Monday)
  var firstday = new Date(date);
  firstday.setDate(date.getDate() - (date.getDay() === 0 ? 6 : date.getDay() - 1)); // Adjusted to make Monday the first day of the week
  
  // Get the last day of that week (Sunday)
  var lastday = new Date(firstday);
  lastday.setDate(firstday.getDate() + 6);
  
  $('#from_date').val(dateToString(firstday));
  $('#to_date').val(dateToString(lastday));
}



function getThisWeek(){
  
  var firstday = new Date().GetFirstDayOfWeek(new Date());
  var lastday = new Date().GetLastDayOfWeek(new Date());
  
  $('#from_date').val(dateToString(firstday));
  $('#to_date').val(dateToString(lastday));

}


function getLastWeek(){
  var date = new Date();
  //console.log(date);
  date.setDate(date.getDate()-7);  
  //console.log(date);
  var firstday = new Date().GetFirstDayOfWeek(date);
  var lastday = new Date().GetLastDayOfWeek(date);
  
  $('#from_date').val(dateToString(firstday));
  $('#to_date').val(dateToString(lastday));

}

function dateToString(date){

  var dd = date.getDate();
  var mm = date.getMonth()+1; //January is 0!
  var yyyy = date.getFullYear();

  if(dd<10) {dd = '0'+dd} 

  if(mm<10) {mm = '0'+mm} 

  
  //alert(yyyy + '-' + mm + '-' + dd);
  return yyyy + '-' + mm + '-' + dd;
}

function getDate(interval){
  var date = new Date();
  date.setDate(date.getDate() + interval);

  return dateToString(date);
}


function getLastMonth(){
	var date = new Date();
	date.setDate(0);
	$('#to_date').val(dateToString(date));

	date.setDate(1);
	$('#from_date').val(dateToString(date));
  	
}

function getNextMonth(){
  let date = new Date();
  date.setMonth(date.getMonth() + 1);
  date.setDate(0);
  $('#to_date').val(dateToString(date));
  
  date.setDate(1);
  $('#from_date').val(dateToString(date));
}

function getCurrentMonth(){
	var date = new Date();
	var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
	var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
	var date = new Date();
	date.setDate(0);
	$('#to_date').val(dateToString(lastDay));
	date.setDate(1);
	$('#from_date').val(dateToString(firstDay));
  	
}

function getCurrentYear(){
  var date = new Date();
  console.log(date.getFullYear()+ "-" + date.getMonth() + date.getDay());
  var firstDay = new Date(date.getFullYear(), 0, 1);
  console.log(firstDay);

  var lastDay = new Date(date.getFullYear(), 11, 31);
console.log(lastDay);


  $('#to_date').val(dateToString(lastDay));
  $('#from_date').val(dateToString(firstDay));
    
}

  function update(){
    filter = $( "#filter option:selected" ).val();
    switch (filter){
      //case "": $('#from_date').val(""); $('#to_date').val(""); break;
      case "0": $('#from_date').val(getDate(0)); $('#to_date').val(getDate(0)); break;
      case "-1": $('#from_date').val(getDate(-1)); $('#to_date').val(getDate(-1)); break;
      case "1": $('#from_date').val(getDate(1)); $('#to_date').val(getDate(1)); break;
      
      case "thisweek":getThisWeek(); break;
      
      case "nextweek":getNextWeek(); break;

      case "-7":$('#from_date').val(getDate(-7)); $('#to_date').val(getDate(-1)); break;
      case "-14":$('#from_date').val(getDate(-14)); $('#to_date').val(getDate(-1)); break;
      case "-30":$('#from_date').val(getDate(-30)); $('#to_date').val(getDate(-1)); break;
      
      case "7":$('#from_date').val(getDate(0)); $('#to_date').val(getDate(+6)); break;
      case "14":$('#from_date').val(getDate(0)); $('#to_date').val(getDate(+13)); break;
      
      case "-60":$('#from_date').val(getDate(-59)); $('#to_date').val(getDate(0)); break;
      case "-90":$('#from_date').val(getDate(-89)); $('#to_date').val(getDate(0)); break;
      case "lastweek":getLastWeek(); break;
      case "lastmonth":getLastMonth();break;
      case "currentmonth":getCurrentMonth();break;
      case "nextmonth":getNextMonth();break;
      case "currentyear":getCurrentYear();break;

    }
  }
  function cleanFilter(){
      //filter = $( '#filter option[value=""]' ).attr('selected', 'selected');
      $('#filter').val("");
  }


function showImageFile(url){
	image = document.getElementById("bigimage-file");
	image.src = url;
  div = document.getElementById('bigimage-container')
  div.style.display = 'inline-block';
  
}


function hideImageFile(url){
	div = document.getElementById('bigimage-container')
  div.style.display = 'none';
}

function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
  ev.dataTransfer.setData("task_id", ev.target.id);
}

function drop(ev) {
  ev.preventDefault();
  var child = ev.dataTransfer.getData("task_id");
  var textPadre =ev.target.id;
  var parent = textPadre.replace('task_cel_','');   
  var url = "/tasks/"+child+"/parent/"+parent;
  data =  $("#codigoSeg").serialize();
  $.post(
    url,data
  ,function(result){
      console.log(result);
    });
}


