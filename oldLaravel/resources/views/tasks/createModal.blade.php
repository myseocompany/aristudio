 
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="" enctype="multipart/form-data" id="form_modal">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Task</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
        
        {{ csrf_field() }}

          <div class="row">
            <div class="col-md">
              <div class="form-group">
                <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required">
                <input type="text" class="form-control" id="points" name="points" placeholder="Points" >
                <input type="datetime-local" class="form-control" id="due_date" name="due_date" placeholder="YYYY/MMM/DD" required="required" value="<?php echo date('Y-m-d\TH:i');?>">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" id="description" placeholder="Description"></textarea>

              </div>


              <div class="form-group">
                <input type="hidden" name="project_id" id="project_id">
                <input type="hidden" class="form-control" id="priority" name="priority" placeholder="Priority" >
                
                <input type="hidden" class="form-control col-1" id="not_billing" name="not_billing">
                <input type="hidden" id="user_id" name="user_id">
                <input type="hidden" name="status_id" id="status_id" value="1">
                
                
              </div> 
            </div>
           

          </div>
           
          
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="sendData();">Save</button>
       <!-- <button type="submit" class="btn brn-sum btn-primary my-2 my-sm-0">Submit</button> -->

      </div>
    </div>
    </form>
  </div>
</div>

       
          
    