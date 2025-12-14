<!--myseo-->
  <div>
    <form action="/tasks/" method="GET">
      <div class="row">
        <div class="col-12">
          <div  class="row">
            <div class="col-6">
              <select name="filter" class="form-control" id="filter" onchange="update()">
                <option value="">select time</option>
                <option value="7" @if ($request->filter == "7") selected="selected" @endif>next 7 days</option>
                <option value="0" @if ($request->filter == "0") selected="selected" @endif>today</option>
                <option value="thisweek" @if ($request->filter == "thisweek") selected="selected" @endif>this week</option>
                <option value="currentmonth" @if ($request->filter == "currentmonth") selected="selected" @endif>this month</option>
                <option value="-1" @if ($request->filter == "-1") selected="selected" @endif>yesterday</option>
                <option value="lastweek" @if ($request->filter == "lastweek") selected="selected" @endif>last week</option>
                <option value="lastmonth" @if ($request->filter == "lastmonth") selected="selected" @endif>last month</option>
                <option value="-7" @if ($request->filter == "-7") selected="selected" @endif>last 7 days</option>
                <option value="-30" @if ($request->filter == "-30") selected="selected" @endif>last 30 days</option>
              </select>
            </div>
            <div class="col-6"> 
              <input class="input-date" type="date" id="from_date" name="from_date" onchange="cleanFilter()" value="{{$request->from_date}}">
            <input class="input-date" type="date" id="to_date" name="to_date" onchange="cleanFilter()" value="{{$request->to_date}}">
            </div>
            <div class="col-6">
              <select name="project_id" class="form-control" id="project_id" onchange="submit();">
                <option value="">select a project</option>
               @foreach($projects as $project)
                  <option value="{{$project->id}}" @if ($request->project_id == $project->id) selected="selected" @endif>
                  <?php echo substr($project->name, 0, 20); ?>
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-6">
              <select name="user_id" class="form-control" id="user_id" onchange="submit();">
                <option value="">select a user</option>
                @foreach($users as $user)
                  <option value="{{$user->id}}" @if ($request->user_id == $user->id) selected="selected" @endif>
                     <?php echo substr($user->name, 0, 10); ?>
                  </option>
                @endforeach
              </select>        
            </div> 
            <div class="col-6">
              <select multiple="" name="status_id" class="slectpicker form-control" id="status_id" style="height:100px;">
                <option value="">select status</option>
                @foreach($task_status as $item)
                  <?php
                    $selected = false;
                    for($i=0; $i<count($statuses_id); $i++){
                      if($statuses_id[$i] == $item->id){
                        ;
                        $selected = true;
                      }
                    }
                  ?>
                  <option value="{{$item->id}}"  @if ($selected)  selected="selected" @endif >{{$item->name}}</option>
                @endforeach
              </select>   
            </div>
            <!--
            <div class="col-1">
              <span class="badge" style="background: #007bff; color:white;" id="task_statuses" data-toggle="modal" data-target="#taskStatusesModal" data-backdrop="false">?</span>
            </div>
            -->
            <div class="col-6">
              <select name="type_id" class="form-control" onchange="submit()">
                <option value="">Select type</option>
                @foreach($task_types as $key=>$value)
                  <option value="{{$key}}" @if($request->type_id==$key) selected="selected" @endif>{{$value}}</option>
                @endforeach
              </select>

              <select name="priority" id="priority" class="form-control" onchange="submit();">
                  <option value="">Select a Priority</option>
                  <option value="1" @if($request->priority == "1") selected="selected" @endif>1</option>
                  <option value="2" @if($request->priority == "2") selected="selected" @endif >2</option>
                  <option value="3" @if($request->priority == "3") selected="selected" @endif >3</option>
                  <option value="4" @if($request->priority == "4") selected="selected" @endif >4</option>
                  <option value="5" @if($request->priority == "5") selected="selected" @endif >5</option>
                  <option value="6" @if($request->priority == "6") selected="selected" @endif >6</option>
                  <option value="7" @if($request->priority == "7") selected="selected" @endif >7</option>
                  <option value="8" @if($request->priority == "8") selected="selected" @endif >8</option>
                  <option value="9" @if($request->priority == "9") selected="selected" @endif >9</option>
                  <option value="10" @if($request->priority == "10") selected="selected" @endif >10</option>
                </select>

                <label for="no_billing">No billing:</label>
                <input name="noBilling" id="noBilling" type="radio" class="check" value="1" 
                @if(isset($request->noBilling)&&($request->noBilling==1)) checked @endif  onclick="submit()">
                <label for="no_billing">Billing:</label>
                <input name="noBilling" id="noBilling" type="radio" class="check" value="0" @if(isset($request->noBilling)&&($request->noBilling==0)) checked @endif  onclick="submit()">
            </div>
            {{--
            <div class="col-6">
              @if(isset($task_subtypes)&&($task_subtypes!=null))
                <select name="subtype_id" class="form-control" onchange="submit()">
                  <option value="">Select subtype</option>
                    @foreach($task_subtypes as $item)
                      <option value="{{$item->id}}" @if($request->subtype_id==$item->id) selected="selected" @endif>{{$item->name}}</option>
                    @endforeach
                </select>
              @endif
            </div>
            --}}
            <div class="col-12">
              <div class="row">
                <div class="col-6">
                  <input type="text" id="querystr" name="querystr" placeholder="Search here..." @if(isset($request->querystr))  value="{{$request->querystr}}" @endif> 
                </div>
                <div class="col-6">
                  <input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filter" >
                </div>
              </div>
            </div>
            
        </div>
      </div>
    </form>




</div>
<div class="modal fade" id="taskStatusesModal" tabindex="-1" role="dialog" aria-labelledby="taskStatusesModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="taskStatusesModalLabel">Task Statuses</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <table class="table">
            <thead class="thead-dark">
              <tr>
                <th scope="col" style="border-left: 10px solid #343a40 !important">Name</th>
                <th scope="col" style="border-left: 10px solid #343a40 !important">Description</th>
              </tr>
            </thead>
            <tbody>
              @foreach($task_status as $status_option)
              <tr>
                <th scope="row" style="border-left: 10px solid {{ $status_option->background_color }} !important;">{{ $status_option->name }}</th>
                <td scope="row">{{ $status_option->description }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
      </div>
    </div>
  </div>
</div>