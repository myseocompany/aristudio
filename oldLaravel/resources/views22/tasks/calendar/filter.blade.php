<nav class="no_print navbar navbar-expand-lg navbar-light bg-light">
  <div class="collapse navbar-collapse">
    <form action="/tasks/schedule/" method="GET">
      <div class="row">
        <div class="col-5">
          <div  class="row">
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
              <input class="input-date" type="date" id="from_date" name="from_date" onchange="cleanFilter()" value="{{$request->from_date}}">
            <input class="input-date" type="date" id="to_date" name="to_date" onchange="cleanFilter()" value="{{$request->to_date}}">
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
         @foreach($projects as $project)
            <option value="{{$project->id}}" @if ($request->project_id == $project->id) selected="selected" @endif>
            <?php echo substr($project->name, 0, 20); ?>
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
        <select name="user_id" class="custom-select" id="user_id" onchange="submit();">
          <option value="">select a user</option>
          @foreach($users as $user)
            <option value="{{$user->id}}" @if ($request->user_id == $user->id) selected="selected" @endif>
               <?php echo substr($user->name, 0, 10); ?>
              
            </option>
          @endforeach
        </select>        
      </div> 
      <div class="col-5">
        <div class="row">
          <div class="col-6">
            <select multiple="" name="status_id" class="slectpicker custom-select" id="status_id"">
              <option value="">select a status</option>


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
          <div class="col-6">
            <div class="row">
              <div class="col-12">
                
                <select name="type_id" class="custom-select" onchange="submit()">
                  <option value="">Select type</option>
                  @foreach($task_types as $item=>$value)
                  
                 
                    <option value="{{$item}}" 
                      @if($request->type_id==$item) selected="selected" 
                      @endif>
                      {{$value}}
                    </option>
                  
                 
                  @endforeach
                </select>
              </div>

              <div class="col-12">
              @if(isset($task_subtypes)&&($task_subtypes!=null))
                <select name="subtype_id" class="custom-select" onchange="submit()">
                  <option value="">Select subtype</option>
                  
                    @foreach($task_subtypes as $item)
                      <option value="{{$item->id}}" @if($request->subtype_id==$item->id) selected="selected" @endif>{{$item->name}}</option>

                   
                    @endforeach
                  
                </select>
              @endif
                <!--
            <select name="order_by" id="order_by"  class="custom-select" >
              <option value="">select order</option>
              
              <option value="updated_at" @if($request->order_by=="updated_at") selected="selected" @endif>Updated at</option>
              <option value="due_date" @if($request->order_by=="due_date") selected="selected" @endif>Due date</option>
              <option value="priority" @if($request->order_by=="priority") selected="selected" @endif>Priority</option>
              <option value="lead_time" @if($request->order_by=="lead_time") selected="selected" @endif>Lead time</option>

            </select>
          -->
            <!-- <label for="prioridad">
              Ordenar por prioridad:
            </label>
            <input name="priority" id="priority" type="checkbox" class="check" @if(isset($request->priority)&&($request->priority="on")) checked @endif  onclick="submit()"> -->   
                
             <!-- <label for="observados">
                Whatched by me:
              </label>
              <input name="observer" id="observer" type="checkbox" class="check" @if(isset($request->observer)&&($request->observer="on")) checked @endif  onclick="submit()">
              -->
              </div>
              <div class="col-12">
                <label for="no_billing">
                No billing:
                </label>
                <input name="noBilling" id="noBilling" type="radio" class="check" value="1" @if(isset($request->noBilling)&&($request->noBilling==1)) checked @endif  onclick="submit()">

                <label for="no_billing">
                  Billing:
                </label>
                <input name="noBilling" id="noBilling" type="radio" class="check" value="0" @if(isset($request->noBilling)&&($request->noBilling==0)) checked @endif  onclick="submit()">
              </div>
              
            </div> 
          </div>
          
        </div>
        <!--  
*
*    Combo de estatus
*
-->
             
      </div>
      <div class="col-7">
        <div class="row">
          
          <div class="col-8">
            <input type="text" id="querystr" name="querystr" @if(isset($request->querystr))  value="{{$request->querystr}}" @endif>

          </div>
          <div class="col-4">
             <input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filter" >
            
          </div>
        </div>
        
      </div>
        
    </div>







      
      
      



     
      

    </form>
</div>
</nav>