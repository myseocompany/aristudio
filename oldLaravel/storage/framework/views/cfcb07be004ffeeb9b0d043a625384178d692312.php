<script>
    
    function updateTaskFromModalAJAX(id){
        $("#btn_edit_"+id).attr('disabled','disabled');

        task = getDataGUIEdit(id);

        
        var url = '/tasks/'+id+'/ajax/update';
        $.ajax({
            type : "POST",
            url : url,
            data:task,
            done: onDone,
            success: onSuccess,
            error: onError,
        });
    }

    function onDone(){
        console.log("donde ajax");
    }

    function onSuccess(){
        console.log("ok ajax");
    }

    function onError(){
        console.log("error ajax");
    }


    function getDataGUIEdit(id){
        task ={
            id : id,
            name : $("#name_"+id).val(),
            status_id : $("#status_id_"+id).val(),
            project_id : $("#project_id_"+id).val(),
            user_id : $("#user_id_"+id).val(),
            priority : $("#priority_"+id).val(),
            due_date : $("#due_date_"+id).val(),
            not_billing : $("#not_billing_"+id).val(),
            points : $("#points_"+id).val(),
            file : $("#file_"+id).val(),
            url_finished : $("#url_finished_"+id).val(),
            description : $("#description_"+id).val(),
            '_token':$("input[name=_token]").val()
        }
        return task;
       
    }
  
        



</script>