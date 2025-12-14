
<form action="/customers/" method="GET" id="filter_form" class="row">
  <div class="col-md-3">
    <div>
      <select name="filter" class="custom-select" id="filter" onchange="update()">
        <option value="">Seleccione tiempo</option>
        <option value="0" @if ($request->filter == "0") selected="selected" @endif>Hoy</option>
        <option value="-1" @if ($request->filter == "-1") selected="selected" @endif>Ayer</option>
        <option value="lastweek" @if ($request->filter == "lastweek") selected="selected" @endif>semana pasada</option>
        <option value="lastmonth" @if ($request->filter == "lastmonth") selected="selected" @endif>mes pasado</option>
        <option value="-7" @if ($request->filter == "-7") selected="selected" @endif>últimos 7 dias</option>
        <option value="-30" @if ($request->filter == "-30") selected="selected" @endif>últimos 30 dias</option>
        
        <option value="thisweek" @if ($request->filter == "thisweek") selected="selected" @endif>esta semana</option>
        <option value="currentmonth" @if ($request->filter == "currentmonth") selected="selected" @endif>este mes</option>
        <option value="nextweek" @if ($request->filter == "nextweek") selected="selected" @endif>proxima semana</option>
        <option value="nextmonth" @if ($request->filter == "nextmonth") selected="selected" @endif>próximo mes</option>
        <option value="+7" @if ($request->filter == "+7") selected="selected" @endif>próximos 7 dias</option>
        <option value="+30" @if ($request->filter == "+30") selected="selected" @endif>próximos 30 dias</option>
        
      </select>
    </div>
    <div>
      <input class="input-date" type="date" id="from_date" name="from_date" onchange="cleanFilter()" value="{{$request->from_date}}">
      <input class="input-date" type="date" id="to_date" name="to_date" onchange="cleanFilter()" value="{{$request->to_date}}">
    </div>
  </div>
  <div class="col-md-2">
    <select name="status_id" class="slectpicker custom-select" id="status_id" onchange="submit();">
      <option value="">Estado...</option>
      @foreach($customer_options as $item)
        <option value="{{$item->id}}" @if ($request->status_id == $item->id) selected="selected" @endif>
           {{ $item->name }}
          
        </option>
      @endforeach
    </select>


    <!--  
*
*    Combo de usuarios
*
-->
      <select name="user_id" class="custom-select" id="user_id" onchange="submit();">
        <option value="">Usuario...</option>
        <option value="null">Sin asignar</option>
        @foreach($users as $user)
          <option value="{{$user->id}}" @if ($request->user_id == $user->id) selected="selected" @endif>
             <?php echo substr($user->name, 0, 10); ?>
            
          </option>
        @endforeach
      </select>
  </div>
  <div class="col-md-2">
      <!--  
*
*    Combo de fuentes
*
-->
      <select name="source_id" class="custom-select" id="source_id" onchange="submit();">
        <option value="">Fuente...</option>
        @foreach($sources as $item)
          <option value="{{$item->id}}" @if ($request->source_id == $item->id) selected="selected" @endif>
             <?php echo substr($item->name, 0, 15); ?>
            
          </option>
        @endforeach
      </select>


      @if(isset($projects))
      <select name="project_id" class="custom-select" id="project_id" onchange="submit();">
        <option value="">Proyecto...</option>
        @foreach($projects as $item)
          <option value="{{$item->id}}" @if ($request->project_id == $item->id) selected="selected" @endif>
             <?php echo substr($item->name, 0, 15); ?>
            
          </option>
        @endforeach
      </select>
      @endif
  </div> 

       
      
     {{-- Combo de estados --}}
     <div class="col-md-4">
      @php
          $search = str_replace("&#x202C;", "", $request->search);
      @endphp
      <input type="text" name="search" id="search" @if(isset($search) && $search != "") value="{{$search}}" @endif placeholder="Busca o escribe">

      <div>
        <?php $cu = $request->created_updated; ?>
        <label class="radio"> Fecha de creación 
          <input type="radio" name="created_updated" value="created" 
          @if((isset($cu)&& ($cu == 'created'))||(!isset($cu)))  checked @endif onchange="submit();"> </label> 
        <label class="radio" > o actualizacion 
          <input type="radio" name="created_updated" value="updated" @if(isset($request->created_updated)&& ($request->created_updated == "updated")) checked @endif onchange="submit();"> </label>
    </div>  
     </div>
      <div class="col-md-1">
        <input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filtrar" > 
      </div>
  
     
      
    </form>
  