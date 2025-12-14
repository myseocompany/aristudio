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


class TimerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request){
        $model = "";
        if(isset($request->task_id)&&($request->task_id!="")){
            $model = Task::find($request->task_id);
        }else{
            $model = new Task();
            $model->name = $request->name;
            $model->user_id = $request->user_id;
            $model->creator_user_id = $request->user_id;
            $model->updator_user_id = $request->user_id;
            $model->project_id = $request->project_id;
            $model->due_date = Carbon::now();
            
        }

        
        $model->started_at = Carbon::now();
        $model->save();

        return $model->id;
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
        if(isset($request->status_id) && $request->status_id == 7){
            $model->status_id = 7;
            $model->delivery_date = Carbon::now();
        }
        $model->save();

        $model->points =strval( $to->diffInSeconds($from)/3600);
        $model->save();

        if(isset($model->project))
            $model->project_name = $model->project->name; 
        $next_model_due_date = Carbon::now();
        $next_model_due_date->addDay();
        if(isset($request->status_id) && $request->status_id == 7){
            $next_model = new Task;
            $next_model->name = $model->name;
            $next_model->project_id = $model->project_id;
            $next_model->due_date = $next_model_due_date;
            $next_model->status_id = 7;
            $next_model->user_id = $model->user_id;
            $next_model->save();
        }
        return $model->toJson();
    }

    public function index(Request $request){
        $projects = Project::join('project_users', 'projects.id', '=', 'project_users.project_id')
            ->selectRaw('project_users.id as project_users_id, projects.id as id, projects.name as name, projects.weight as weight')
            ->where('project_users.user_id', \Auth::id())
            ->where('projects.status_id', 3)
            ->orderBy('projects.weight', 'ASC')
            ->get();

           

        $actual_task = Task::whereNotNull('started_at')
            ->whereNull('finished_at')
            ->where('user_id', \Auth::id())
            ->first();

        $today = Carbon::now()->format('Y-m-d');  // Usa Carbon para obtener la fecha actual
        $endOfDay = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');  // Final del día actual
        

        // Obtener la fecha de hoy en formato 'Y-m-d'
        $today = date('Y-m-d');

        $model = Task::join('projects', 'projects.id', '=', 'tasks.project_id')
            ->select('tasks.id as id', 'tasks.name as name', 'tasks.due_date as due_date', 'tasks.status_id as status_id', 'tasks.project_id')
            ->where('tasks.user_id', \Auth::id())
            ->whereIn('tasks.status_id', [1, 7])
            ->whereDate('tasks.due_date', '>=', $today) // Solo incluye tareas de hoy en adelante
            ->orderBy('tasks.due_date', 'ASC')  // Ordena primero por fecha de vencimiento
            ->orderBy('tasks.priority', 'DESC') // Luego ordena por prioridad
            ->paginate(50);

        


        return view('tasks.timer', compact('projects', 'request', 'actual_task', 'model'));
    }


       
}