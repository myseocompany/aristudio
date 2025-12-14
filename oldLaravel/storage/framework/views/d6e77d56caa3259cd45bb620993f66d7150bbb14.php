  

<?php $__env->startSection('content'); ?>
<!--
<nav class="navbar navbar-expand-md navbar-white fixed-top bg-white" id="center-nav">
  <div class="container">
    

  </div>
</nav>
-->

<?php if(isset($request->project_id) && ($request->project_id !="")): ?>
        
<h1><?php echo e($project->name); ?></h1>
<!--  
<div class="block-projects">
  <div> <?php echo nl2br($project->description); ?></div class="block-projects">
  </div>
-->
<?php echo $__env->make('tasks.priority_table', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
  <div><a href="/projects/<?php echo e($project->id); ?>">Ver</a> | <a href="/projects/<?php echo e($project->id); ?>/edit">Editar</a></div>

  <?php else: ?>
  <h1>Tasks</h1>
<?php endif; ?>



<?php echo $__env->make('tasks.menu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('tasks.createForm', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('tasks.filter', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


<?php echo $__env->make('tasks.dashBoard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
  <script>
    colors = new Array();
    text = new Array();
    
    <?php $__currentLoopData = $task_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      colors[<?php echo e($status->id); ?>] = "<?php echo e($status->background_color); ?>"; 
      text[<?php echo e($status->id); ?>] = "<?php echo e($status->name); ?>"; 
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    </script>
<!--
        <div class="bigimage-container" id="bigimage-container">
          <img src="" alt="" width="100%" class="task_image_big" id="bigimage-file"> 
        </div>
-->     
<?php echo $__env->make('tasks.data_table', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>













<?php $__env->stopSection(); ?>

<?php $__env->startSection('footerjs'); ?>


 <script>

  $(document).ready(function(){
    /*ocular*/
     $(" #sub").css("visibility", "hidden");
     $(" #sub").css("display", "none");
  });
    
  function hideSubTypes(id) {
    var type = $(".sub-hide_"+id).attr("style");

    if(type == "visibility: visible; display: inline-block;"){
      $(".sub-hide_"+id).css("visibility", "hidden");
      $(".sub-hide_"+id).css("display", "none");
    }else if(type == "visibility: hidden; display: none;"){
      $(".sub-hide_"+id).css("visibility", "visible");
      $(".sub-hide_"+id).css("display", "inline-block");
    }
  }
</script>







<script>
    
$(document).ready(function(){
  update();

});

function openTaskModal(project_id, user_id){
  console.log(user_id);
    cleanFormModal();
    $("#project_id option[value='"+project_id+"']").attr("selected", true);
    //$("#task_user_id option[value='"+user_id+"']").attr("selected", true);
    $("#exampleModal").modal();
    
}

function openEditTaskModal(){
  
  cleanFormModal();
    /*
    console.log(user_id);
    
    $("#task_project_id option[value='"+project_id+"']").attr("selected", true);
    $("#task_user_id option[value='"+user_id+"']").attr("selected", true);
    */
    $("#editModal").modal();
    
  }


function cleanFormModal(){
            $("#task_name").val("");
            /*$("#task_status_id").val("");*/
            /*$("#task_project_id").val("");*/
            /*$("#task_user_id").val("");*/
            $("#task_priority").val("");
            /*$("#task_due_date").val("");*/
            $("#task_not_billing").val("");
            $("#task_points").val("");
            $("#task_file").val("");
            $("#task_url_finished").val("");
            $("#task_description").val("");
          }



function getDataGUI(){
  newEvent={
    name : $("#task_name").val(),
    status_id : $("#task_status_id").val(),
    project_id : $("#task_project_id").val(),
    user_id : $("#task_user_id").val(),
    priority : $("#task_priority").val(),
    due_date : $("#task_due_date").val(),
    not_billing : $("#task_not_billing").val(),
    points : $("#task_points").val(),
    file : $("#task_file").val(),
    url_finished : $("#task_url_finished").val(),
    description : $("#task_description").val(),
    '_token':$("input[name=_token]").val()
  }
  return newEvent;
}

function sendRequest(action, objEvent, method, modal){
  console.log("senrequest")
  console.log(objEvent);
  $.ajax({
    type : method,
    url : "<?php echo e(url('/task_from_calendar')); ?>" + action,
    data:objEvent,
    success:function(msg){
      if(!modal){
        $("#exampleModal").modal('toggle');
        //location.reload();
      }
    },
    error:function(){
      alert("Error");
    }
  });
}















function showTeam(id){
  if($('.toggle_'+id).css("display")=="none")
    $('.toggle_'+id).show();
  else
    $('.toggle_'+id).hide();
}  


jQuery( document ).ready(function(){
  // vars
  startArray = [];
  finalArray = [];

  child = 0;

  function startDrag(ui){

  }  
  function stopDrag(ui){

  }

  function getTaskList(vector){
    array = Array();
    
    for(i=0; i<vector.length; i++){

      if(vector[i].indexOf("task_id_")!=-1)
        array.push( parseInt( vector[i].replace('task_id_', '') ));
        //array.push( vector[i] );
    }
    return array;
  }

  function getParent(vector, child){
    parent = "";
    for(i=0; i<vector.length; i++){

      if(vector[i] == child)
        parent = vector[i+1];
    }
    return parent;
  }

  function setParentAjax(child, parent){
    endpoint = '/tasks/'+child+'/setParent/'+parent;
    console.log(endpoint);
    $.ajax({
        type: 'GET', //THIS NEEDS TO BE GET
        url: endpoint,
        success: function (data) {
            console.log(data);
            
        },
        error: function(data) { 
             console.log(data);
        }
    });

  }


  


  function isEqual(v1, v2){
    equal = true;
    if(v1.length == v2.length){
      for(i=0; i<v1.length; i++)
        if(v1[i]!=v2[i])
          equal = false;
    }else{
      equal = false;
    }
    return equal;
  }

  dragged = null;

  $(function() {
    $("#sortable tbody").sortable({
      cursor: "move",
      placeholder: "sortable-placeholder",
      start: function( event, ui ) {
        startArray = getTaskList($( "#sortable tbody" ).sortable( "toArray" ));
        console.log(startArray);
        console.log("start");
        child = parseInt($(ui.item).attr('id').replace('task_id_', ''));
        console.log(child);
      },
      stop: function( event, ui ) {
        finalArray = getTaskList($( "#sortable tbody" ).sortable( "toArray" ));

        console.log(finalArray);

        console.log("stop");
        if(!isEqual(startArray, finalArray)){
          parent = getParent(finalArray, child);
          if(parent == undefined)
            parent = -1;
          console.log(parent);
          setParentAjax(child, parent);
          //ui.item.html="";
          dragable = ui;
          dragable.item.css('display', 'none');
        }

      },
    }).disableSelection();
  });





});

function updateTypeAjax(tid, tyid){
  console.log(tyid);
  type_id = "0";
  if (!isNaN(parseInt(tyid)))
    type_id = tyid;

    endpoint = '/tasks/'+tid+'/setType/'+type_id;
    
    console.log(endpoint);
    $.ajax({
        type: 'GET', //THIS NEEDS TO BE GET
        url: endpoint,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            //showSubTypes(tid, data);
            
        },
        error: function(data) { 
             console.log(data);
        }
    });

  }

  

  

function updateSubTypeAjax(tid, tyid){


  //console.log(tyid);
  type_id = "0";
  if (!isNaN(parseInt(tyid)))
    type_id = tyid;

    endpoint = '/tasks/'+tid+'/setSubType/'+type_id;
    
   // console.log(endpoint);
    $.ajax({
        type: 'GET', //THIS NEEDS TO BE GET
        url: endpoint,
        dataType: 'json',
        success: function (data) {
            //console.log(data);
            
              $(" #subtype_id_"+tid).css("visibility", "hidden");
              $(" #subtype_id_"+tid).css("display", "none");
            console.log(data);
            location.reload();

            
        },
        error: function(data) { 
             console.log(data);
        }
    });

  }


  function showSubTypes(tid, data){
    //subtype_id_31520
   
     $(" #type_id_"+tid).css("visibility", "hidden");
    $(" #type_id_"+tid).css("display", "none");
    str = '<select name="subtype_id" id="subtype_id_'+tid+'" class="custom-select" onchange="updateSubTypeAjax('+tid+', this.value );">;';
    str += '<option>Select a type...</option>';
    $.each(data, function(i, obj) {
    str += '<option value="'+obj.id+'">'+obj.name+'</option>';
  });

  str += '</select>';



    $("#after_type_"+tid).html(str);
  }

   function getMessages(task_id,creator_user){  
              $("#task_id_m").val(task_id);
                        var str = "";

                        $.ajax({
                        type: "GET",
                        url :"/task/get_messages/"+task_id,
                        success : function(res){
                          $.each(res, function(i, obj) {

                                        str +='<table >';
                                        if(creator_user.id == obj.id){
                                        str +='<tr >';
                                        str += '<td  style="float: left;background-color: transparent;border-top: 0px;"><img  style="clip-path: circle(13px at center);width: 26px;margin-left: 10px;" src="/laravel/storage/app/public/files/users/'+ obj.image_url+' "</td>';
                                        str += '<td class="creator" style="width: 250px;float: left;background-color: transparent; border-top: 0px;" ><input class=" form-control" disabled value="'+obj.description+'" style="background: #ffffff; box-shadow: 0px 4px 4px rgb(50 50 71 / 8%), 0px 4px 8px rgb(50 50 71 / 6%);border-radius: 20px;padding: 15px 12px !important;" >'+'</input></td>';
                                        str +='</tr>';
                                        }else{
                                           str +='<tr >';
                                        str += '<td  style="float: right;background-color: transparent;border-top: 0px;"><img  style="clip-path: circle(13px at center);width: 26px;" src="/laravel/storage/app/public/files/users/'+ obj.image_url+' "</td>';
                                        str += '<td class="user" style="width: 250px;float: rigth;background-color: transparent; border-top: 0px;" ><input class=" form-control" disabled value="'+obj.description+'" style="width: 250px;float: right;background-color: #2196F3; border-radius: 20px;padding: 15px 12px !important;" " >'+'</input></td>';
                                        str +='</tr>';
                                        }
                             str +='</table>';   

                             $("#count_messages_"+task_id).html(str); 
                    
                              });

                          console.log("get"+res);
                        },
                      },"html");
                    }

          /* function sendMessage(task_id,user_id){  
             
                  var description = $("#description_modal_edit_"+task_id).val();
                  var task_id_m = $("#task_id_m").val();
                    $.ajax({
                        type: "GET",
                        url : "/task/message/"+task_id_m+"/"+user_id.id+"/"+description,
                        success : function(res){
                            $("#description_modal_edit_"+task_id).val("");
                            getMessages(task_id_m,user_id); 
                        },
                        error:function(){
                            alert("Error");
                        }
                    },"html");
            }*/

             function sendMessage(task_id,user_id){  
                  var description = $("#description_modal_edit_"+task_id).val();
                  var task_id_m = $("#task_id_m").val();
                    var parametros = {
                            "task_id" : task_id_m,
                            "user_id" : user_id,
                            "description" : description
                    };
                    $.ajax({
                            data:  parametros, //datos que se envian a traves de ajax
                            url:   '/task/message/post', //archivo que recibe la peticion
                            type:  'post', //método de envio
                            beforeSend: function () {
                                    $("#resultado").html("Procesando, espere por favor...");
                            },
                            success:  function (response) { //una vez que el archivo recibe el request lo procesa y lo devuelve
                                       $("#description_modal_edit_"+task_id).val("");
                                       getMessages(task_id_m,user_id); 
                            }
                    });
                
            }






</script>
<?php echo $__env->make('tasks.pieces.ajax', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


<script type="text/javascript">
  function openComentary(task_id){
    var display = toggleComentary(task_id);
  }

  function toggleComentary(task_id){
    var display = $("#comment_"+task_id).css("display");
    if(display == "flex"){
      $("#comment_"+task_id).css("display","none");
    }else{
      $("#comment_"+task_id).css("display","flex");

    }
  }

//enviar formulario al dar enter
  /*
  document.getElementById('task_priority').addEventListener('keydown', inputCharacters);
  document.getElementById('task_name').addEventListener('keydown', inputCharacters);
  document.getElementById('description').addEventListener('keydown', inputCharacters);
  document.getElementById('task_points').addEventListener('keydown', inputCharacters); 
  document.getElementById('task_url_finished').addEventListener('keydown', inputCharacters);
       function inputCharacters(event) {
        if (event.keyCode == 13) {
           objEvent = getDataGUI();
          sendRequest('',objEvent, "GET");
        }
      }
  */                                          
</script>
<style type="text/css">
      th, td {
        padding: 9px 0px !important;
        }
        .modal_messages{
          position: fixed;
          top: 0;
          left: 0;
          z-index: 999999999999999;
          display: none;
          width: 100%;
          height: 100%;
          overflow: hidden;
          outline: 0;
          box-shadow: 0px 10px 10px black; 
        }
   
        .modal-content-messages {
         box-shadow: 0px 10px 10px #0000005c;
             position: relative;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
            width: 70%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: .3rem;
            outline: 0;
            top:175px;
            left: 65px;
        }



     </style>
                    
                  



<?php $__env->stopSection(); ?>


<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>