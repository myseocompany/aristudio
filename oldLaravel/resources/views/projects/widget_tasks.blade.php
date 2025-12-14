<div id="task-table" class="table-wrapper-scroll-y my-custom-scrollbar">
  
    <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Points</th>
                    <th>Due Date</th>
                    <th>User</th>
                   {{--  <th>Status</th> --}}
                    <th>Edit</th>
                  </tr>
                </thead>
                <tbody>
  
                  @foreach($model->tasks as $item)
                  <tr>
                    <td>{{ $item->id }}</td>
                    <td><a href="/tasks/{{ $item->id }}">{{ $item->name }}</a></td>
                    <td>@if(isset($item->status_id)){{ $item->status->name }}@endif</td>
                    <td>{{ $item->points }}</td>
                    <td>{{$item->due_date }}</td>
                    <td>@if(isset($item->user_id)) 
                    {{$item->user->name}} @endif</td>
                    {{-- <td>@if(isset($item->status_id)) 
                    {{$item->users->name}} @endif</td> --}}
                    <td>
                     <a class="btn btn-sm btn-primary my-2 my-sm-0" href="/tasks/{{$item->id }}/edit">Edit</a>
                    </td>
                  </tr>
          @endforeach
                </tbody>
              </table>
            </div>
              
              
  
                <!-- diffForHumans() -->
    <div>
      
      
    </div>
  </div>