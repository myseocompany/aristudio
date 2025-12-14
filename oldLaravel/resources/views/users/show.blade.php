  @extends('layout')

  @section('content')

  <div class="title">
      <label for="Title">
        <h1>Users</h1>
      </label>    
  </div>

  <div class="card main-card">
    <div class="card-body main-body-presentation">
      <div class="vertical-nav-presentation">
        <div class="row">
          <div class="col-4 nav-panel">
            <div class="nav flex-column nav-pills navs-content-presentations" id="v-pills-tab" role="tablist" aria-orientation="vertical">
              <button class="nav-link" id="v-pills-profile-tab" data-toggle="pill" data-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false">Profile</button>
                <a href="/password/edit/{{ $user->id }}">Change Password</a>
                
              <button class="nav-link" id="v-pills-settings-tab" data-toggle="pill" data-target="#v-pills-settings" type="button" role="tab" aria-controls="v-pills-settings" aria-selected="false">Projects</button>
            </div>
          </div>
        </div>
      </div>

      <div class="information-block">
        <h5 class="profile">My Profile</h5>
        <form method="POST" action="/users/{{ $user->id }}/edit">
          {{ csrf_field() }}
        <div class="card card-presentation-user">
          <div class="card-body content-presentation-user">
            @if(isset($user->image_url))<img src="/laravel/storage/app/public/files/users/{{ $user->image_url }}" height="150px" width="150px" style="
            border-radius: 50%;
            "> @endif
            <div class="presentation-block">
              <h4>{{ $user->name }}</h4>
              <div class="help-block">{{ $user->position }}</div>
              <div class="help-block">{{ $user->role->name }}</div>
              <div class="form-group col-md-4 submit">
                <button type="submit" class="btn btn-outline-success my-2 my-sm-0 btn-edit-user">Edit</button>
             </div>
            </div>
          </div>
        </div>
        <div class="card card-presentation-user">
          <div class="card-body">
            <label class="title-card mb-3" for="budget"><strong>Personal information</strong></label>
            <div class="row g-3">
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Name</strong></label>
                <div class="help-block">{{ $user->name }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Document</strong></label>
                <div class="help-block">{{ $user->document }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Phone</strong></label>
                <div class="help-block">{{ $user->phone }} </div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Email</strong></label>
                <div class="help-block">{{ $user->email }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Birth date</strong></label>
                <div class="help-block">{{ $user->birth_date }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Blood type</strong></label>
                <div class="help-block">{{ $user->blood_type }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="description"><strong>Address</strong></label>
                <div class="help-block">{{ $user->address }}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="card card-presentation-user">
          <div class="card-body">
            <label class="title-card mb-3" for="budget"><strong>Employment details</strong></label>
            <div class="row g3">
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Position</strong></label>
                <div class="help-block">{{ $user->position }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Daily goal</strong></label>
                <div class="help-block">{{ $user->daily_goal }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Hourly rate</strong></label>
                <div class="help-block">{{ $user->hourly_rate }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Contract type</strong></label>
                <div class="help-block">{{ $user->contract_type }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Entry date</strong></label>
                <div class="help-block">{{ $user->entry_date }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Termination date</strong></label>
                <div class="help-block">{{ $user->termination_date }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="description"><strong>Last login</strong></label>
                <div class="help-block">{{ $user->last_login }}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="card card-presentation-user">
          <div class="card-body">
            <label class="title-card mb-3" for="budget"><strong>Additional information</strong></label>
            <div class="row g-3">
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>E.P.S</strong></label>
                <div class="help-block">{{ $user->eps }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>A.R.L</strong></label>
                <div class="help-block">{{ $user->arl }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Role</strong></label>
                <div class="help-block">{{ $user->role->name }}</div>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="budget"><strong>Status</strong></label>
                <div class="help-block">{{ $user->status->name }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </form>

  <form action="/projects/{{$user->id}}/addProject" method="POST">
    {{ csrf_field() }}
    <h2>Projects</h2>
    
    <div class="row">
        <div class="form-group col-md-6">
          <select name="project_id" id="project_id" class="form-control">
            <option value="">Select project...</option>
              @foreach ($projects as $item)
                {{-- expr --}}
              <option value="{{$item->id}}">{{$item->name}}</option>
              @endforeach
          </select>
        </div>
        <div class="form-group col-md-6">
          <button type="submit" class="btn btn-sm btn-primary my-2 my-sm-0">Submit</button>
        </div>
    </div>
  </form>


    <div id="user-table" class="table-wrapper-scroll-y my-custom-scrollbar">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead class="">
          <tr>
            <th>Name</th>
            <th></th>
          </tr>
          </thead>
          @foreach ($project_users as $item)
          <tr>
            <td>{{$item->name}}</td>
            <td><a href="/projects/{{$item->id}}/deleteProject/{{$user->id}}">Delete</a></td>
          </tr>
          @endforeach
        </table>
    </div>
  </div>
  @endsection