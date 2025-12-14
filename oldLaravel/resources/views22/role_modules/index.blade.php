@extends('layout')
@section('content')

<h2>Role Modules</h2>

<div class="row">
	<div class="form-group col-md-5">
		<label for="role_id">Roles</label>
		<select id="role_id" name="role_id" class="custom-select" onchange="loadModules();">
			<option value="">Select a Role...</option>
			@foreach($roles as $role)
				<option value="{{$role->id}}">{{$role->name}}</option>
			@endforeach
		</select>
	</div>
	<div class="form-group col-md-5">
		<div id="modules"></div>
	</div>
	  @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,7,1,0,0,0,0) == 1)
	<div class="form-group col-md-2">
		<br>
		<a class="btn btn-primary btn-sm" onclick="saveRoleModule();" style="color: white;">Create</a>
	</div>
	@endif
</div>
<div class="table-responsive-sm">
	<table class="table" id="table">
		<thead>
			<tr>
				<th>Role</th>
				<th>Module</th>
				<th>Create</th>
				<th>Read</th>
				<th>Update</th>
				<th>Delete</th>
				<th>List</th>
			</tr>
		</thead>
		<tbody>
			@foreach($role_modules as $item)
			<tr>
				<td>{{$item->role->name}}</td>
				<td>{{$item->module->name}}</td>

				<td>
					@if($item->created == 1)
						<div>
							<input checked="" class="switch" type="checkbox" id="isCheckedClass_created_{{$item->role_id}}_{{$item->module_id}}" name="isCheckedClass_created_{{$item->role_id}}_{{$item->module_id}}" onchange="changePermission({{$item->role_id}},{{$item->module_id}}, 'created');" value="1">
						</div>
					@else
						<div>
							<input class="switch" type="checkbox" id="isCheckedClass_created_{{$item->role_id}}_{{$item->module_id}}" name="isCheckedClass_created_{{$item->role_id}}_{{$item->module_id}}" onchange="changePermission({{$item->role_id}},{{$item->module_id}}, 'created');" value="">
						</div>	
					@endif
				</td>
				<td>
					@if($item->readed == 1)
						<div>
							<input checked="" class="switch" type="checkbox" id="isCheckedClass_readed_{{$item->role_id}}_{{$item->module_id}}" name="isCheckedClass_readed_{{$item->role_id}}_{{$item->module_id}}" onchange="changePermission({{$item->role_id}},{{$item->module_id}}, 'readed');" value="1">
						</div>
					@else
						<div>
							<input class="switch" type="checkbox" id="isCheckedClass_readed_{{$item->role_id}}_{{$item->module_id}}" name="isCheckedClass_readed_{{$item->role_id}}_{{$item->module_id}}" onchange="changePermission({{$item->role_id}},{{$item->module_id}}, 'readed');" value="">
						</div>	
					@endif
				</td>
				<td>
					@if($item->updated == 1)
						<div>
							<input checked="" class="switch" type="checkbox" id="isCheckedClass_updated_{{$item->role_id}}_{{$item->module_id}}" name="isCheckedClass_updated_{{$item->role_id}}_{{$item->module_id}}" onchange="changePermission({{$item->role_id}},{{$item->module_id}}, 'updated');" value="1">
						</div>
					@else
						<div>
							<input class="switch" type="checkbox" id="isCheckedClass_updated_{{$item->role_id}}_{{$item->module_id}}" name="isCheckedClass_updated_{{$item->role_id}}_{{$item->module_id}}" onchange="changePermission({{$item->role_id}},{{$item->module_id}}, 'updated');" value="">
						</div>	
					@endif
				</td>
				<td>
					@if($item->deleted == 1)
						<div>
							<input checked="" class="switch" type="checkbox" id="isCheckedClass_deleted_{{$item->role_id}}_{{$item->module_id}}" name="isCheckedClass_deleted_{{$item->role_id}}_{{$item->module_id}}" onchange="changePermission({{$item->role_id}},{{$item->module_id}}, 'deleted');" value="1">
						</div>
					@else
						<div>
							<input class="switch" type="checkbox" id="isCheckedClass_deleted_{{$item->role_id}}_{{$item->module_id}}" name="isCheckedClass_deleted_{{$item->role_id}}_{{$item->module_id}}" onchange="changePermission({{$item->role_id}},{{$item->module_id}}, 'deleted');" value="">
						</div>	
					@endif
				</td>
				<td>
					@if($item->list == 1)
						<div>
							<input checked="" class="switch" type="checkbox" id="isCheckedClass_list_{{$item->role_id}}_{{$item->module_id}}" name="isCheckedClass_list_{{$item->role_id}}_{{$item->module_id}}" onchange="changePermission({{$item->role_id}},{{$item->module_id}}, 'list');" value="1">
						</div>
					@else
						<div>
							<input class="switch" type="checkbox" id="isCheckedClass_list_{{$item->role_id}}_{{$item->module_id}}" name="isCheckedClass_list_{{$item->role_id}}_{{$item->module_id}}" onchange="changePermission({{$item->role_id}},{{$item->module_id}}, 'list');" value="">
						</div>	
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
		
	</table>
</div>

<script>


