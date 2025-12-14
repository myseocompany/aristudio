<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Metadata;
use App\User;
use App\Task;
use App\TaskStatus;
use App\ProjectStatus;
use App\ProjectType;
use App\Role;
use App\ProjectUser;
use App\ProjectLogin;

// php artisan make:controller ProjectController -r

class ProjectLoginController extends Controller
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
    public function index(Request $request)
    {
        $model = Project::orderBy('status_id','ASC')->get();



        $task_status = TaskStatus::all();

        $users = User::orderBy('name')->get();


        return view('projects.index', compact('model','users', 'task_status','request'));
     
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $project_type = ProjectType::all();
        $project_status = ProjectStatus::all();
        return view('projects.create', compact('project_type','project_status'));
    }

    /**
     * Store a newly created resource in storage.
     *i
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $model = new ProjectLogin;
        $model->project_id = $request->project_id;
        $model->name = $request->name;
        $model->user = $request->user;
        $model->password = $request->password;
        $model->url = $request->url;
        
        if ($model->save()) {
            return redirect('/projects/'.$request->project_id);
        }
    }

    public function updateLogin(Request $request, $id){
        $model = ProjectLogin::find($id);
        $model->name = $request->name;
        $model->user = $request->user;
        $model->password = $request->password;
        $model->url = $request->url;
        $model->save();
        return redirect('/projects/'.$model->project_id);
    }

    public function deleteLogin(Request $request, $id){
        $model = ProjectLogin::find($id);
        $projectId = $model->project_id;
        $model->delete();
        return redirect('/projects/'.$projectId);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        //
        $model = Project::find($id);
        $projects = Project::where('id',$id)->get();
        $logins = ProjectLogin::where('project_id',$id)->get();
        //$task_status_options_JSON = Metadata::getTaskStatusOptions();
        //$task_status_options = json_decode($task_status_options_JSON["value"], true);
        $task_status = TaskStatus::all();
        // selecciono todos los usuarios activos
        $users_project = ProjectUser::
            where('project_id','=', $id)->get();
        $array = Array();
        foreach ($users_project as $item) {
            $array[] = $item->user_id;
        }

        
        $users = User::
            where('users.status_id', 1)
            ->where(function($query){
                $query = $query->where('users.role_id', 1);
                $query = $query->orwhere('users.role_id', 2);
            
            })
            ->whereNotIn('id', $array)
            ->orderBy("id", "desc")
            ->get();
        $tasks = Task::all();
        // dd($project->users);
        
       // $project->tasks->sortByDesc("due_date");

        return view('projects.show', compact('model','task_status','users', 'tasks','projects','request','logins'));
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
        $model = Project::find($id);
        $project_status = ProjectStatus::all();
        $project_type = ProjectType::all();


        
        return view('logins.edit', compact('model', 'project_status', 'project_type'));
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
        //
        $model = Project::find($id);

        
        $model->name = $request->name;
        $model->type_id = $request->type_id;
        $model->description = $request->description;
        $model->budget = $request->budget;
        $model->start_date = $request->start_date;
        $model->finish_date = $request->finish_date;
        $model->weekly_pieces = $request->weekly_pieces;
        $model->ads_budget = $request->ads_budget;
        $model->status_id = $request->status_id;
        $model->color = $request->color;

        
        $model->save();



        return redirect('/projects/'.$id);
    }
    // adduser
     // public function addUser(Request $request) {
     //    $model->users = $request->users;
     //    $model->save();

     //    return redirect('/projects')       
     // }

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

    public function addUser(Request $request, $id){
        $model = new ProjectUser;

        $model->project_id = $id;
        $model->user_id = $request->user_id;
        

        $model->save();

        return redirect('/projects/'.$id);


    }

    public function deleteUser(Request $request, $pid, $uid){

        $model = ProjectUser::where("project_id", $pid)->where("user_id",$uid);
        

       
        

        $model->delete();

        

        return redirect('/projects/'.$pid);


    }

}
