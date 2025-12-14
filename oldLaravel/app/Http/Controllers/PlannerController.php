<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\TaskStatus;

use App\User;
use App\Project;
use App\TasksVersion;
use DB;
use Auth;
use Mail;
use Carbon\Carbon;
use App\TaskType;


class PlannerController extends Controller
{
	public function store(Request $request){
		$model = new Task();
		$model->name = $request->name;
		$model->user_id = $request->user_id;
		$model->creator_user_id = $request->user_id;
        $model->updator_user_id = $request->user_id;
        $model->project_id = $request->project_id;
        $model->status_id = 1;
        
        $model->due_date = Carbon::today();


        
        $model->save();
        if(isset($model->project))
            $model->project_name = $model->project->name; 
        if(isset($model->status)){
            $model->status_name = $model->status->name; 
            $model->status_background_color = $model->status->background_color; 
               
        }

        return $model;
	}

	public function stop(Request $request){
		$model =  Task::find($request->id);
		$model->name = $request->name;
		$model->project_id = $request->project_id;
		
		$model->finished_at = Carbon::now();
        $from = Carbon::parse($model->started_at);
        $to = Carbon::parse($model->finished_at);
        
        $model->due_date = Carbon::now();
        $model->status_id = 6;
        
        $model->save();
        $model->points = $to->diffInSeconds($from)/3600;
        $model->save();

        if(isset($model->project))
        	$model->project_name = $model->project->name; 


        return $model->toJson();
	}

	public function index(Request $request){
		$projects = Project::orderBy('name')
            ->join('project_users', 'projects.id', '=', 'project_users.project_id')
            ->selectRaw('project_users.id as project_users_id, projects.id as id, projects.name as name')
            ->where('project_users.user_id', \Auth::id())
            ->where('projects.status_id', 3)
            ->get();

        $users = User::where('status_id',1)->get();

        $actual_task = Task::whereNotNull('started_at')
        	->whereNull('finished_at')
        	->where('user_id', \Auth::id())
        	->first();

        $model = Task::where('user_id', \Auth::id())
             ->where('status_id',1)
        	->orderBy('due_date', 'DESC')
        	->paginate(20);

		return view('tasks.planner', compact('projects', 'request', 'actual_task', 'model', 'users'));
	}
}