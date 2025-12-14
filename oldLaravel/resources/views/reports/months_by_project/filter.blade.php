
<div>
    <form action="/reports/months_project" method="GET">
    	

      <div class="row">
      	<div class="col">
      		<select name="filter" class="custom-select" id="filter" onchange="update()">
	        <option value="">select time</option>

	        <option value="currentmonth" @if ($request->filter == "currentmonth") selected="selected" @endif>this month</option>        
	        <option value="lastmonth" @if ($request->filter == "lastmonth") selected="selected" @endif>last month</option>
	        <option value="-30" @if ($request->filter == "-30") selected="selected" @endif>last 30 days</option>
        
      	</select>
      	</div>
        <div class="col">
        	<input class="input-date" type="date" id="from_date" name="from_date" onchange="cleanFilter()" value="{{$request->from_date}}">
        </div>
        <div class="col">
          
          <input class="input-date" type="date" id="to_date" name="to_date" onchange="cleanFilter()" value="{{$request->to_date}}">
       	</div>
       

        <div class="col">
			
			<select  name="project_id" class="slectpicker custom-select" id="project_id" onchange="submit();">
	        <option value="">Select project </option>


	        @foreach($projects as $item)
	        <option value="{{$item->id}}"  @if ($item->id == $request->project_id)  selected="selected" @endif >
	        	{{$item->name}}
	        </option>
	        @endforeach
	       
	      </select>
      </div>
      
      <div class="col">
			
			<select  name="status_id" class="slectpicker custom-select" id="status_id" onchange="submit();">
	        <option value="">Select status </option>


	        @foreach($statuses as $item)
	        <option value="{{$item->id}}"  @if ($item->id == $request->status_id)  selected="selected" @endif >
	        	{{$item->name}}
	        </option>
	        @endforeach
	       
	      </select>
      </div>
      <div class="col">
	     <input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filtrar" >
       </div>
      </div>
    </form>
</div>
