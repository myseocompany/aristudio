<div class="no_print">
  <form action="/tasks/daily" method="GET">
    <div class="row">
      <div class="col-5">
        <div class="row">
          <div class="col-6">
            <select name="filter" class="custom-select" id="filter" onchange="update()">
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
            <div class="row">
              <div class="col">    <input class="input-date" type="date" id="from_date" name="from_date" onchange="cleanFilter()" value="{{$request->from_date}}">
              </div>
              <div class="col">
                <input class="input-date" type="date" id="to_date" name="to_date" onchange="cleanFilter()" value="{{$request->to_date}}">
              </div>
            </div>  
          </div>
        </div>
      </div>
      <div class="col-3">
      <!--  
      *
      *    Combo de proyectos
      *
      -->
        <select name="project_id" class="custom-select" id="project_id" onchange="submit();">
            <option value="">select a project</option>
           @foreach($projects_options as $project)
              <option value="{{$project->id}}" @if ($request->project_id == $project->id) selected="selected" @endif>
              <?php echo substr($project->name, 0, 15); ?>
              </option>
            @endforeach
        </select>
      </div>
      <div class="col-3">

        <!--  
        *
        *    Combo de usuarios
        *
        -->
      <select multiple="multiple" name="user_id" class="custom-select" id="user_id">
        <option value="">select a user</option>
        @foreach($users_options as $user)
          <option value="{{$user->id}}" @if ($request->user_id == $user->id) selected="selected" @endif>
             <?php echo substr($user->name, 0, 15); ?>
            
          </option>
        @endforeach
      </select>
      </div>
      <div class="col-1">


        <input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filter" >
        
      </div>       
    </div>
  </form>
</div>