<div id="accordion" role="tablist" class="no_print">


  <div id="collapseOne" class="collapse">
    <div class="card card-body">

      <h2>Create Task</h2>
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
                <option value="{{$project->id}}" @if ($project->id == $request->project_id) selected="selected" @endif>{{$project->name}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="name">Commitment Date</label>
              <input type="date" class="form-control" id="delivery_date" name="delivery_date" placeholder="YYYY/MMM/DD"
                valuea="@if(!isset($request->delivery_date)){{ date('Y-m-d')}}@else{{$request->delivery_date}}@endif"
                value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
              <label for="name">Due Date</label>
              <input type="date" class="form-control" id="due_date" name="due_date" placeholder="YYYY/MMM/DD" required="required"
                valuea="@if(!isset($request->from_date)){{ date('Y-m-d')}}@else{{$request->from_date}}@endif"
                value="<?php echo date('Y-m-d'); ?>">
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
              <div class="form-group">
                <label for="type_id">Type</label>
                <select name="type_id" id="type_id_{{$item->id}}" class="custom-select" onchange="getSubTypes(this.value );">
                  <option value="">Select a type...</option>
                  @foreach($task_types_component as $option)
                  <option value="{{$option->id}}" @if($item->type_id==$option->id) selected="selected" @endif >{{$option->name}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-6">
                <span id="after_type">
                </span>
              </div>
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
              <select name="status_id" id="status_id" class="form-control">
                @foreach($task_status as $item)
                <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
                ?>
              </select>
            </div>

            <div class="form-group">
              <label for="estimated_points">Estimated Time</label>
              <input type="text" class="form-control" id="estimated_points" name="estimated_points" placeholder="Estimated Points">
            </div>

            <label for="points">Points</label>
              <div class="row">
                <input type="hidden" class="form-control col-md-5" id="hours" name="hours" min="0" required value="0">

                <input type="hidden" class="form-control col-md-5" id="minutes" name="minutes" min="0" max="59" required value="0">

              </div>
              <input type="input" class="form-control form-group" id="points" name="points" placeholder="Points" pattern="^(\d(\.\d{1,2})?|0?\.\d{1,2}|2(\.0{1,2})?)$" oninput="validateRange(this)">
            
            <div class="form-group">
              <label for="name">Archivo</label>
              <input type="file" class="form-control" id="file" name="file" placeholder="Name">
            </div>
            
            <div class="form-group">
              <div class="form-group">
                <label for="points">Url Finished Task </label>
                <input class="form-control" name="url_finished" id="url_finished" placeholder="Url" value=""></input>
              </div>
            </div>

          </div>
          <input type="hidden" name="from" id="from" class="form-control" value="project">
        </div>

        <div class="form-group">
          <label for="value_generated">Value generated:</label>
          <input name="no_value_generated" id="no_value_generated_yes" type="radio" class="check" value="1" 
            @if(isset($request->no_value_generated) && $request->no_value_generated == 1) checked @endif>
          <label for="no_value_generated_yes">Yes</label>

          <input name="no_value_generated" id="no_value_generated_no" type="radio" class="check" value="0" 
            @if(isset($request->no_value_generated) && $request->no_value_generated == 0) checked @endif>
          <label for="no_value_generated_no">No</label>
        </div>


        <div class="form-group">
          <label for="description">Description</label>
          <textarea class="form-control" name="description" id="description" cols="30" rows="10"></textarea>
        </div>

        <div class="form-group row">
          <div class="form-group col-md-6">
            <label for="copy">Copy in</label>
            <textarea class="form-control" name="copy" id="copy" cols="30" rows="7"></textarea>
          </div>
          <div class="form-group col-md-6">
            <label for="caption">Copy out</label>
            <textarea class="form-control" name="caption" id="caption" cols="30" rows="7"></textarea>
          </div>

        </div>






        <button type="submit" class="btn brn-sum btn-primary my-2 my-sm-0">Submit</button>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
  function getSubTypes(tyid) {
    $("#after_type").empty();
    console.log("get");
    type_id = "0";
    if (!isNaN(parseInt(tyid)))
      type_id = tyid;

    endpoint = '/tasks/setType/' + tyid;

    console.log(endpoint);
    $.ajax({
      type: 'GET', //THIS NEEDS TO BE GET
      url: endpoint,
      dataType: 'json',
      success: function(data) {
        console.log(data);
        loadSubTypes(data);

      },
      error: function(data) {
        console.log(data);
      }
    });

  }

  function loadSubTypes(data) {
    str = '<label for="sub_type_id">Sub Type</label><select name="sub_type_id" id="sub_type_id" class="custom-select">;';
    str += '<option value="">Select a sub  type...</option>';
    $.each(data, function(i, obj) {
      str += '<option value="' + obj.id + '">' + obj.name + '</option>';
    });
    str += '</select>';

    $("#after_type").html(str);
  }
</script>