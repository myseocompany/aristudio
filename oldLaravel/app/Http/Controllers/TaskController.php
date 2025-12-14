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
use App\TaskTypeFull;
use App\TaskMessage;
use App\ProjectUser;
use App\TaskUser;
use App\Notification;

class TaskController extends Controller
{

    protected $attributes = ['project_name'];
    protected $appends = ['project_name'];
    protected $project_name;
    public $from_date;
    public $to_date;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    function getActiveTeam($request)
    {
        $model = User::where('status_id', 1)
            ->whereIn('role_id', [1, 2])
            ->get();
        return $model;
    }

    function getProjects($request)
    {
        return Project::where('status_id', 3)
            ->orderBy('name', 'ASC')
            ->get();
    }

    function getFilteredActiveTeam($request)
    {
        $model = User::where('status_id', 1)
            ->whereIn('role_id', [1, 2])
            ->where(function ($query) use ($request) {
                if (isset($request->user_id)) {
                    $this->getSelectedUsers($request);
                    $query->whereIn('id', $this->getSelectedUsers($request));
                } else {
                    $users = $this->getActiveTeam($request);
                    $users_id = array();
                    foreach ($users as $key => $value) {
                        $users_id[] = $value->id;
                    }
                    $query->whereIn('id', $users_id);
                }
            })
            ->get();
        return $model;
    }

    function getFilteredProjects($request)
    {
        return Project::where('status_id', 3)
            ->orderBy('weight', 'ASC')
            ->where(function ($query) use ($request) {
                $projects = $this->getProjects($request);
                $projects_id = array();
                foreach ($projects as $key => $value) {
                    $projects_id[] = $value->id;
                }
                if (isset($request->project_id)) {
                    $query->where('id', $request->project_id);
                } else {
                    $query->whereIn('id', $projects_id);
                }
            })
            ->get();
    }
    function getDailyStatuses()
    {
        return [1, 8, 2, 6, 58];
    }


