<!-- Modal create task -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Task</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
  
          <form method="POST" action="/tasks" enctype="multipart/form-data">
          {{ csrf_field() }}
  
            <div class="row">
              <div class="col-md">
                <div class="form-group">
              <label for="name">Name</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required">
            </div>
  
            <div class="form-group">
              <label for="user_id">Project</label>
              <select name="project_id" id="project_id" class="form-control" required="required">
                  <option value="">Select a Project</option>
                  @foreach ($projects as $project)
                    <option value="{{$project->id}}" @if($item->project_id == $project->id) selected="" @endif>{{$project->name}}</option>
                  @endforeach
                </select>
            </div>
  
            <div class="form-group">
              <label for="priority">Priority</label>    
              <select name="priority" id="priority" class="form-control">
                <option value="0">Select a Priority</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
              </select>
            </div>
              
  
            <div class="form-group">
              <label for="name">Due Date</label>
              <input type="date" class="form-control" id="due_date" name="due_date" placeholder="YYYY/MMM/DD" required="required" value="<?php echo date('Y-m-d');?>">
            </div>
  
  
            <div class="form-group row">
              <label for="name" class="col-6">Not Billing</label>
              <input type="checkbox" class="form-control col-1" id="not_billing" name="not_billing">
            </div> 
  
  
              </div>
              <div class="col-md">
                <div class="form-group">
              <label for="user_id">User</label>
              <select name="user_id" id="user_id" class="form-control">
                <option value="">Select a User</option>
              @foreach ($users as $user)
                  <option value="{{$user->id}}" @if ($user->id == $request->user_id) selected="selected" @endif>{{$user->name}}</option>
              @endforeach
              </select>
            </div>
            <div class="form-group">
             <label for="status_id">Status</label>
               <select name="status_id" id="status_id" class="form-control" >
              @foreach($task_status as $item)
                  <option value="{{$item->id}}">{{$item->name}}</option>
              @endforeach
              ?>
              </select>
            </div>
  
            <div class="form-group">
              <label for="points">Points</label>    
              <input type="text" class="form-control" id="points" name="points" placeholder="Points">
            <div class="form-group">
              <label for="name">Archivo</label>
              <input type="file" class="form-control" id="file" name="file" placeholder="Name">
            </div>
  
            <div class="form-group">
            <label for="points">Url Finished Task </label>
              <input class="form-control" name="url_finished" id="url_finished" placeholder="Url" value="" ></input>
            </div>
              </div>
  
            </div>
              <input type="hidden" name="from" id="from" class="form-control" value="project">
            </div>
  
  
            <div class="form-group">
            <label for="description">Description</label>
              <textarea class="form-control" name="description" id="description" cols="20" rows="5"></textarea>
            </div>
  
            
            <button id="btnCancel" class="btn btn-sm btn-warning my-2 my-sm-0" data-dismiss="modal">Cancel</button>
            <button type="submit"  class="btn brn-sum btn-primary my-2 my-sm-0">Create</button>
          </form>
        </div>
      </div>

    </div>
    @if(isset($item->url_finished) && ($item->url_finished!=null))
        <a href="{{$item->url_finished}}" target="_blanck">  <i class="fas fa-link"></i></a>
        @endif
  </div>