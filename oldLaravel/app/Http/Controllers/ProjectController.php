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
use App\Account;
use App\DocumentType;
use App\ProjectDocument;
use App\TaskType;
use Auth;


// php artisan make:controller ProjectController -r

class ProjectController extends Controller
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
        if(Auth::user()->getRoleModule(Auth::user()->role_id,3) == 3){
            $model = Project::orderBy('status_id','ASC')->get();
            $task_status = TaskStatus::all();
            $users = User::orderBy('name')->get();
            $task_types_component = TaskType::whereNull('parent_id')->get();
            return view('projects.index', compact('model','users', 'task_status','request', 'task_types_component'));
        }else{
            return redirect("http://myseo.com.co/");
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(  (Auth::user()->role_id == 1)){
            $project_type = ProjectType::all();
            $project_status = ProjectStatus::all();
            $task_types_component = TaskType::whereNull('parent_id')->get();
            return view('projects.create', compact('project_type','project_status', 'task_types_component'));
        }else{
            return redirect("http://myseo.com.co/");
        }
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
        $model = new Project;

        $model->id = $request->id;
        $model->type_id = $request->type_id;
        $model->name = $request->name;
        $model->description = $request->description;
        $model->weight = $request->weight;
        $model->budget = $request->budget;
        $model->start_date = $request->start_date;
        $model->finish_date = $request->finish_date;
        $model->weekly_pieces = $request->weekly_pieces;
        $model->ads_budget = $request->ads_budget;
        $model->status_id = $request->status_id;
        $model->lead_target = $request->lead_target;
        $model->sales = $request->sales;

        $model->save();

        return redirect('/projects');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {

        if(Auth::user()->getRoleModule(Auth::user()->role_id,3) == 3){
            $request->project_id = $id;
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            
            $model = Project::find($id);
            $projects = Project::where('id',$id)->get();
            $accounts = Account::all();
            $document_types = DocumentType::all();
            $logins = ProjectLogin::where('project_id',$id)->get();
            //$task_status_options_JSON = Metadata::getTaskStatusOptions();
            //$task_status_options = json_decode($task_status_options_JSON["value"], true);
            $documents = ProjectDocument::where('project_id',$id)->orderBy('id', 'DESC')->get();
            $task_status = TaskStatus::all();
            // selecciono todos los usuarios activos
            $users_project = ProjectUser::
                where('project_id','=', $id)->get();
            $array = Array();
            foreach ($users_project as $item) { 
                $array[] = $item->user_id;
            }

            
            $pending_users = User::
                where('users.status_id', 1)
                ->where(function($query){
                    $query->where('users.role_id', 1); // admin
                    $query->orwhere('users.role_id', 2); // team
                    $query->orwhere('users.role_id', 3); // team
                        
                
                })
                ->whereNotIn('id', $array)
                ->orderBy("id", "desc")
                ->get();

            $users = User::orderBy('name')
            ->where('users.status_id', 1)
            ->where(function ($query){
                $query = $query->where("role_id", 1);
                $query = $query->orwhere("role_id", 2);
                
            })
            ->get();

            //$tasks = Task::where('poject_id',$id);
            // dd($project->users);
            
           // $project->tasks->sortByDesc("due_date");
            $task_types_component = TaskType::whereNull('parent_id')->get();

            return view('projects.show', compact('model','task_status','pending_users', 'users','projects','request','logins', 'accounts', 'document_types','documents','task_types_component'));
        }else{
            return redirect("http://myseo.com.co/");
        }
    }

    public function loginDownload($id,Request $request){
        $logins = ProjectLogin::where('project_id',$id)->get();
        return view('projects.login_download', compact('logins'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(  (Auth::user()->role_id == 1)){
            $model = Project::find($id);
            $project_status = ProjectStatus::all();
            $project_type = ProjectType::all();
            $task_types_component = TaskType::whereNull('parent_id')->get();
            return view('projects.edit', compact('model', 'project_status', 'project_type', 'task_types_component'));
        }else{
            return redirect("http://myseo.com.co/");
        }
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
        $model->weight = $request->weight;
        $model->budget = $request->budget;
        $model->start_date = $request->start_date;
        $model->finish_date = $request->finish_date;
        $model->weekly_pieces = $request->weekly_pieces;
        $model->ads_budget = $request->ads_budget;
        $model->status_id = $request->status_id;
        $model->color = $request->color;
        $model->lead_target = $request->lead_target;
        $model->monthly_points_goal = $request->monthly_points_goal;
        $model->sales = $request->sales;

        
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

    public function addProject(Request $request, $id){
        $model = new ProjectUser;

        $model->user_id = $id;
        $model->project_id = $request->project_id;
        

        $model->save();

        return redirect('/users/'.$id);


    }

    public function deleteUser(Request $request, $pid, $uid){
        $model = ProjectUser::where("project_id", $pid)->where("user_id",$uid);
        $model->delete();
        return redirect('/projects/'.$pid);
    }

    public function deleteProject(Request $request, $pid, $uid){
        $model = ProjectUser::where("project_id", $pid)->where("user_id",$uid);
        $model->delete();
        return redirect('/users/'.$uid);
    }

}