function saveRoleModule(){
	var role_id = $("#role_id").val();
	var module_id = $("#module_id").val();
	

	if(role_id != "" && module_id != ""){
		endpoint = '/save_role_module/'+role_id+'/'+module_id;
		$.ajax({
			type: 'GET', //THIS NEEDS TO BE GET
			url: endpoint,
			dataType: 'json',
			success: function (data) {
				$("#modules").empty();
				$("#table>tbody").append("<tr><td>"+data.rol_name+"</td><td>"+data.module_name+"</td><td><div class='form-check form-check-inline'><input class='switch' type='checkbox' id='isCheckedClass_created_"+role_id+"_"+module_id+"' name='isCheckedClass_created_"+role_id+"_"+module_id+"' onchange='changePermission("+role_id+","+module_id+", "+'"created"'+");' value=''></div></td><td><div class='form-check form-check-inline'><input class='switch' type='checkbox' id='isCheckedClass_readed_"+role_id+"_"+module_id+"' name='isCheckedClass_readed_"+role_id+"_"+module_id+"' onchange='changePermission("+role_id+","+module_id+", "+'"readed"'+");' value=''></div></td><td><div class='form-check form-check-inline'><input class='switch' type='checkbox' id='isCheckedClass_updated_"+role_id+"_"+module_id+"' name='isCheckedClass_updated_"+role_id+"_"+module_id+"' onchange='changePermission("+role_id+","+module_id+", "+'"updated"'+");' value=''></div></td><td><div class='form-check form-check-inline'><input class='switch' type='checkbox' id='isCheckedClass_deleted_"+role_id+"_"+module_id+"' name='isCheckedClass_deleted_"+role_id+"_"+module_id+"' onchange='changePermission("+role_id+","+module_id+", "+'"deleted"'+");' value=''></div></td><td><div class='form-check form-check-inline'><input class='switch' type='checkbox' id='isCheckedClass_list_"+role_id+"_"+module_id+"' name='isCheckedClass_list_"+role_id+"_"+module_id+"' onchange='changePermission("+role_id+","+module_id+", "+'"list"'+");' value=''></div></td></tr>");
				console.log("yes");
			},
			error: function(data) { 
				console.log("fail");
			}
		});
	}
}

function loadModules(){
	var role_id = $("#role_id").val();
	console.log(role_id);
	$("#modules").empty();

	if (!isNaN(parseInt(role_id)))
		role_id = role_id;
		endpoint = '/get_modules/'+role_id;
		$.ajax({
			type: 'GET', //THIS NEEDS TO BE GET
			url: endpoint,
			dataType: 'json',
			success: function (data) {
				printModules(data);
				console.log("yes");
			},
			error: function(data) { 
				console.log("fail");
			}
		});
}

function printModules(data){
    str = '<label for="module_id">Modules</label><select name="module_id" id="module_id" class="custom-select">;';
    str += '<option value="">Select a Module...</option>';
    $.each(data, function(i, obj) {
      str += '<option value="'+obj.id+'" >'+obj.name+'</option>';
    });
    str += '</select>';

    $("#modules").html(str);
  }

function changePermission(role_id, module_id, input){
	console.log("siiiiiiiii");
	var check_name ="isCheckedClass_"+input+"_"+role_id+"_"+module_id;
	console.log(check_name);

	var checked = $("#"+check_name).is(":checked");
	if (checked) {
		console.log("checked");
		$.ajax({
          type : "GET",
          url : "{{url('/change_permission')}}",
          data: {
            role_id : role_id,
            module_id : module_id,
            value : "1",
            input : input
          },
          success:function(msg){
            console.log("exit");
          },
          error:function(){
            console.log("Error");
          }
        });
    }else{
    	console.log("not checked");
    	$.ajax({
          type : "GET",
          url : "{{url('/change_permission')}}",
          data: {
            role_id : role_id,
            module_id : module_id,
            value : "",
            input : input
          },
          success:function(msg){
            console.log("exit");
          },
          error:function(){
            console.log("Error");
          }
        });

    }
}

</script>
<style type="text/css">
	input[type="checkbox"].switch{
 
  -webkit-appearance: none;
     -moz-appearance: none;
          appearance: none;
  width: 40px;
  height: 1.5em;
  background: #21252970;
  border-radius: 3em;
  position: relative;
  cursor: pointer;
  outline: none;
  -webkit-transition: all .2s ease-in-out;
  transition: all .2s ease-in-out;
  }

  input[type="checkbox"].switch:checked{
  background: #007bff;
  }
  
  input[type="checkbox"].switch:after{
  position: absolute;
  content: "";
  width: 1.5em;
  height: 1.5em;
  border-radius: 50%;
  background: #fff;
  -webkit-box-shadow: 0 0 .25em rgba(0,0,0,.3);
          box-shadow: 0 0 .25em rgba(0,0,0,.3);
  -webkit-transform: scale(.7);
          transform: scale(.7);
  left: 0;
  -webkit-transition: all .2s ease-in-out;
  transition: all .2s ease-in-out;
  }
  
  input[type="checkbox"].switch:checked:after{
  left: calc(100% - 1.5em);
  }
</style>
@endsection
