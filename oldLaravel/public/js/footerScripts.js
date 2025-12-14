function updateStatusAjax(id){

            base_url = "/tasks/"+id+"/updateStatusMini/";
            var status_id = $("#status_id_"+id).val();
            var task_id = $("#task_id_"+id).val();
            var token = $("#token_id_"+id).val();

            var dataString = 'status/'+status_id+'/token/'+token;
            console.log(base_url+dataString);
            $.ajax({
                type: "GET",
                url : base_url+dataString,
                success : function(data){
                    
                    //notifyMe(data);
                }
            },"html");

}

function updateLavelStatus(tid, sid){
    selector = '#task_cel_'+tid;
    console.log(selector);
    
    $(selector).css('background-color', colors[sid]);
    $(selector+' a').text(text[sid]);
    console.log(colors[sid]);
}

function updateNextStatusAjax(id){
    console.log("updating");
            base_url = "/tasks/"+id+"/updateNextStatusMini/";
            var status_id = $("#status_id_"+id).val();
            var task_id = $("#task_id_"+id).val();
            var token = $("#token_id_"+id).val();

            var dataString = 'status/'+status_id+'/token/'+token;
            console.log(base_url+dataString);
            $.ajax({
                type: "GET",
                url : base_url+dataString,
                success : function(data){
                    
                    updateLavelStatus(id, data);
                },
                errir : function(data){
                    console.log(data);
                }
            },"html");
}

function setStatusAjax(tid, ts_id){
    
    status_id = "0";
    if (!isNaN(parseInt(ts_id)))
      status_id = ts_id;
  
      endpoint = '/tasks/setStatus/'+tid+'/'+status_id;
      
      $.ajax({
          type: 'GET', //THIS NEEDS TO BE GET
          url: endpoint,
          dataType: 'json',
          success: function (data) {
              console.log(data);
              updateLavelStatus(tid, status_id);
              
          },
          error: function(data) { 
               console.log(data);
          }
      });
  
    }


function updateUserAjax(tid, uid){
    base_url = "/tasks/"+tid+"/updateUser/";
    
    var token = $("#token_id_"+tid).val();

    var dataString = 'user/'+uid+'/token/'+token;
    console.log(base_url+dataString);
    $.ajax({
        type: "GET",
        url : base_url+dataString,
        success : function(data){
            $('.toggle_'+tid).hide();
            $('#task_user_image_'+tid).attr('src', "/laravel/storage/app/public/files/users/" +data);
            //$('#task_id_'+tid).hide(3000);
            console.log(data);

        }
    },"html");

    /* old 
            base_url = "https://myseo.com.co/tasks/"+tid+"/updateUser/";
            
            var token = $("#token_id_"+tid).val();

            var dataString = 'user/'+uid+'/token/'+token;
            console.log(base_url+dataString);
            $.ajax({
                type: "GET",
                url : base_url+dataString,
                success : function(data){
                    $('#task_user_image_'+tid).attr('src', "/laravel/storage/app/public/files/users/" +data);
                    
                    console.log(data);

                }
            },"html");
*/
}


function updateObserverAjax(id){

            base_url = "/tasks/"+id+"/observer/";
            var observer_id = $("#observer_id").val();
            var task_id = $("#task_id_"+id).val();
            var token = $("#token_id_"+id).val();

            var dataString = observer_id+'/token/'+token;
            console.log(base_url+dataString);
            $.ajax({
                type: "GET",
                url : base_url+dataString,
                success : function(data){
                    $('#observer_img_'+id).css('display', data);
                    console.log(data);
                    console.log('#observer_img_'+id);

                }
            },"html");

}




//  Scripts del boton de contar tiempo
function toggleButton(btn){
    
    
    console.log(btn);
    if(btn=='play'){
        starTask();
    }
    if(btn=='stop'){
        stopTask();
    }

}



/*Actualizar due_date de tareas*/
function updateDate(id){

    base_url = "/tasks/"+id+"/updateDate/";
    var status_id = $("#status_id_"+id).val();
    var task_id = $("#task_id_"+id).val();
    var token = $("#token_id_"+id).val();

    var dataString = 'status/'+status_id+'/token/'+token;
    console.log(base_url+dataString);
    $.ajax({
        type: "GET",
        url : base_url+dataString,
        success : function(data){
            $("#date_"+id).html("<p>"+data.response+"</p>");        
        },
        error:function(){
            alert("Error");
        }
    },"html");
}

function validateRange(input) {
    var value = parseFloat(input.value);
    if (value < 0 || value > 2) {
      alert("Please enter a decimal number between 0 and 2.");
      input.value = "";
    }
  }