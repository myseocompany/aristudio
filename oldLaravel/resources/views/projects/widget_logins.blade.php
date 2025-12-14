
@if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,6,0,1,0,0,0) == 1)
<h2>Login</h2>
<a href="/projects/{{$model->id}}/login_download" class="btn btn-sm btn-primary">Download</a>
  @endif

<div id="project-login" class="table-wrapper-scroll-y my-custom-scrollbar">
  
 @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,6,0,1,0,0,0) == 1) 
<table class="table">
  <thead>
  </thead>
  </tr>
  <tbody>  


    @foreach($logins as $item)                           <!--  m_id,c,r,u,d) m=6-> logins -->
  
 
    <tr>
      <th>Url <br>Name <br> User <br> Password</th>
      

      <td>
         <a @if( substr($item->url,0,8)== 'https://') href="{{$item->url}}" @else href="https://{{$item->url}}" @endif target="_blank">{{$item->url}}<a><br>
        {{$item->name}}<br>
        {{$item->user}}<br>
     
        <div style="float: left";>
          <a>
           <img  onclick="copyPassword('password_{{$item->id}}')" style="width: 20px;"  src="/images/copy.png">
          </a>
        </div>
        <div id="password_{{$item->id}}">
        {{$item->password}}
        </div>
        </td>
  
       
      <td>
          @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,6,0,0,1,0,0) == 1)
        <a href="" class="btn btn-sm btn-primary my-2 my-sm-0"  data-toggle="modal" data-target="#editModal{{$item->id}}">Edit</a>
          @endif


            <!-- Modal -->
        <div class="modal fade" id="editModal{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Login Edit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="/project_logins/{{$item->id}}/update" method="POST">
              {{ csrf_field() }}
                <div class="modal-body">
                  
                    <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="name"><strong>Name</strong></label>
                        <input class="form-control" type="text" name="name" value="{{ $item->name}}">
                      </div>

                      <div class="form-group">
                        <label for="url"><strong>URL</strong></label>
                        <input class="form-control" type="text" name="url" value="{{ $item->url}}">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="user"><strong>User</strong></label>
                        <input class="form-control" type="text" name="user" value="{{ $item->user}}">
                      </div>

                      <div class="form-group">
                        <label for="password"><strong>Password</strong></label>
                        <input class="form-control" type="text" name="password" value="{{ $item->password}}">
                      </div>
                      <input type="hidden" name="project_id" id="project_id" value="{{$item->id}}">
                    </div>

                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-secondary my-2 my-sm-0" data-dismiss="modal">Close</button>
                  <input type="submit" name="" value="Submit" class="btn btn-sm btn-primary my-2 my-sm-0">
                </div>
              </form>
            </div>
          </div>
        </div>
      </td>
      <td>
        @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,6,0,0,0,1,0) == 1)
        <a class="btn btn-sm btn-danger my-2 my-sm-0" href="/project_logins/{{$item->id}}/delete" type="submit">Delete</a>
        @endif
    </tr>
    
    @endforeach
 
  </tbody>
</table>
</div>
@endif    