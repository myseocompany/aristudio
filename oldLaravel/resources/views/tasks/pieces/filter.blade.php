<!--myseo-->
@php
    $query_str="";
    if(isset($request->query)&& ($request->query !=""))
      $query_str = $request->query;
@endphp
<nav>
  <div>
    <form action="/pieces/" method="GET">
      <div class="row">
      <div class="col-12">
        <div class="row">
        <div class="col-4">
            <input type="text" id="query_str" val="" placeholder="... busca acá" value="@php if(isset($request->query_str)&& ($request->query_str !="")) echo $query_str; @endphp">
            </div>
            <div class="col-4">
            <select name="status_id" class="slectpicker custom-select" id="status_id" onchange="submit();">
              <option value="">select status</option>
              @foreach($task_status as $item)
                <option value="{{$item->id}}" @if($item->id == $request->status_id) selected @endif>{{$item->name}}</option>
              @endforeach
            </select>  
            
          </div>
          <div class="col-4"><input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filter" ><div>
        </div>
      </div>
    </div>
    </form>
</div>
</nav>
<script>
$(document).ready(function(){
  $("#query_str").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