    function daily(Request $request)
    {
        $users = $this->getFilteredActiveTeam($request);
        $projects = $this->getFilteredProjects($request);
    
        $users_options = $this->getActiveTeam($request);
        $projects_options = $this->getProjects($request);
    
        $statuses_id = $this->getDailyStatuses();
    
        // Construcción de la consulta
        $tasksQuery = Task::query()
            ->whereIn('status_id', $statuses_id)
            ->when($request->has('value_generated_1') && $request->value_generated_1 == 1, function ($query) {
                return $query->where('value_generated', 1); // Filtrar tareas con value_generated = 1
            })
            ->when($request->has('value_generated_0') && $request->value_generated_0 == 1, function ($query) {
                return $query->where('value_generated', 0); // Filtrar tareas con value_generated = 0
            })
            ->when($request->project_id, function ($query) use ($request) {
                return $query->where('project_id', $request->project_id);
            })
            ->when($request->user_id, function ($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->orderBy('due_date', 'asc');
    
        $tasks = $tasksQuery->get();
    
        $task_status = TaskStatus::where('status_id', 1)->orderBy('weight', 'asc')->get();
        $column_width = '';
        if ($users->count() <> 0) {
            $column_width = round(100 / $users->count(), 0);
            $column_width = 'width="' . $column_width . '%"';
        }
    
        return view('tasks.daily', compact('users', 'projects', 'users_options', 'projects_options', 'task_status', 'tasks', 'request', 'statuses_id', 'column_width'));
    }
    

    function getSelectedUsers($request)
    {


        $url = $request->fullurl();
        $paramenters = explode("&", $url);
        $res = array();
        foreach ($paramenters as $key => $value) {
            if (strpos($value, "user_id") !== false && (str_replace("user_id=", "", $value) != 0)) {
                $res[] = str_replace("user_id=", "", $value);
            }
        }

        return $res;
    }

    function getStatusID($request)
    {


        $url = $request->fullurl();
        $paramenters = explode("&", $url);
        $res = array();
        foreach ($paramenters as $key => $value) {
            if (strpos($value, "status_id") !== false && (str_replace("status_id=", "", $value) != 0)) {
                $res[] = str_replace("status_id=", "", $value);
            }
        }

        if (!count($res)) {
            $model = TaskStatus::orderBy('weight', 'asc')->get();
            foreach ($model as $item) {
                $res[] = $item->id;
            }

            //$res[]=1;
            /*
            $res[]=8;
            $res[]=2;
            $res[]=6;
            */
        }

        //dd($res);

        return $res;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // buscador - controller
    //  public function ajaxsearch(Request $request) {
    //     $query = Task::name($request->get('name'))->orderBy('id', 'ASC')->get();
    //     return view('tasks.ajax')->with('tasks', $query);
    // }
    public function userIndex(Request $request)
    {
        //$allowed_projects = 
        //$user = User::findOrFail(1);
        // selecciono todos los usuarios activos

        $users = User::orderBy('name')
            ->where('users.status_id', 1)
            ->where(function ($query) {
                $query = $query->where("role_id", 1);
                $query = $query->orwhere("role_id", 2);
            })
            ->get();
        // saco de la URL los status_id    
        $statuses_id = $this->getStatusID($request);

        $model  =  $this->getTask($request, $statuses_id);
        $request->user_id = Auth::user()->id;


        if (isset($request->project_id) && ($request->project_id != null)) {

            $tasksGroup = $this->getTaskGroupByStatus($request, $statuses_id);
        } else
            $tasksGroup = $this->getTaskGroupByProject($request, $statuses_id);
        // filtro con proyectos

        //$task_status_options_JSON = Metadata::getTaskStatusOptions();
        //$task_status_options = json_decode()->all();($task_status_options_JSON["value"], true);
        //$task_status = TaskStatus::all();
        //$task_status = TaskStatus::orderBy('weight','asc')->get();
        $task_status = TaskStatus::where('status_id', 1)->orderBy('weight', 'asc')->get();


        $projects = Project::orderBy('weight')
            ->join('project_users', 'projects.id', '=', 'project_users.project_id')
            ->selectRaw('project_users.id as project_users_id, projects.id as id, projects.name as name')
            ->where('project_users.user_id', \Auth::id())
            ->where('projects.status_id', 3)
            ->get();



        $total_points = 0;


        /*
        foreach ($tasks as $model){
            
            $model->project_name = "";
            if(isset($model->project_id)){
                $project = Project::find($model->project_id);
                if(isset($project))
                    $model->project_name = $project->name;
                
            }
            if(isset($model->user_id))
                $model->user_name = User::find($model->user_id)->name;
           // if(isset($model->status_id))
             //   $model->status_name = Metadata::getTaskStatusOptionsById($model->status_id);
             if( isset($model->status_id) && ($model->status_id <>"") ){
                
                $taskStatus = TaskStatus::find($model->status_id);
                if(isset($taskStatus))
                    $model->task_status = $taskStatus->name;
                else
                    $model->task_status = "";
                //var_dump($model);
            }
        }
        */
        /*$model = $tasks;
        */
        // $status_task = Task::find($id);
        $days = array();
        if (isset($request->from_date)) {
            $from = Carbon::parse($request->from_date);
            $to = Carbon::parse($request->to_date);
            $diff = $to->diffInDays($from);
            $days[] = $from->toDateString();
            for ($i = 0; $i < $diff; $i++) {
                $days[] = $from->addDays(1)->toDateString();
            }
            //dd($days);
        }

        $dates = $this->getDates($request);

        $task_types = TaskType::whereNull('parent_id')->get();
        $task_types_options = DB::select('select * from vw_task_types_full');

        $task_types_component = TaskType::whereNull('parent_id')->get();

        $notifications = Notification::where('recipient_id', Auth::id())
            ->whereNull('reviewed')
            ->get();

        $weeks = 1;

        $month_start = strtotime('monday this week');
        $from_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d', $month_start));

        $month_end = strtotime('monday next week');
        $to_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d', $month_end));


        $weeks = $from_date->diffInDays($to_date);
        if ($weeks == 0) {
            $weeks = 1;
        }
        //dd($weeks);
        $weeks_array = array();
        for ($i = 0; $i < $weeks; $i++) {
            $weeks_array[] =
                array($from_date->format('Y-m-d') . " 00:00:00", $from_date->format('Y-m-d') . " 23:59:59");
            $from_date->addDays(1)->format('Y-m-d');
        }
        //dd($weeks_array);
        $status_array = array(56);




        $user_data_with_points = array();
        //Obtener id de los usuarios con puntos
        for ($i = 0; $i < $weeks; $i++) {
            foreach ($users as $user) {
                $task_points = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->first();
                if ($task_points) {
                    $user_data_with_points[] = $task_points->user_id;
                }
            }
        }
        //obtener usuarios
        $users_graph = User::orderBy('name')
            ->where('users.status_id', 1)
            ->whereIn('id', array_unique($user_data_with_points))
            ->get();


        for ($i = 0; $i < $weeks; $i++) {
            $user_data = array();
            foreach ($users_graph as $user) {
                $tasks = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->sum('points');
                $user_data[] = $tasks;

                $task_points = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->first();
                if ($task_points) {
                    $user_data_with_points[] = $task_points->user_id;
                }
            }
            $data[] = $user_data;
        }



        return view('tasks.index', compact(
            'model',
            'users',
            'projects',
            'task_status',
            'request',
            'statuses_id',
            'total_points',
            'tasksGroup',
            'days',
            'dates',
            'task_types',
            'task_types_options',
            'task_types_component',
            'notifications',
            'weeks_array',
            'weeks',
            'data',
            'users_graph'
        ));
    }

    public function indexResponsive(Request $request)
    {
        $this->getIndex($request, $model, $users, $projects, $task_status, $statuses_id, $total_points, $tasksGroup, $days, $task_types, $task_subtypes);
        $task_types_component = TaskType::whereNull('parent_id')->orderBy('order', 'DESC')->get();


        $weeks = 1;

        $month_start = strtotime('monday this week');
        $from_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d', $month_start));

        $month_end = strtotime('monday next week');
        $to_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d', $month_end));


        $weeks = $from_date->diffInDays($to_date);
        if ($weeks == 0) {
            $weeks = 1;
        }
        //dd($weeks);
        $weeks_array = array();
        for ($i = 0; $i < $weeks; $i++) {
            $weeks_array[] =
                array($from_date->format('Y-m-d') . " 00:00:00", $from_date->format('Y-m-d') . " 23:59:59");
            $from_date->addDays(1)->format('Y-m-d');
        }
        //dd($weeks_array);
        $status_array = array(56);



        $user_data_with_points = array();

        //Obtener id de los usuarios con puntos
        for ($i = 0; $i < $weeks; $i++) {
            foreach ($users as $user) {
                $task_points = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->first();
                if ($task_points) {
                    $user_data_with_points[] = $task_points->user_id;
                }
            }
        }
        //obtener usuarios
        $users_graph = User::orderBy('name')
            ->where('users.status_id', 1)
            ->whereIn('id', array_unique($user_data_with_points))
            ->get();


        for ($i = 0; $i < $weeks; $i++) {
            $user_data = array();
            foreach ($users_graph as $user) {
                $tasks = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->sum('points');
                $user_data[] = $tasks;

                $task_points = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->first();
                if ($task_points) {
                    $user_data_with_points[] = $task_points->user_id;
                }
            }
            $data[] = $user_data;
        }


        return view('tasks.index_responsive', compact('model', 'users', 'projects', 'task_status', 'request', 'statuses_id', 'total_points', 'tasksGroup', 'days', 'task_types', 'task_types_component', 'task_subtypes', 'weeks_array', 'weeks', 'data', 'users_graph'));
    }


    public function getPendingStatusID()
    {
        $ids = TaskStatus::orderBy('weight', 'asc')
            ->where('status_id', 1)
            ->where('pending', 1)
            ->pluck('id') // Extrae solo los ids
            ->toArray(); // Convierte la colección a un array

        return $ids;
    }

    public function getPriority(Request $request)
    {

        $model = Task::where(function ($query) use ($request) {
            // busco la prioridad de la semana o la del mes
            $query->whereIn("type_id", [117, 120]);
            // miro que no se haya cumplido
            $query->where("status_id", 1);

            if (isset($request->project_id) && ($request->project_id != ""))
                $query->where("project_id", $request->project_id);
        })->get();

        return $model;
    }
    public function index(Request $request)
    {

        $this->getIndex($request, $model, $users, $projects, $task_status, $statuses_id, $total_points, $tasksGroup, $days, $task_types, $task_subtypes);

        $task_types_component = TaskType::whereNull('parent_id')->get();


        $weeks = 1;

        $month_start = strtotime('monday this week');
        $from_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d', $month_start));

        $month_end = strtotime('monday next week');
        $to_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d', $month_end));


        $weeks = $from_date->diffInDays($to_date);
        if ($weeks == 0) {
            $weeks = 1;
        }
        //dd($weeks);
        $weeks_array = array();
        for ($i = 0; $i < $weeks; $i++) {
            $weeks_array[] =
                array($from_date->format('Y-m-d') . " 00:00:00", $from_date->format('Y-m-d') . " 23:59:59");
            $from_date->addDays(1)->format('Y-m-d');
        }
        //dd($weeks_array);
        $status_array = array(56);



        $user_data_with_points = array();

        //Obtener id de los usuarios con puntos
        for ($i = 0; $i < $weeks; $i++) {
            foreach ($users as $user) {
                $task_points = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->first();
                if ($task_points) {
                    $user_data_with_points[] = $task_points->user_id;
                }
            }
        }
        //obtener usuarios
        $users_graph = User::orderBy('name')
            ->where('users.status_id', 1)
            ->whereIn('id', array_unique($user_data_with_points))
            ->get();


        for ($i = 0; $i < $weeks; $i++) {
            $user_data = array();
            foreach ($users_graph as $user) {
                $tasks = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->sum('points');
                $user_data[] = $tasks;

                $task_points = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->first();
                if ($task_points) {
                    $user_data_with_points[] = $task_points->user_id;
                }
            }
            $data[] = $user_data;
        }

        // Clonar el request original
        $clonedRequest = clone $request;

        // Modificar el request clonado
        $clonedRequest->merge(['type_id' => 117]);

        // Obtener los IDs de los estados pendientes
        $pending_status = $this->getPendingStatusID();

        // Obtener las tareas con el request clonado y los estados pendientes
        //$priority = $this->getTask($clonedRequest, $pending_status);
        $priority = "";
        $priority = $this->getPriority($request);


        //dd($priority);
        $project = "";
        if (isset($request->project_id) && ($request->project_id != ""))
            $project = Project::find($request->project_id);



        return view('tasks.index', compact(
            'project',
            'priority',
            'model',
            'users',
            'projects',
            'task_status',
            'request',
            'statuses_id',
            'total_points',
            'tasksGroup',
            'days',
            'task_types',
            'task_types_component',
            'task_subtypes',
            'weeks_array',
            'weeks',
            'data',
            'users_graph'
        ));
    }

    public function schedule(Request $request)
    {
        $this->getIndex($request, $model, $users, $projects, $task_status, $statuses_id, $total_points, $tasksGroup, $days, $task_types, $task_subtypes);
        $task_types_component = TaskType::whereNull('parent_id')->get();
        return view(
            'tasks.calendar.schedule',
            compact('model', 'users', 'projects', 'task_status', 'request', 'statuses_id', 'total_points', 'tasksGroup', 'days', 'task_types', 'task_subtypes', 'task_types_component')
        );
    }


    public function printIndex(Request $request)
    {
        $this->getIndex($request, $model, $users, $projects, $task_status, $statuses_id, $total_points, $tasksGroup, $days, $task_types, $weeks_array, $weeks, $data);

        return view('tasks.indexPrintable', compact('model', 'users', 'projects', 'task_status', 'request', 'statuses_id', 'total_points', 'tasksGroup', 'days', 'task_types'));
    }



    /*  INDEX que ve todo el mundo */
    public function getIndex(Request $request, &$model, &$users, &$projects, &$task_status, &$statuses_id, &$total_points, &$tasksGroup, &$days, &$task_types, &$task_subtypes)
    {


        //$allowed_projects = 
        $user = User::findOrFail(1);
        // selecciono todos los usuarios activos
        $users = User::orderBy('name')
            ->where('users.status_id', 1)
            ->where(function ($query) {
                $query = $query->where("role_id", 1);
                $query = $query->orwhere("role_id", 2);
            })
            ->get();


        // saco de la URL los status_id    
        $statuses_id = $this->getStatusID($request);
        $model  =  $this->getTask($request, $statuses_id);


        if (isset($request->project_id) && ($request->project_id != null)) {
            $tasksGroup = $this->getTaskGroupByStatus($request, $statuses_id); // estados de un solo proyecto

        } else
            $tasksGroup = $this->getTaskGroupByProject($request, $statuses_id); // estados de todos los proyectos

        // filtro con proyectos

        //$task_status_options_JSON = Metadata::getTaskStatusOptions();
        //$task_status_options = json_decode()->all();($task_status_options_JSON["value"], true);
        //$task_status = TaskStatus::all();
        $task_status = TaskStatus::where('status_id', 1)->orderBy('weight', 'asc')->get();



        $projects = Project::orderBy('weight')
            ->join('project_users', 'projects.id', '=', 'project_users.project_id')
            ->selectRaw('project_users.id as project_users_id, projects.id as id, projects.name as name')
            ->where('project_users.user_id', \Auth::id())
            ->where('projects.status_id', 3)

            ->get();



        $total_points = 0;



        foreach ($model as $item) {

            $item->project_name = "";
            if (isset($item->project_id)) {
                $project = Project::find($item->project_id);
                if (isset($project))
                    $item->project_name = $project->name;
            }
            if (isset($item->user_id))
                $item->user_name = User::find($item->user_id)->name;
            // if(isset($item->status_id))
            //   $item->status_name = Metadata::getTaskStatusOptionsById($item->status_id);
            if (isset($item->status_id) && ($item->status_id <> "")) {

                $taskStatus = TaskStatus::find($item->status_id);
                if (isset($taskStatus))
                    $item->task_status = $taskStatus->name;
                else
                    $item->task_status = "";
                //var_dump($model);
            }
        }
        // $status_task = Task::find($id);
        $days = array();
        if (isset($request->from_date)) {
            $from = Carbon::parse($request->from_date);
            $to = Carbon::parse($request->to_date);
            $diff = $to->diffInDays($from);
            $days[] = $from->toDateString();
            for ($i = 0; $i < $diff; $i++) {
                $days[] = $from->addDays(1)->toDateString();
            }
            //dd($days);
        }


        $task_types = array();
        TaskType::getTree($task_types, null);

        $task_types_component = TaskType::whereNull('parent_id')->get();

        //$task_types_options = DB::table('vw_task_types_full')->all();
        $task_subtypes = null;
        if (isset($request['type_id']))
            $task_subtypes = TaskType::where('parent_id', $request['type_id'])->get();
    }

    public function getTaskTypes()
    {
        $model = TaskType::whereNull('parent_id')->get();


        return $model;
    }



    public function getStartAndEndDate($week, $year)
    {

        $time = strtotime("1 January $year", time());
        $day = date('w', $time);
        $time += ((7 * $week) + 1 - $day) * 24 * 3600;
        $return[0] = date('Y-n-j', $time);
        $time += 6 * 24 * 3600;
        $return[1] = date('Y-n-j', $time);
        return $return;
    }


    public function getDatesOld($request)
    {
        $to_date = Carbon::today()->addDays(1); // ayer
        $from_date = Carbon::today();


        if (isset($request->from_date) && ($request->from_date != null)) {
            //dd($request->to_date);

            //$request->from_date = '2020-05-01';

            $from_date  = Carbon::createFromFormat('Y-m-d', $request->from_date);
            $to_date    = Carbon::createFromFormat('Y-m-d', $request->to_date);
        }

        $to_date = $to_date->format('Y-m-d') . " 23:59:59";
        $from_date = $from_date->format('Y-m-d');

        return array($from_date, $to_date);
    }

    public function getDates($request)
    {
        // Define las fechas por defecto para hoy y el final del día de hoy
        $from_date = Carbon::today();
        $to_date = Carbon::today()->endOfDay(); // Asegura que el día actual esté completo

        // Verifica si se han proporcionado fechas específicas en el request
        if (isset($request->from_date) && !empty($request->from_date) && isset($request->to_date) && !empty($request->to_date)) {
            $from_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date . ' 00:00:00'); // Establece inicio del día para from_date
            $to_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->to_date . ' 23:59:59'); // Establece fin del día para to_date
        }

        // Formatea las fechas para la consulta
        $from_date = $from_date->format('Y-m-d H:i:s');
        $to_date = $to_date->format('Y-m-d H:i:s');




        return array($from_date, $to_date);
    }

    public function getTask($request, $statuses_id)
    {
        $dates = $this->getDates($request);

        $model = Task::leftJoin('task_statuses', 'task_statuses.id', '=', 'tasks.status_id')
                    ->leftJoin('project_users', 'tasks.project_id', '=', 'project_users.project_id')
                    ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
                    ->where(function ($query) use ($request, $statuses_id, $dates) {
                        $query->where('project_users.user_id', '=', Auth::id());

                        if ($request->has('user_id')) {
                            $query->where('tasks.user_id', '=', $request->user_id);
                        }

                        if ($request->has('project_id')) {
                            $query->where('tasks.project_id', '=', $request->project_id);
                        }

                        if ($request->has('type_id')) {
                            $query->where('tasks.type_id', '=', $request->type_id);
                        }

                        if (!empty($statuses_id)) {
                            $query->whereIn('tasks.status_id', $statuses_id);
                        } else {
                            $query->where('tasks.status_id', '=', 1); // Default status
                        }

                        // Manejo de los filtros de value_generated
                        if ($request->has('no_value_generated')) {
                            if ($request->no_value_generated == 1) {
                                $query->where('tasks.value_generated', '=', 1); // Mostrar tareas con value_generated = 1
                            } elseif ($request->no_value_generated == 0) {
                                $query->where('tasks.value_generated', '=', 0); // Mostrar tareas con value_generated = 0
                            }
                        }
                        

                        // Aplicar filtro de rango de fechas
                        $query->whereBetween('tasks.due_date', $dates);

                        if ($request->has('querystr')) {
                            $query->where(function ($query) use ($request) {
                                $query->orWhere('tasks.name', 'like', '%' . $request->querystr . '%')
                                    ->orWhere('tasks.description', 'like', '%' . $request->querystr . '%');
                            });
                        }
                    })
                    ->select(DB::raw('tasks.*, DATEDIFF(now(), tasks.created_at) as lead_time'))
                    ->orderBy('tasks.priority', 'desc')
                    ->orderBy('tasks.due_date', 'asc')
                    ->get();

        return $model;
    }

    

    public function getTaskOld($request, $statuses_id)
    {
        $dates = $this->getDates($request);
        $model = Task::leftJoin('task_statuses', 'task_statuses.id', 'tasks.status_id')
            ->leftJoin('project_users', 'tasks.project_id', '=', 'project_users.project_id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            //->whereNull('parent_id')
            ->where(
                function ($query) use ($request, $statuses_id, $dates) {
                    $query->where('project_users.user_id', "=", \Auth::id());
                    if (isset($request->user_id) && ($request->user_id != null)) {
                        $query->where('tasks.user_id', "=", $request->user_id);
                    }
                    if (isset($request->project_id))
                        $query->where('tasks.project_id', "=", $request->project_id);

                    if (isset($request->type_id))
                        $query->where('tasks.type_id', "=", $request->type_id);

                    $str = "";
                    if (sizeof($statuses_id)) {
                        $str = "";

                        $query->where(function ($query) use ($statuses_id, $str) {
                            foreach ($statuses_id as $key => $value) {
                                $query->orwhere('tasks.status_id', "=", $value);
                            }
                        });
                    } else {
                        $query->where('tasks.status_id', "=", 1);
                    }
                    // if week & year
                    if (isset($request->week) && ($request->week != null)) {
                        $dates = $this->getStartAndEndDate($request->week, $request->year);
                        $request->from_date = $dates[0];
                        $request->to_date = $dates[1];
                    }
                    $query->whereBetween('due_date', $dates);



                    if (isset($request->querystr)) {
                        $query->where(function ($query) use ($request) {
                            $query->orwhere('tasks.name', "like", "%" . $request->querystr . "%");
                            $query->orwhere('tasks.description', "like", "%" . $request->querystr . "%");
                        });
                    }

                    if (isset($request->no_value_generated) && ($request->no_value_generated != null)) {
                        if ($request->no_value_generated == 1)
                            $query->where(
                                function ($query) use ($request, $statuses_id) {
                                    $query->where('value_generated', "=", 1);
                                }
                            );
                        if ($request->no_value_generated == 0) {
                            $query->where(function ($query) {
                                $query->where('value_generated', "=", 0);
                                $query->orWhereNull('value_generated');
                            });
                        }
                    }

                    if (isset($request->priority) && ($request->priority != "")) {
                        $query->where(function ($query) use ($request) {
                            $query->orwhere('tasks.priority', "=", $request->priority);
                        });
                    }
                }
            )
            ->select(DB::raw('tasks.*, DATEDIFF( now(), tasks.created_at ) as lead_time'))

            /* ->orderBy('projects.weight', 'asc') */
            /*->orderBy('tasks.project_id', 'asc')*/
            ->orderBy('tasks.priority', 'desc')
            /*->orderBy('task_statuses.weight', 'asc')*/
            ->orderBy('tasks.due_date', 'asc')

            ->get();
        /*
                if(isset($request->order_by)){ 
                    switch ($request->order_by) {
                        case 'priority':
                            $model = $model->sortByDesc('priority');
                            break;
                        case 'due_date':
                            $model = $model->sortBy('due_date');
                            break;
                        case 'lead_time':
                            $model = $model->sortByDesc('lead_time');
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }
    */
        //  dd($model); 
        //$sql = $model->toSql();
        //dd($sql);
        return $model;
    }

    public function belongsToProject($pid)
    {
        $model = ProjectUser::where('project_id', $pid)
            ->where('user_id', \Auth::id())->first();
        $belongs = true;
        if (!$model)
            $belongs = false;
        return $belongs;
    }

    public function getTaskGroupByStatus($request, $statuses_id)
    {
        /*dd($statuses_id);
        */
        $model = Task::join('task_statuses', 'task_statuses.id', 'tasks.status_id')
            ->where('project_id', $request['project_id'])
            ->where(
                function ($query) use ($request, $statuses_id) {

                    if (sizeof($statuses_id)) {

                        $query->whereIn('tasks.status_id', $statuses_id);
                    }
                    if (isset($request->querystr)) {
                        $query = $query->where(function ($query) use ($request) {
                            $query->orwhere('tasks.name', "like", "%" . $request->querystr . "%");
                            $query->orwhere('tasks.description', "like", "%" . $request->querystr . "%");
                        });
                    }



                    if (isset($request->type_id) && ($request->type_id != "") && ($request->type_id != null)) {
                        $query->where('tasks.type_id', "=", $request->type_id);
                    }
                    if (isset($request->subtype_id) && ($request->subtype_id != "") && ($request->subtype_id != null)) {
                        $query->where('tasks.sub_type_id', "=", $request->subtype_id);
                    }


                    if (isset($request->from_date) && ($request->from_date != null)) {
                        $dates = $this->getDates($request);
                    } else {

                        $dates = array($this->from_date, $this->to_date);
                    }

                    $query->whereBetween('due_date', $dates);

                    if (isset($request->no_value_generated) && ($request->no_value_generated != null)) {
                        if ($request->no_value_generated == 1)
                            $query->where(
                                function ($query) use ($request, $statuses_id) {
                                    $query->where('value_generated', "=", 1);
                                }
                            );
                        if ($request->no_value_generated == 0) {
                            $query->where(function ($query) {
                                $query->where('value_generated', "=", 0);
                                $query->orWhereNull('value_generated');
                            });
                        }
                    }

                    if (isset($request->user_id) && ($request->user_id != null)) {
                        $query->where('tasks.user_id', "=", $request->user_id);
                    }
                }
            )
            ->select(DB::raw('tasks.status_id, sum(points) as sum_points, count(tasks.id) as count_points'))
            ->groupBy('weight', 'tasks.status_id')
            ->orderby('weight')
            ->get();


        /*           
        foreach ($model as $item){
            $item->color = TaskStatus::getColor($item->status_id);
            $item->name = TaskStatus::getName($item->status_id);
               
        }
        */

        //dd($model);

        return $model;
    }

    public function getTaskGroupByProject($request, $statuses_id)
    {


        $model = Task::join('project_users', 'tasks.project_id', '=', 'project_users.project_id')
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->where(
                function ($query) use ($request, $statuses_id) {
                    $query = $query->where('project_users.user_id', "=", \Auth::id());
                    if (isset($request->user_id) && ($request->user_id != null)) {
                        $query = $query->where('tasks.user_id', "=", $request->user_id);
                    }
                    if (isset($request->project_id))
                        $query = $query->where('tasks.project_id', "=", $request->project_id);
                    if (isset($request->type_id))
                        $query = $query->where('tasks.type_id', "=", $request->type_id);

                    if (sizeof($statuses_id)) {
                        $query = $query->where(function ($query) use ($statuses_id) {
                            foreach ($statuses_id as $key => $value) {
                                $query = $query->orwhere('tasks.status_id', "=", $value);
                            }
                        });
                    }
                    if (isset($request->from_date) && ($request->from_date != null)) {
                        $dates = $this->getDates($request);
                        $query = $query->whereBetween('due_date', $dates);
                    } else {

                        $query = $query->whereBetween('due_date', array($this->from_date, $this->to_date));
                    }
                    if (isset($request->querystr)) {
                        $query = $query->where(function ($query) use ($request) {
                            $query->orwhere('tasks.name', "like", "%" . $request->querystr . "%");
                            $query->orwhere('tasks.description', "like", "%" . $request->querystr . "%");
                        });
                    }
                    if (isset($request->no_value_generated) && ($request->no_value_generated != null)) {
                        if ($request->no_value_generated == 1)
                            $query->where(
                                function ($query) use ($request, $statuses_id) {
                                    $query->where('value_generated', "=", 1);
                                }
                            );
                        if ($request->no_value_generated == 0) {
                            $query->where('value_generated', "=", 0);
                        }
                    }
                }
            )
            ->select(DB::raw('tasks.project_id as project_id, projects.name, projects.weight,  sum(points) as sum_points, count(tasks.id) as count_points'))

            ->groupBy('projects.name')
            ->groupBy('tasks.project_id')
            ->groupBy('projects.weight')

            ->orderby('projects.weight')
            ->get();

        foreach ($model as $item) {
            $item->color = $item->project->color;
            $item->name = $item->project->name;
        }
        return $model;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        // $task_status_options_JSON = TaskStatus::getTaskStatusOptionsById($id);
        // $task_status_options = json_decode()->all();($task_status_options_JSON["value"], true);
        $users = User::where("role_id", '=', '1')
            ->where("role_id", '=', '2')
            ->where(function ($query) {
                $query = $query->where("role_id", 1);
                $query = $query->orwhere("role_id", 2);
            })
            ->get();
        $task_status = TaskStatus::where('status_id', 1)->orderBy('weight', 'asc')->get();
        //$task_status = TaskStatus::all();


        $projects = Project::orderBy('weight')
            ->join('project_users', 'projects.id', '=', 'project_users.project_id')
            ->selectRaw('project_users.id as project_users_id, projects.id as id, projects.name as name')
            ->where('project_users.user_id', \Auth::id())
            ->get();

        // $model

        //dd($task_status_options);
        return view('tasks.create', compact('users', 'task_status', 'projects', 'model'));
    }


    /**
     * Convierte el tiempo ingresado en el request a horas en formato decimal.
     *
     * @param Illuminate\Http\Request $request El request que contiene los datos de tiempo.
     * @return float Total de horas en formato decimal o null si la validación falla.
     */
    protected function convertTimeToDecimalFromRequest(Request $request)
    {

        // Extraer valores de horas, minutos y segundos
        $hours = $request->hours ?? 0; // Usa null coalescence para asegurar valores por defecto
        $minutes = $request->minutes ?? 0;
        $seconds = $request->seconds ?? 0;

        // Conversión de tiempo a horas en formato decimal
        $totalHours = $hours + ($minutes / 60) + ($seconds / 3600);


        return $totalHours;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if(  (Auth::user()->role_id == 1)){
    
        $model = new Task;
    
        $model->id = $request->id;
        $model->name = $request->name;
        // Realiza el cast a float antes de guardar
        $totalHours = $this->convertTimeToDecimalFromRequest($request);
    
        $model->points = $request->points;
    
        $model->name = $request->name;
    
        // dd($model->points);
    
        $model->description = $request->description;
        $model->project_id = $request->project_id;
        $model->user_id = $request->user_id;
        $model->status_id = $request->status_id;
        $model->parent_id = $request->parent_id;
    
        $model->caption = $request->caption;
        $model->copy = $request->copy;
    
        $model->priority = $request->priority;
    
        $model->estimated_points = $request->estimated_points;
    
        if ($request->type_id != "") {
            $model->type_id = $request->type_id;
        }
        if ($request->sub_type_id != "") {
            $model->sub_type_id = $request->sub_type_id;
        }
    
        $model->creator_user_id = Auth::id();
        $model->updator_user_id = Auth::id();
    
        $model->due_date = $request->due_date;

        $model->delivery_date = $request->delivery_date;
    
        // Nuevo campo 'value_generated'
        $model->value_generated = $request->has('value_generated') ? $request->value_generated : 1;
    
        $model->url_finished = $request->url_finished;
    
        if ($request->hasFile('file')) {
    
            $request->file('file')->store('public/files');
            // ensure every image has a different name
            $path = $request->file('file')->hashName();
            $model->file_url = $path;
        }
    
        $model->save();
    
        $this->sendNotification($model);
        $version = new \App\TasksVersion;
        $this->saveVersion($model);
    
        // dd($request);
        if (isset($request->from) && ($request->from == "project")) {
            return back();
        }
    
        // return redirect('/tasks');
        //  }else{
        //    return Redirect::back()->withErrors(['msg', 'You are not allowed to make this action']);
        //    }
    }
    
    

    public function storeFast(Request $request)
    {
        //
        $model = new Task;

        $model->name = $request->name;
        $model->points = 1;
        //$model->description = $request->description;
        if (isset($request->project_id))
            $model->project_id = $request->project_id;
        $model->user_id = \Auth::id();
        $model->status_id = 1;


        $model->due_date = date("Y/m/d");
        $model->delivery_date = date("Y/m/d");
        $model->save();

        //dd($request);
        if (isset($request->from) && ($request->from == "project")) {
            return back();
        }

        return redirect('/tasks');
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $model = Task::find($id);
        $versions = TasksVersion::where('task_id', '=', $id)
            ->orderby("updated_at", "DESC")->get();


        $model->project_name = "without project";

        if (isset($model->project_id)) {
            $project = Project::find($model->project_id);
            if (isset($project))
                $model->project_name = $project->name;
        }

        if (isset($model->user_id))
            $model->user_name = User::find($model->user_id)->name;

        $taskStatus = TaskStatus::find($model->status_id);
        if (isset($taskStatus))
            $model->task_status = $taskStatus->name;
        else
            $model->task_status = "";
        //var_dump($model);

        $task_users = TaskUser::where('task_id', $id)->orderby('created_at', 'DESC')->get();

        $notifications = Notification::where('recipient_id', Auth::id())
            ->whereNull('reviewed')
            ->get();

        return view('tasks.show', compact('model', 'versions', 'task_users', 'notifications'));
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
        $model = Task::find($id);

        // $task_status_options_JSON = Metadata::getTaskStatusOptions();
        // $task_status_options = json_decode()->all();($task_status_options_JSON["value"], true);
        //$task_status = TaskStatus::orderBy("weight", "asc")->get();
        $task_status = TaskStatus::where('status_id', 1)->orderBy('weight', 'asc')->get();

        $users = User::orderBy('name')
            ->where('users.status_id', 1)
            ->where(function ($query) {
                $query = $query->where("role_id", 1);
                $query = $query->orwhere("role_id", 2);
            })
            ->get();

        $projects = Project::orderBy('weight')
            ->join('project_users', 'projects.id', '=', 'project_users.project_id')
            ->selectRaw('project_users.id as project_users_id, projects.id as id, projects.name as name')
            ->where('project_users.user_id', \Auth::id())
            ->where('status_id', 3)
            ->get();

        $task_types = TaskType::whereNull('parent_id')->get();
        $task_sub_type = TaskType::all();
        return view('tasks.edit', compact('model', 'task_status', 'users', 'projects', 'task_types', 'task_sub_type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        //
        $model = Task::find($request->id);

        if ($model) {
            $model->name = $request->name;
            $model->points = $request->points;
            $model->estimated_points = $request->estimated_points;

            if (isset($request->description))
                $model->description = $request->description;
            $model->project_id = $request->project_id;
            $model->user_id = $request->user_id;
            $model->priority = $request->priority;

            if (isset($request->caption))
                $model->caption = $request->caption;
            if (isset($request->copy))
                $model->copy = $request->copy;


            if (isset($request->parent_id))
                $model->parent_id = $request->parent_id;


            if (isset($request->type_id)) {
                $model->type_id = $request->type_id;
            }
            if (isset($request->sub_type_id)) {
                $model->sub_type_id = $request->sub_type_id;
            }

            if ($request->has('value_generated')) {
                $task->value_generated = $request->value_generated; // Puede ser 1 o 0
            } else {
                $task->value_generated = null; // Si no está seleccionado, se guarda como null
            }

            $model->updator_user_id = Auth::id();

            if (($request->status_id == 3)) {

                if ((Auth::user()->role_id == 1))
                    $model->status_id = $request->status_id;
            } else {
                $model->status_id = $request->status_id;
            }

            $model->due_date = $request->due_date;
            $model->delivery_date = $request->delivery_date;

            if (isset($request->value_generated)) {
                $model->value_generated = true;
            } else {
                $model->value_generated = false;
            }


            $model->url_finished = $request->url_finished;



            if ($request->hasFile('file')) {

                $request->file('file')->store('public/files');
                // ensure every image has a different name
                $path = $request->file('file')->hashName();
                $model->file_url = $path;
            }

            $model->save();

            $this->sendNotification($model);

            $version = new \App\TasksVersion;
            $this->saveVersion($model);
        }

        //return redirect('/tasks/'.$id);
        return back();
    }


    public function deleteFile(Request $request, $id)
    {
        //
        //
        $model = Task::find($id);

        $model->file_url = null;

        $model->save();

        $version = new \App\TasksVersion;
        $this->saveVersion($model);

        return redirect('/tasks/' . $id);
    }


    public function saveVersion(Task $task)
    {
        $model = new \App\TasksVersion;

        $model->task_id = $task->id;
        $model->name = $task->name;
        $model->points = $task->points;
        $model->description = $task->description;
        $model->project_id = $task->project_id;
        $model->user_id = $task->user_id;
        $model->status_id = $task->status_id;
        $model->parent_id = $task->parent_id;
        $model->delivery_date = $task->delivery_date;
        $model->due_date = $task->due_date;
        $model->priority = $task->priority;
        $model->created_at = $task->created_at;
        $model->updated_at = $task->updated_at;
        $model->updator_user_id = \Auth::id();
        $model->lead_time = $task->lead_time;
        $model->creator_user_id = $task->creator_user_id;
        $model->file_url = $task->file_url;
        $model->started_at = $task->started_at;
        $model->color = $task->color;
        $model->process_time = $task->process_time;
        $model->observer_id = $task->observer_id;
        $model->value_generated = $task->value_generated;
        $model->url_finished = $task->url_finished;
        $model->sub_type_id = $task->sub_type_id;
        $model->copy = $task->copy;
        $model->caption = $task->caption;
        $model->referrer = $task->referrer;
        $model->estimated_points = $task->estimated_points;

        $model->save();
    }


    public function updateStatusMini(Request $request, $id)
    {
        $model = Task::find($id);

        $version = new \App\TasksVersion;
        $this->saveVersion($model);

        $model->status_id = $request->status_id;
        $model->save();

        $this->sendNotification($model);
    }

    public function updateNextStatusMini(Request $request, $id)
    {
        $model = Task::find($id);

        $version = new \App\TasksVersion;
        $this->saveVersion($model);

        $model->status_id = $nextStatus;
        $model->save();

        $this->sendNotification($model);

        return $nextStatus;
    }


    public function getNextStatus($status_id)
    {
        // Obtener todos los estados ordenados por 'weight'
        $statuses = TaskStatus::orderBy('weight', 'ASC')
            ->where('status_id', 1) // Asegúrate de que este filtro sea correcto según tus necesidades
            ->get();

        $nextStatus = $status_id;
        $found = false;

        foreach ($statuses as $item) {
            if ($found) {
                $nextStatus = $item->id;
                break;
            }
            if ($status_id == $item->id) {
                $found = true;
            }
        }

        // Si llegamos al final y no encontramos el siguiente estado, devolvemos el primer estado
        if (!$found || $nextStatus == $status_id) {
            $nextStatus = $statuses->first()->id;
        }

        return $nextStatus;
    }


    public function updateStatusRest($id, $status, $token)
    {

        if (($status == 3) &&  (Auth::user()->role_id == 1)) {
            $model = Task::find($id);
            $this->saveVersion($model);
            $model->status_id = $status;
            if (($status == 6)) {
                $model->delivery_date = Carbon::now()->toDateTimeString();
            }
            $model->save();

            $this->sendNotification($model);
        } else {
            if (($status != 3)) {
                $model = Task::find($id);
                $this->saveVersion($model);

                if (($status == 6)) {
                    $model->delivery_date = Carbon::now()->toDateTimeString();
                }

                $model->status_id = $status;
                $model->save();

                $this->sendNotification($model);
            } else {
            }
        }
        return $model->status_id;
    }


    public function updateNextStatusRest($id, $status, $token)
    {
        $model = Task::find($id);
        $nextStatus = $this->getNextStatus($model->status_id);
        $status = TaskStatus::find($nextStatus);

        $this->updateStatusRest($id, $nextStatus, $token);
        return $status->id;
    }

    public function updateStatus(Request $request, $id)
    {
        $this->updateStatusMini($request, $id);

        return back()->withInput();
    }


    public function updateUser($tid, $uid, $token)
    {
        $model = Task::find($tid);
        $this->saveVersion($model);
        $model->user_id = $uid;
        $model->save();


        $user = User::find($uid);
        $str = "user.png";
        if (isset($user->image_url))
            $str = $user->image_url;
        return $str;
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

    public function getProject_nameAttribute()
    {

        return $this->id . "j";
    }
    public function search(Request $request)
    {
        $model = Task::all();
        return view('tasks.index', compact('model'));
    }
    public function searchOld(Request $request)
    {
        $projects = Project::orderBy('weight')
            ->join('project_users', 'projects.id', '=', 'project_users.project_id')
            ->selectRaw('project_users.id as project_users_id, projects.id as id, projects.name as name')
            ->where('project_users.user_id', \Auth::id())
            ->get();
        $projects_id = array();
        foreach ($projects as $p) {
            $projects_id[] = $p->id;
        }

        $tasks  =  Task::search($request->search)->get();


        // filtro con proyectos

        //$task_status_options_JSON = Metadata::getTaskStatusOptions();
        //$task_status_options = json_decode()->all();($task_status_options_JSON["value"], true);
        $task_status = TaskStatus::all();

        $users = User::orderBy('name')->get();
        $projects = Project::orderBy('name')
            ->join('project_users', 'projects.id', '=', 'project_users.project_id')
            ->selectRaw('project_users.id as project_users_id, projects.id as id, projects.name as name')
            ->where('project_users.user_id', \Auth::id())
            ->get();



        //$tasks;
        foreach ($tasks as $model) {
            if (isset($model->project_id))
                $model->project_name = Project::find($model->project_id)->name;
            if (isset($model->user_id))
                $model->user_name = User::find($model->user_id)->name;
            // if(isset($model->status_id))
            //   $model->status_name = Metadata::getTaskStatusOptionsById($model->status_id);
            if (isset($model->status_id) && ($model->status_id <> "")) {

                $taskStatus = TaskStatus::find($model->status_id);
                if (isset($taskStatus))
                    $model->task_status = $taskStatus->name;
                else
                    $model->task_status = "";
                //var_dump($model);


            }
        }
        $model = $tasks;
        // $status_task = Task::find($id);

        return view('tasks.index', compact('model', 'users', 'projects', 'task_status', 'request'));
    }



    public function sendNotification($model)
    {
        $roles = array();
        $taskStatus = TaskStatus::find($model->status_id);

        //  Planned, started, finished, cancel, suspended, onHold
        switch ($model->status_id) {
            case 1:
                $roles[] = 2;
                break;
            case 2:
                $roles[] = 3;
                break;
            case 3:
                $roles[] = 3;
                break;
            case 4:
                $roles[] = 2;
                $roles[] = 3;
                break;
            case 5:
                $roles[] = 2;
                $roles[] = 3;
                break;
            case 6:
                $roles[] = 2;
                $roles[] = 3;
                break;

            default:
                # code...
                break;
        }
        //dd($roles);
        // busco los usuarios que debo notificar
        $users = User::orderBy('name')
            ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->join('project_users', 'users.id', '=', 'project_users.user_id')

            ->selectRaw('users.id as user_id, users.name as user_name, users.email')
            ->where('project_users.project_id', $model->project_id)
            ->whereIn('users_roles.role_id', $roles)
            ->get();

        //   dd($users);
        foreach ($users as $user) {


            $msg = substr($model->name, 0, 15) . " la cambió " . Auth::user()->getShortName(8) . " a " . $taskStatus->name;

            /*
            
            Mail::raw($msg, function ($message) use ($user, $taskStatus, $msg){
                $message->from('create@myseo.com.co', 'CREATE MySEO');

                $message->to($user->email, Auth::user()->getShortName(8))->subject($msg);
            });

            */
        }
    }
    public function observe(Request $request, $id, $uid, $token)
    {
        $model = Task::find($id);
        $display = "false";


        $version = new \App\TasksVersion;
        $this->saveVersion($model);


        if ($uid == $model->observer_id) {
            $model->observer_id = null;
            $display = "none";
        } else {
            $model->observer_id = $uid;
            $display = "inline";
        }

        $model->save();



        return $display;
    }

    public function setParent($child, $parent)
    {


        $child = Task::where('id', $child)->first();
        if ($parent == -1)
            $child->parent_id = null;
        else {
            $parent_parent  = Task::where('id', $parent)->first()->parent_id;
            if ($parent_parent == null)
                $child->parent_id = $parent;
            else
                $child->parent_id = $parent_parent;
        }
        $child->save();
    }

    public function setStatus($tid, $ts_id)
    {


        $model = Task::find($tid);
        $this->saveVersion($model);

        if ($ts_id == 0)
            $model->status_id = null;
        else
            $model->status_id = $ts_id;

        $model->save();

        //$model = TaskType::where('parent_id', $ts_id)->get();

        //dd($model->status->background_color);

        return $model->status_id;
    }

    public function setType($tid, $tyid)
    {


        $model = Task::find($tid);

        $model->type_id = $tyid;

        if ($tyid == 0)
            $model->type_id = null;

        $model->save();

        $model = TaskType::where('parent_id', $tyid)->get();

        return $model->toJson();
    }

    public function setSubType($tid, $tyid)
    {
        $model = Task::find($tid);
        $model->sub_type_id = $tyid;
        if ($tyid == 0)
            $model->sub_type_id = null;
        $model->save();
    }

    public function setUser($tid, $uid)
    {
        $model = Task::find($tid);
        $this->saveVersion($model);
        $model->user_id = $uid;
        $model->save();


        $user = User::find($uid);
        $str = "user.png";
        if (isset($user->image_url))
            $str = $user->image_url;
        return $str;
    }

    public function getType($tid)
    {
        $model = TaskType::where('parent_id', $tid)->get();
        return $model->toJson();
    }





    public function updateDate($id, $status, $token)
    {
        $model = Task::find($id);

        if ($status == 1 || $status == 2 || $status == 8) {
            $model->due_date = Carbon::now();
            $model->save();
        }
        return response()->json(['response' => date('M-d', strtotime($model->due_date))]);
    }

    public function updateDueDate($id, $status, $token)
    {
        $model = Task::find($id);

        if ($status == 1 || $status == 2 || $status == 8) {
            $model->due_date = Carbon::now()->addDays(8);
            $model->save();
        }
        return response()->json(['response' => date('M-d', strtotime($model->due_date))]);
    }

    //taskmessages metodo get
    /*  public function sendMessage($task_id,$user_id,$description){
        $model = new TaskMessage;
        $model->user_id = $user_id;
        $model->task_id = $task_id;
        $model->description = $description;
        if($model->save()){

            //GUARGAR NOTIFICACIÓN
            $notification = new Notification;
            
            $task = Task::find($task_id);
            $user = User::find($user_id);
            $notification->sender_id = $user->id;
            $notification->recipient_id = $task->user_id;
            $notification->task_id = $task_id;
            $notification->body = $user->name." Hizo un comentario en la tarea: ".$task->name;
            $notification->save();

            return $model->toJson();
        }

        return "error";
        
    }*/

    //metodo post
    public function sendMessage(Request $request)
    {
        //dd($request->user_id['id']);
        $model = new TaskMessage;
        $model->user_id = $request->user_id['id'];
        $model->task_id = $request->task_id;
        $model->description = $request->description;

        if ($model->save()) {

            //GUARGAR NOTIFICACIÓN
            $notification = new Notification;

            $task = Task::find($request->task_id);
            $user = User::find($request->user_id['id']);
            $notification->sender_id = $user->id;
            $notification->recipient_id = $task->user_id;
            $notification->task_id = $request->task_id;
            $notification->body = $user->name . " Hizo un comentario en la tarea: " . $task->name;
            $notification->save();

            return $model->toJson();
        }

        return "error";
    }

    public function editCommentary(Request $request)
    {
        $model = TaskMessage::find($request->task_message_id);
        $model->description = $request->description;
        if ($model->save()) {
            return $model->toJson();
        }
        return "error";
    }



    public function getMessages($tid)
    {

        $model = TaskMessage::where('task_id', $tid)
            ->join('users', 'users.id', 'task_messages.user_id')
            ->selectRaw('users.*, task_messages.id as task_message_id, task_messages.description, task_messages.created_at as task_message_created_at')
            ->get();

        return $model;
    }


    public function pieces(Request $request)
    {
        $users = User::where('status_id', 1)->get(); //Active
        $task_status = TaskStatus::where('status_id', 1)->orderBy('weight', 'asc')->get();
        //$task_status = TaskStatus::orderBy('weight','asc')->get();

        /*
        where(
            function ($query) use ($request){
                if(isset($request->status_id) && ($request->status_id != null)){
                    $query->where('status_id', "=", $request->status_id);
                }else{
                    //$task_status = TaskStatus::orderBy('weight','asc')->get();
                    $task_status = TaskStatus::where('status_id',1)->orderBy('weight','asc')->get();
                    $task_status_id = array();
                    foreach($task_status as $ts){
                        $task_status_id[] = $ts->id;
                    }
                    $query->whereIn('status_id', $task_status_id);
                }
            })
        -> 
        */

        //dd( isset($request->status_id)&&($request->status_id!=""));

        $tasks = Task::where('project_id', 114)

            ->whereBetween('id', [44120, 44170])
            ->where(
                function ($query) use ($request) {
                    if (isset($request->status_id) && ($request->status_id != "")) {
                        $query->where('tasks.status_id',  $request->status_id);
                    }
                    $query->where('type_id', 42);
                }
            )
            ->orderBy('name', 'ASC')
            ->get();
        // dd($tasks);

        $statuses_id = array();
        foreach ($tasks as $task) {
            $statuses_id[] = $task->status_id;
        }
        $statuses_id = array_unique($statuses_id);
        $tasksGroup = $this->getTaskGroupByStatusFromPieces($request, $statuses_id);

        return view('tasks.pieces.index', compact('tasks', 'users', 'task_status', 'request', 'tasksGroup'));
    }

    public function piecesOnlyShow(Request $request)
    {
        $users = User::where('status_id', 1)->get(); //Active
        //$task_status = TaskStatus::orderBy('weight','asc')->get();
        $task_status = TaskStatus::where('status_id', 1)->orderBy('weight', 'asc')->get();
        $tasks = Task::where(
            function ($query) use ($request) {
                if (isset($request->status_id) && ($request->status_id != null)) {
                    $query->where('status_id', "=", $request->status_id);
                } else {
                    //$task_status = TaskStatus::orderBy('weight','asc')->get();
                    $task_status = TaskStatus::where('status_id', 1)->orderBy('weight', 'asc')->get();
                    $task_status_id = array();
                    foreach ($task_status as $ts) {
                        $task_status_id[] = $ts->id;
                    }
                    $query->whereIn('status_id', $task_status_id);
                }
            }
        )
            ->where('project_id', 114)
            ->where('type_id', 42)
            ->orderBy('name', 'ASC')
            ->get();

        $statuses_id = array();
        foreach ($tasks as $task) {
            $statuses_id[] = $task->status_id;
        }
        $statuses_id = array_unique($statuses_id);
        $tasksGroup = $this->getTaskGroupByStatusFromPieces($request, $statuses_id);

        return view('tasks.pieces.index_show', compact('tasks', 'users', 'task_status', 'request', 'tasksGroup'));
    }

    public function getTaskGroupByStatusFromPieces($request, $statuses_id)
    {


        $model = Task::join('task_statuses', 'task_statuses.id', 'tasks.status_id')
            ->where('project_id', 114)

            ->whereBetween('tasks.id', [44120, 44170])
            ->where(
                function ($query) use ($request) {
                    if (isset($request->status_id) && ($request->status_id != "")) {
                        $query->where('tasks.status_id',  $request->status_id);
                    }
                    $query->where('type_id', 42);
                }
            )
            ->select(DB::raw('tasks.status_id, sum(points) as sum_points, count(tasks.id) as count_points'))
            ->groupBy('weight', 'tasks.status_id')
            ->orderby('weight')
            ->get();
        return $model;
    }

    public function piecesShow($id)
    {
        if (Auth::user()) {
            $task = Task::find($id);
            return view('tasks.pieces.show', compact('task'));
        }
    }

    public function setPieceUser($u, $t)
    {
        $task = Task::find($t);
        if ($task) {
            $task->user_id = $u;
            $task->save();
            return $task;
        } else {
            return null;
        }
    }

    public function setPieceStatus($s, $t)
    {
        $task = Task::find($t);
        if ($task) {
            $task->status_id = $s;
            $task->save();
            return $task->status->background_color;
        } else {
            return null;
        }
    }

    public function setDescription(Request $request)
    {
        $tid = $request->task_id;
        $attribute = $request->attribute;
        $value = $request->value;
        $this->updateTaskAttribute($tid, $attribute, $value);
    }
    public function setCaption(Request $request)
    {
        $tid = $request->task_id;
        $attribute = $request->attribute;
        $value = $request->value;
        $this->updateTaskAttribute($tid, $attribute, $value);
    }
    public function setCopy(Request $request)
    {
        $tid = $request->task_id;
        $attribute = $request->attribute;
        $value = $request->value;
        $this->updateTaskAttribute($tid, $attribute, $value);
    }
    public function setUrlFinished(Request $request)
    {
        $tid = $request->task_id;
        $attribute = $request->attribute;
        $value = $request->value;
        $this->updateTaskAttribute($tid, $attribute, $value);
    }

    public function updateTaskAttribute($tid, $attribute, $value)
    {
        $task = Task::find($tid);
        if ($task) {
            $task->$attribute = $value;
            $task->save();
            return $task;
        } else {
            return null;
        }
    }


    public function setTaskUser($tid, $uid)
    {
        $model = TaskUser::where('user_id', $uid)
            ->where('task_id', $tid)
            ->first();
        if (!$model) {
            $model = new TaskUser;
            $model->user_id = $uid;
            $model->task_id = $tid;
            $model->save();
        }
        return $tid;
    }

    public function getNotifications($uid)
    {
        $notification = Notification::where('recipient_id', $uid)
            ->whereNull('reviewed')
            ->count();
        return $notification;
    }

    public function setNotificationReviewed($nid)
    {
        $notification = Notification::find($nid);
        $notification->reviewed = 1;
        $notification->save();
        return $notification;
    }

    public function getAllNotifications()
    {
        $notifications = Notification::where('recipient_id', Auth::id())
            ->whereNull('reviewed')
            ->get();
        return $notifications->toJson();
    }




    public function approveTask($task_id)
    {
        $model = Task::find($task_id);
        $model->status_id = 62; //Traffic
        if ($model->save()) {
            return $model->toJson();
        }
        return "error";
    }

    public function rejectTask($task_id)
    {
        $model = Task::find($task_id);
        $model->status_id = 2; //Service
        if ($model->save()) {
            return $model->toJson();
        }
        return "error";
    }


    public function chargeAccount(Request $request)
    {
        $this->getIndex($request, $model, $users, $projects, $task_status, $statuses_id, $total_points, $tasksGroup, $days, $task_types, $task_subtypes);
        $task_types_component = TaskType::whereNull('parent_id')->get();


        $weeks = 1;

        $month_start = strtotime('monday this week');
        $from_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d', $month_start));

        $month_end = strtotime('monday next week');
        $to_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d', $month_end));


        $weeks = $from_date->diffInDays($to_date);
        if ($weeks == 0) {
            $weeks = 1;
        }
        //dd($weeks);
        $weeks_array = array();
        for ($i = 0; $i < $weeks; $i++) {
            $weeks_array[] =
                array($from_date->format('Y-m-d') . " 00:00:00", $from_date->format('Y-m-d') . " 23:59:59");
            $from_date->addDays(1)->format('Y-m-d');
        }
        //dd($weeks_array);
        $status_array = array(56);



        $user_data_with_points = array();

        //Obtener id de los usuarios con puntos
        for ($i = 0; $i < $weeks; $i++) {
            foreach ($users as $user) {
                $task_points = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->first();
                if ($task_points) {
                    $user_data_with_points[] = $task_points->user_id;
                }
            }
        }
        //obtener usuarios
        $users_graph = User::orderBy('name')
            ->where('users.status_id', 1)
            ->whereIn('id', array_unique($user_data_with_points))
            ->get();


        for ($i = 0; $i < $weeks; $i++) {
            $user_data = array();
            foreach ($users_graph as $user) {
                $tasks = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->sum('points');
                $user_data[] = $tasks;

                $task_points = DB::table('tasks')
                    ->whereIn('status_id', $status_array)
                    ->whereBetween('due_date', $weeks_array[$i])
                    ->where('user_id', $user->id)
                    ->first();
                if ($task_points) {
                    $user_data_with_points[] = $task_points->user_id;
                }
            }
            $data[] = $user_data;
        }

        $user = User::find($request->user_id);

        return view('tasks.charge_account', compact('model', 'users', 'projects', 'task_status', 'request', 'statuses_id', 'total_points', 'tasksGroup', 'days', 'task_types', 'task_types_component', 'task_subtypes', 'weeks_array', 'weeks', 'data', 'users_graph', 'user'));
    }



    /// importador
    public function import()
    {
        $statuses = TaskStatus::all();
        $users = User::where('status_id', 1)->get(); //Active
        $projects = Project::where('status_id', 3)->get(); //Running
        return view('tasks.import', compact('statuses', 'users', 'projects'));
    }

    public function bulkStore(Request $request)
    {
        $data = $this->uploadFile($request);
        foreach ($data as $array)
            $this->createFromArray($array);
        return redirect('/tasks/');
    }

    public function uploadFile($request)
    {
        $data = "";
        $path = "";
        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $data = array_slice($data, 1);
        }
        return $data;
    }

    public function createFromArray($array)
    {
        $NAME = 0;
        $DESCRIPTION = 1;
        $USER = 2;
        $PROJECT = 3;
        $STATUS = 4;

        $user_id = $this->getIdFromName($array[$USER], 'users');
        $status_id = $this->getIdFromName($array[$STATUS], 'task_statuses');
        $project_id = $this->getIdFromName($array[$PROJECT], 'projects');

        $model = new Task;
        if (isset($array[$NAME]))
            $model->name = $array[$NAME];
        if (isset($array[$DESCRIPTION]))
            $model->description = $array[$DESCRIPTION];

        if (intval($user_id))
            $model->user_id = $user_id;
        if (intval($status_id))
            $model->status_id = $status_id;
        if (intval($project_id))
            $model->project_id = $project_id;
        $model->save();
    }


    public function getIdFromName($name, $table)
    {
        $id = 0;
        $model = DB::table($table)->where('name', '=', $name)->first();
        if ($model)
            $id = $model->id;
        else {
            echo "<h1>" . $name . " " . $table . "</h1>";
        }
        return $id;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAJAX(Request $request)
    {
        //
        //
        dd($request);
        $model = Task::find($request->task_id);

        $model->name = $request->name;
        $model->points = $request->points;
        $model->description = $request->description;
        $model->project_id = $request->project_id;
        $model->user_id = $request->user_id;
        $model->priority = $request->priority;

        $model->caption = $request->caption;
        $model->copy = $request->copy;


        if (isset($request->parent_id))
            $model->parent_id = $request->parent_id;


        if (isset($request->type_id)) {
            $model->type_id = $request->type_id;
        }
        if (isset($request->sub_type_id)) {
            $model->sub_type_id = $request->sub_type_id;
        }


        $model->updator_user_id = Auth::id();

        if (($request->status_id == 3)) {

            if ((Auth::user()->role_id == 1))
                $model->status_id = $request->status_id;
        } else {
            $model->status_id = $request->status_id;
        }

        $model->due_date = $request->due_date;
        $model->delivery_date = $request->delivery_date;
        if (isset($request->value_generated)) {
            $model->value_generated = true;
        } else {
            $model->value_generated = false;
        }


        $model->url_finished = $request->url_finished;


        if ($request->hasFile('file')) {

            $request->file('file')->store('public/files');
            // ensure every image has a different name
            $path = $request->file('file')->hashName();
            $model->file_url = $path;
        }

        $model->save();

        $this->sendNotification($model);

        $version = new \App\TasksVersion;
        $this->saveVersion($model);

        return redirect('/tasks/' . $id);
    }

    public function updateValueGenerated($taskId, $value_generated)
{
    // Encuentra la tarea
    $task = Task::findOrFail($taskId);

    // Actualiza el valor de `value_generated`
    $task->value_generated = $value_generated;
    $task->save();

    // Responde con el nuevo valor generado
    return response()->json([
        'message' => 'Valor generado actualizado correctamente',
        'value_generated' => $task->value_generated
    ]);
}


}
