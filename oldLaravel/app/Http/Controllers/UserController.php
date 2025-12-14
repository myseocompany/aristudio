<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\User;
use App\UserStatus;
use App\Role;
use App\UserWorkshops;
use App\WorkshopUser;
use App\Venue;
use App\UserLocation;
use App\UserRole;
use App\Project;
use App\ProjectUser;

class UserController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users =    User::orderBy('status_id', 'DESC')
            ->orderBy('name', 'ASC')->get();
        $role = Role::all();
        return view('users.index', compact('users','role'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = Role::all();
        $status = UserStatus::all();
        return view('users.create', compact('role','status'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $model = new User;

        $model->id = $request->id;
        $model->name = $request->name;
        $model->email = $request->email;
        $model->phone = $request->phone;
        $model->position = $request->position;
        $model->address = $request->address;
        
        $model->status_id = $request->status_id;
        $model->role_id = $request->role_id;
        $model->document = $request->document;
        $model->password = bcrypt($request->password);

        if($request->hasFile('image_url')){
            $file = $request->file('image_url');
            $name = time().$file->getClientOriginalName();
            $file-> storeAs('/files/users', $name, 'public');
            $model->image_url = $name;
        }
        
        
        $model->save();

        return redirect('/users');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);


        /*
        $projects = ProjectUser::

                            join('projects', 'projects.id','project_users.project_id')
                            ->where('project_users.user_id', '<>', $id)
                            ->where('projects.status_id', 3)
                            ->select(DB::raw('distinct(project_users.project_id), projects.id, projects.name'))
                            ->get();

        dd($projects);
*/






        $users_project = ProjectUser::
            where('user_id','=', $id)->get();

        $projects_id = Array();
        foreach ($users_project as $item) { 
            $projects_id[] = $item->project_id;
        }

        $projects = Project::
            where('status_id', 3)
            ->whereNotIn('id', $projects_id)
            ->get();

        $project_users = ProjectUser::where('user_id',$id)
                            ->join('projects', 'projects.id','project_users.project_id')
                            ->get();
        //dd($project_users);
        return view('users.show', compact('user','projects','project_users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $model = User::find($id);
        $role = Role::all();
        $user_statuses = UserStatus::all();

        return view('users.edit', compact('model', 'user_statuses','role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $model = User::find($id);

        $model->name = $request->name;
        $model->document = $request->document;
        $model->email = $request->email;
        $model->phone = $request->phone;
        $model->address = $request->address;
        $model->position = $request->position;
        $model->status_id = $request->status_id;
        $model->role_id = $request->role_id;
        $model->color = $request->color;

        if($request->hasFile('image_url')){
            $file = $request->file('image_url');
            $image_url = time() . $file->getClientOriginalName();
            $file->storeAs('/files/users', $image_url, 'public');
            $model->image_url = $image_url;
        }

        $model->save();

        return redirect('/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function createProximity(){

        $venues = Venue::all();

        return view('proximities.index' , compact('venues'));

    }

    public function storeProximity(Request $request){
      
        $model = new UserLocation;
        $model->user_id = Auth::id();
        $model->latitude = $request->currentLat;
        $model->longitude = $request->currentLon;
        $model->distance = $request->distance;
        $model->save();

        return view('proximities.show' , compact('model'));

    }

    public function viewProximity(){

        
        $model = UserLocation::
       
       orderBy('distance_m','asc')
        ->orderBy('created_at','desc')
        ->get();

        return view('proximities.showProximity', compact('model'));

    }

    public function viewMapProximity(){
        $venues = Venue::all();
        
        $model = UserLocation::orderBy('distance_m','asc')
        ->orderBy('created_at','desc')
        ->get();

        return view('proximities.showMapProximity', compact('model','venues'));

    }

    public function editPassword($id)
    {
        $user = User::findOrFail($id); // Buscar el usuario por ID
        return view('users.editPassword', compact('user'));
    }

    public function updatePassword(Request $request, $id)
    {


        $model = User::find($id);
       

        if(isset($request->new_password) && ($request->new_password !="") ){
            $model->password = Hash::make($request->new_password);
        }

        $model->save();

        return redirect('/users');
    }
}


