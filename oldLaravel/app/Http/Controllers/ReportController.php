<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Khill\Lavacharts\Lavacharts;
use DB;
use App\Task;

use App\User;
use App\Project;
use App\ProjectStatus;
use App\TaskStatus;
use App\Action;
use Carbon\Carbon;
use App\TaskReportWeeksUser;



class ReportController extends Controller{
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    function getStartAndEndDate($week, $year)
    {

        $time = strtotime("1 January $year", time());
        $day = date('w', $time);
        $time += ((7*$week)-$day)*24*3600;
        $return[0] = date('Y-m-d', $time);
        $time += 6*24*3600;
        $return[1] = date('Y-m-d', $time);
       
        return $return;
    }
    public function index2()
    {
        ///, week(updated_at) as week
        $tasks = \DB::table('tasks')
                     ->select(DB::raw('week(due_date) as week ,  count(*) as pr'))
                     ->where('status_id', '<>', 2)
                     ->groupBy('week')
                     ->get();


        $data = \Lava::DataTable();


        $data
            ->addDateColumn('Year')
            ->addNumberColumn('Points');
        
        
        foreach($tasks as $item){
           $data->addRow([
                $this->getStartAndEndDate($item->week, 2017)[0], intval($item->pr)
            ]);
        }
                   
                   

        \Lava::AreaChart('data', $data, [
            'title' => 'Data Growth',
            'legend' => [
                'position' => 'in'
            ]
        ]);


        return view('reports.index');
    }

    public function index(){


            $model = \DB::table('tasks')
                     ->select(DB::raw('year(due_date) as year, week(due_date) as week ,  sum(points) as sum_points'))
                     ->where('status_id', '=', 3)
                     ->groupBy('year', 'week')
                     ->get();
            $users = Task::
                     select(DB::raw('user_id, year(due_date) as year, week(due_date) as week ,  sum(points) as sum_points'))
                     ->where('status_id', '=', 3)
                     ->groupBy('year', 'week','user_id')
                     ->get();
        foreach ($users as $item){
            
            $item->name = User::getName($item->user_id);
        }

        return view('reports.index', compact('model','users'));
    
    }


    public function weeksByTeam(Request $request){
        
        

        $users = User::where('status_id', '=', 1)
                    ->whereIn('role_id', [1,2 ])
                    ->where(function($query) use ($request){
                        if(isset($request->user_id) && $request->user_id != null){
                            $query->where('id', $request->user_id);
                        }else{
                            $all_users = User::select('id')->where('status_id', '=', 1)
                            ->whereIn('role_id', [1,2 ])
                            ->get();
                            $users_id = array();
                            foreach ($all_users as $value) {
                                $users_id[] = $value->id;
                            }
                            $query->whereIn('id', $users_id);
                        }
                    })
                    ->get();



        $weeks = 1;
        $to_date = Carbon::today()->subDays(0); // ayer
        $from_date = Carbon::today()->subDays(7*4);

        if(isset($request->from_date) && ($request->from_date!=null)){
            $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date);
            $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date);
        }

        $weeks = $from_date->diffInWeeks($to_date);
        
        $weeks_array = array();
        
        for( $i=0; $i<$weeks; $i++ ){
            $weeks_array[] = 
                Array($from_date->format('Y-m-d'), $from_date->addDays(7)->format('Y-m-d'));
        }

        $status_array = Array(3, 6, 56, 57, 58);
        /*
        $data = array();

        // ->where('user_id', $user->id)
                 
        for($i=0; $i<$weeks; $i++ ){
            $tasks = DB::table('tasks')
                 ->whereIn('status_id', $status_array)
                 ->whereBetween('due_date', $weeks_array[$i])
                 ->where(function($query) use ($request){
                        if(isset($request->user_id) && $request->user_id != null){
                            $query->where('user_id', $request->user_id);
                        }else{
                            $all_users = User::select('id')->where('status_id', '=', 1)
                            ->whereIn('role_id', [1,2 ])
                            ->get();
                            $users_id = array();
                            foreach ($all_users as $value) {
                                $users_id[] = $value->id;
                            }
                            $query->whereIn('user_id', $users_id);
                        }
                    })
                 ->sum('points');
            $data[] = $tasks;
        }
    */
        


        

        $user_data_with_points = array();
        //Obtener id de los usuarios con puntos
        for($i=0; $i<$weeks; $i++ ){
            foreach ($users as $user) {
                $task_points = DB::table('tasks')
                 ->whereIn('status_id', $status_array)
                 ->whereBetween('due_date', $weeks_array[$i])
                 ->where('user_id', $user->id)
                 ->first();
                 if($task_points){
                    $user_data_with_points[] = $task_points->user_id;
                 }
            }
        }
        //obtener usuarios
        $users_graph = User::orderBy('name')
            ->where('users.status_id', 1)
            ->whereIn('id', array_unique($user_data_with_points))
            ->get();


        for($i=0; $i<$weeks; $i++ ){
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
                 if($task_points){
                    $user_data_with_points[] = $task_points->user_id;
                 }
            }
            $data[] = $user_data;
        }


//dd($data);

            
        $controller = $this;

        return view('reports.weeks_by_team', compact( 
            'controller', 'request', 'users', 'weeks' ,'from_date', 'to_date', 'data', 'weeks_array', 'users_graph'));
    
    }

    

    public function weeksByUser(Request $request){
        
        $users = User::select('id')->where('status_id', '=', 1)
                    ->whereIn('role_id', [1,2 ])
                    ->where(function($query) use ($request){
                        $all_users = User::
                            select(DB::raw('id'))
                            ->where('status_id', '=', 1)
                            ->whereIn('role_id', [1,2 ])
                             ->get();
                            $all_users_id = array();
                            foreach ($all_users as $key => $value) {
                                $all_users_id[] = $value["id"];
                            }
                        if(isset($request->user_id) && $request->user_id != null)
                            $query->where('id', $request->user_id);
                        else
                            
                            $query->whereIn('id', $all_users_id);
                    })
                    ->get();


        $to_date = Carbon::today();
        $from_date = Carbon::today()->subDays(8);
        
        if(isset($request->from_date) && ($request->from_date!=null)){
            $to_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->to_date . " 23:59:59");
            $from_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date . "00:00:00");
        }

        $year_from_date = $from_date->format('Y');
        $month_from_date = $from_date->format('m');

        $year_to_date = $to_date->format('Y');
        $month_to_date = $to_date->format('m');

        for ($i=$month_from_date; $i <= $month_to_date ; $i++) { 
            if($i == $month_from_date){
                $weeks_array = $this->getWeeksByMonths($i, $year_to_date);
            }else{
                $month_next = $this->getWeeksByMonths($i, $year_to_date);
                $weeks_array_last = sizeof($weeks_array);  
                foreach ($month_next as $key => $value) {
                    $weeks_array[$weeks_array_last + $key] =  array('start'=> $value["start"], 'end' => $value["end"]);
                } 
            }
        }
        
        $weeks_diff = sizeof($weeks_array);
        //dd($weeks_diff);



        $users_graph = User::where('users.status_id', '=', 1)
        ->Join('tasks', 'tasks.user_id', 'users.id')
                    ->whereIn('users.role_id', [1,2 ])
                    
                    ->whereBetween('tasks.due_date',array($from_date->format('Y-m-d'), $to_date->format('Y-m-d')))
                    ->select(DB::raw(' distinct(users.id) , users.name'))
                    ->where(function($query) use ($request){
                        $all_users = User::
                            select(DB::raw('id'))
                            ->where('status_id', '=', 1)
                            ->whereIn('role_id', [1,2 ])
                             ->get();
                            $all_users_id = array();
                            foreach ($all_users as $key => $value) {
                                $all_users_id[] = $value["id"];
                            }
                        if(isset($request->user_id) && $request->user_id != null)
                            $query->where('users.id', $request->user_id);
                        else
                            
                            $query->whereIn('users.id', $all_users_id);
                    })
                     ->get();




        $status_array = Array(3, 6, 56, 57);
        $data = Array();
        $tasks = Task::
                 whereIn('tasks.status_id', $status_array)
                 ->whereBetween('due_date',array($from_date->format('Y-m-d H:i:s'), $to_date->format('Y-m-d H:i:s')))
                 ->select(DB::raw(' week(due_date) as week, year(due_date) as year,  user_id, sum(points) as points'))
                 ->whereIn('user_id', $users->toArray())
                 ->leftJoin('users', 'users.id', 'tasks.user_id')
                 ->groupBy('year','week','user_id')
                 ->orderBy('week','ASC')
                 ->get();

        
        $week_first = ($tasks[0]->week-1);/*Obtener primera semana*/
        
        $week_size = sizeof($tasks)-1;/*Obtener cantidad de tareas*/
       
        $week_last = $tasks[$week_size]->week; /*Obtener ultima semana*/
        
        $weeks_diff = ($week_last - $week_first);/*Obtener la cantidad de semanas*/

        //dd($weeks_diff);

        $data = Array();
        foreach($users_graph as $user){
            $user_data = array();
            for ($i=0; $i < sizeof($tasks) ; $i++) {
                if($tasks[$i]->user_id == $user->id){
                    $user_data[] = $tasks[$i]->points;
                }
            }
            $data[] = $user_data;
        }

        $controller = $this;
        return view('reports.weeks_by_user', compact( 
            'controller', 'request', 'users', 'users_graph', 'weeks_diff' ,'from_date', 'to_date', 'data', 'weeks_array'));
    }


    function getWeeksByMonths($month,$year){       
        $day_last = date("d",mktime(0,0,0,$month+1,0,$year));
        $weeks = array();
        $weeks_count = 0;
        $start = 1;
        $end = 0;
        $week_day = '';
        for($i = 1;$i<=$day_last;$i++){
            $date = mktime(0,0,0,$month,$i,$year);
            $week_day = date('w',($date));
            if($week_day == 0){
                $weeks[$weeks_count] = array('start' => $start."/".$month."/".$year,'end'=>$i."/".$month."/".$year);
                $start = $i+1;
                $weeks_count++;
            }
        }
        $week_last = end($weeks);
        if($week_last['end'] != $day_last){
            $weeks[$weeks_count] = array('start' => $start."/".$month."/".$year,'end' => $day_last."/".$month."/".$year);
        }
        return $weeks;
    }



    public function getMonths($from, $to){

    	$from_month = $from->format('m');
    	$to_month = $to->format('m');

		$from_year = $from->format('Y');
    	$to_year = $to->format('Y');

//dd($from_year);
    	$time_span_array = array();
        
        for( $i=$from_year; $i<=$to_year; $i++ ){
        	for( $j=$from_month; $j<=$to_month; $j++ ){
        		if(($i == $from_year) & ($j == $from_month))
        			$start_date = $from;
        		else
        			$start_date = Carbon::createFromFormat('Y-m-d H:i:s', $i.'-'.$j.'-01 00:00:00');

        		if(($i == $to_year) & ($j == $to_month))
        			$finish_date = $to;
        		else
        			$finish_date = Carbon::createFromFormat('Y-m-d H:i:s', $i.'-'.($j+1).'-01 00:00:00');

            	$time_span_array[] = Array($start_date, $finish_date);
        	}
        }

    	//dd($time_span_array);
    	return $time_span_array;

    }

    public function monthsByUser(Request $request){

        
        

        

        // obtengo los usuarios activos
        $users = User::where('status_id', '=', 1)
                    ->whereIn('role_id', [1,2 ])
                    ->where(function($query) use ($request){
                        $all_users = User::
                            select(DB::raw('id'))
                            ->where('status_id', '=', 1)
                            ->whereIn('role_id', [1,2 ])
                             ->get();
                            $all_users_id = array();
                            foreach ($all_users as $key => $value) {
                                $all_users_id[] = $value["id"];
                            }
                        if(isset($request->user_id))
                            $query->where('id', $request->user_id);
                        else
                            $query->whereIn('id', $all_users_id);
                    })
                     ->get();


        $time_span = 1;
        //$to_date = Carbon::today()->subDays(0); // ayer
        //$from_date = Carbon::today()->subMonths(1);

        $to_date = Carbon::now()->endOfMonth();
        $from_date = Carbon::now()->startOfMonth();


        if(isset($request->from_date) && ($request->from_date!=null)){
            $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->addDays(1);
            $from_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date." 00:00:00");
        }


    	
        $time_span = $from_date->diffInMonths($to_date);
        if($time_span < 1){
            $time_span = 1;
        }
        
        
        $time_span_array = $this->getMonths($from_date, $to_date);

        $status_array = Array(3, 6, 56, 57);
        $data = array();

        
        foreach ($users as $user){
            $user_data = array();

            for($i=0; $i<$time_span; $i++ ){
                $tasks = DB::table('tasks')
                     ->whereIn('status_id', $status_array)
                     ->whereBetween('due_date', $time_span_array[$i])

                     ->where('user_id', $user->id)
                     ->sum('points');
                $user_data[] = $tasks;
            }
            $data[] = $user_data;

        }

        //dd($time_span);
        
            
        $controller = $this;

        return view('reports.months_by_user', compact( 
            'controller', 'request', 'users', 'time_span' ,'from_date', 'to_date', 'data', 'time_span_array'));
    
    }


    public function monthsByProject(Request $request){
        $projects = Project::where("status_id", "=", "3")
                ->where(function($query)use($request){
                    $all_projects = Project::
                            select(DB::raw('id'))
                            ->where('status_id', '=', 3)
                             ->get();
                            $all_projects_id = array();
                            foreach ($all_projects as $key => $value) {
                                $all_projects_id[] = $value["id"];
                            }
                    if(isset($request->project_id))
                        $query->where('id', $request->project_id);
                    else
                        $query->whereIn('id', $all_projects_id);
                 })
                ->orderBy("weight", "asc")
                ->get();



        $time_span = 1;
        //$to_date = Carbon::today()->subDays(0); // ayer
        //$from_date = Carbon::today()->subMonths(1);

        $to_date = Carbon::now()->endOfMonth();
        $from_date = Carbon::now()->startOfMonth();


        if(isset($request->from_date) && ($request->from_date!=null)){
            $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->addDays(1);
            $from_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date." 00:00:00");
        }

        $time_span = $from_date->diffInMonths($to_date);
        if($time_span < 1){
            $time_span = 1;
        }
        
        
        $time_span_array = $this->getMonths($from_date, $to_date);
        //dd($time_span_array);
        $status_array = Array(3, 6, 56, 57);
        if(isset($request->status_id)){
            $status_array = Array($request->status_id);
        }
        
        $statuses = TaskStatus::whereIn('id',$status_array)->get();
        
       
        $data = array();

        
        foreach ($projects as $project){
            
            $project_data = array();

            for($i=0; $i<$time_span; $i++ ){
                $tasks = DB::table('tasks')
                     ->whereIn('status_id', $status_array)
                     ->whereBetween('due_date', $time_span_array[$i])

                     ->where('project_id', $project->id)
                     ->sum('points');
                    
                $project_data[] = $tasks;
               
            }
//error
            $data[] = $project_data;
           

        }

        //dd($time_span);
        
            
        $controller = $this;

        return view('reports.months_by_project', 
            compact('controller', 'request', 'projects', 'time_span' ,'from_date', 'to_date', 'data', 'time_span_array','statuses'));
    
    }


    function getStatusID($request){


        $url = $request->fullurl();
        $paramenters = explode("&", $url);
        $res = array();
        foreach($paramenters as $key=>$value)
        {
            if(strpos($value, "status_id")!==false && (str_replace("status_id=", "", $value)!=0)){
                $res[] = str_replace("status_id=", "", $value);
            }
        }

        if(!count($res)){
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

      
        return $res;
        
    }

    public function daysByUser(Request $request){
        // obtengo los usuarios activos
        $users = User::where('status_id', '=', 1)
                    ->whereIn('role_id', [1,2])
                    ->where(function($query)use($request){
                        if(isset($request->user_id) && $request->user_id != null){
                            $query->where('id', $request->user_id);
                        }else{
                            $all_users = User::
                            select(DB::raw('id'))
                            ->where('status_id', '=', 1)
                            ->whereIn('role_id', [1,2 ])
                             ->get();
                            $all_users_id = array();
                            foreach ($all_users as $key => $value) {
                                $all_users_id[] = $value["id"];
                            }
                            $query->whereIn('id', $all_users_id);
                        }
                    })
                     ->get();
        $days = 1;
        $to_date = Carbon::today(); // ayer
        $from_date = Carbon::today()->subDays(1);


        if(isset($request->from_date) && ($request->from_date!=null)){
            $from_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date." 00:00:00");
            $to_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->to_date." 23:59:59");
            
        }

        
        $days = $from_date->diffInDays($to_date);

        if($days < 1){
            $days = 1;
        }
        
        $days_array = array();
        
        for( $i=0; $i<$days; $i++ ){
            $from = $from_date;
            //$from->addDays(1);    

            $days_array[] = Array(
                $from->format('Y-m-d'), 
                $from->addHours(23)->addMinutes(59)->addSeconds(59)->format('Y-m-d H:i:s'));
        }
        $status_array = Array(3, 6, 56, 57);
        $data = array();

        $task_statuses = TaskStatus::orderBy('weight' , 'asc')->get();
        $statuses_id = $this->getStatusID($request);


        foreach ($users as $user){
            $user_data = array();
            for($i=0; $i<$days; $i++ ){
                
                $tasks = Task::whereBetween('due_date', $days_array[$i])
                     ->where('user_id', $user->id)
                     ->where(function($query)use ($statuses_id){
                        if(sizeof($statuses_id)){
                            $query->where(function ($query) use ($statuses_id){
                                foreach($statuses_id as $key=>$value){
                                    $query->orwhere('tasks.status_id', "=", $value);
                            }});
                        }else{
                            $query->where('tasks.status_id', "=", 1);   
                        }
                     })
                     ->sum('points');

                $user_data[] = $tasks;
                
                $tasks = Task::whereBetween('due_date', $days_array[$i])
                     ->where('user_id', $user->id)
                     ->where('status_id',1)
                     ->sum('points');
                $user_data[] = $tasks;
                            }
            $data[] = $user_data;
        }

        /*Eliminar tareas que no tienen puntos del array data*/
        $users_without_points = array();
        
        for($i=0; $i < sizeof($data); $i++){
            $new_data = array();

            if(array_sum($data[$i]) > 0){
                for ($j=0; $j < sizeof($data[$i])-1; $j++){
                    
                    $new_data[] = $data[$i][$j];
                }
                $array_data[] = $new_data;
            }else{
                $users_without_points[] = $i;
            }
        }
        $data = null;
        $data = $array_data;
        
        /*Eliminar Usuarios que no tienen puntos de la colección de usuarios*/
        foreach ($users_without_points as $value) {
            unset($users[$value]);
        }

        /*Volver a guardar usuarios en un array, para reiniciar index*/
        foreach ($users as $value) {
            $new_users[] = $value;
        }
        $users = null;
        $users = $new_users;


        $controller = $this;

        return view('reports.days_by_user', compact( 
            'controller', 'request', 'users', 'days' ,'from_date', 'to_date', 'data', 'days_array', 'task_statuses', 'statuses_id'));
    
    }

    //Manuel 2018-10-31
    public function projectsTaskByStatuses(Request $request){
        
        $user_id = 1;
        if(isset($request->user_id)){
            $user_id = $request->user_id;
            
        }
        $projects = Project::where("status_id", "=", "3")
                ->where(function($query)use($request){
                    $all_projects = Project::
                            select(DB::raw('id'))
                            ->where('status_id', '=', 3)
                             ->get();
                            $all_projects_id = array();
                            foreach ($all_projects as $key => $value) {
                                $all_projects_id[] = $value["id"];
                            }
                    if(isset($request->project_id)&& $request->project_id != null)
                        $query->where('id', $request->project_id);
                    else
                        $query->whereIn('id', $all_projects_id);
                 })
                ->orderBy("weight", "asc")
                ->get();
        
        $task_statuses = TaskStatus::where('status_id', 1)
            ->orderBy('weight' , 'asc')->get();
        $users = User::where("status_id", "=", "1")->get();
        $model = Task::
                     select(DB::raw('user_id, year(due_date) as year, week(due_date) as week ,  sum(points) as sum_points'))
                     ->where('status_id', '=', 3)
                     ->where(function($query)use($request){
                        $all_users = User::
                            select(DB::raw('id'))
                            ->where('status_id', '=', 1)
                            ->whereIn('role_id', [1,2 ])
                             ->get();
                            $all_users_id = array();
                            foreach ($all_users as $key => $value) {
                                $all_users_id[] = $value["id"];
                            }

                        if(isset($request->user_id) && $request->user_id != null)
                            $query->where('user_id', $request->user_id);
                        else
                            $query->whereIn('user_id', $all_users_id);
                     })
                     ->groupBy('year', 'week','user_id')
                     ->orderBy('year', 'desc')
                     ->orderBy('week', 'desc')
                    ->get();
      

        foreach ($model as $item){
            
            $item->name = User::getName($item->user_id);
        }
        
        $graph = $model;
        
        $controller = $this;
       
        return view('reports.projects', compact('model','request', 'graph','controller','users','projects','task_statuses'));
    
    }



    

    

/**
 * Calcula los días laborables entre dos fechas.
 *
 * @param string $startDate La fecha de inicio en formato 'Y-m-d'.
 * @param string $endDate La fecha de fin en formato 'Y-m-d'.
 * @return int La cantidad de días laborables entre las dos fechas.
 */
function calculateWorkingDaysBetweenDates($startDate, $endDate){
    $start = Carbon::createFromFormat('Y-m-d', $startDate);
    $end = Carbon::createFromFormat('Y-m-d', $endDate);
    $end = $end->addDay();  // Incluye el día de finalización en la iteración
    
    $workingDays = 0;
    
    for ($date = $start; $date->lte($end); $date->addDay()) {
        if ($date->isWeekday()) {
            $workingDays++;
        }
    }

    // Resta un día si el último día se incluyó y no es un día laborable
    if ($end->isWeekday() === false) {
        $workingDays--;
    }

    return $workingDays;
}


    //Nico 2021-06-02
    public function usersTaskByStatuses(Request $request){

        $user_id = 1;
        if(isset($request->user_id)){
            $user_id = $request->user_id;
            
        }
        $days = 0;
        if(isset($request->from_date)&&($request->from_date!=""))
            $days = $this->calculateWorkingDaysBetweenDates($request->from_date, $request->to_date);
        
        $projects = Project::where("status_id", "=", "3")->orderBy("name", "asc")->get();
        $task_statuses = TaskStatus::where('status_id', "1")->orderBy('weight' , 'asc')->get();
        $users = User::where("status_id", "=", "1")
                ->where(function($query)use($request){
                    $all_users = User::
                            select(DB::raw('id'))
                            ->where('status_id', '=', 1)
                            ->whereIn('role_id', [1,2 ])
                             ->get();
                            $all_users_id = array();
                            foreach ($all_users as $key => $value) {
                                $all_users_id[] = $value["id"];
                            }
                    if(isset($request->user_id) && $request->user_id != null)
                       
                        $query->where('id', $request->user_id);
                    else
                        $query->whereIn('id', $all_users_id);
                 })
                ->get();
        $model = Task::
                     select(DB::raw('user_id, sum(points) as sum_points'))
                     ->where('status_id', '=', 3)
                     ->where(function($query)use($request){
                        $all_users = User::
                            select(DB::raw('id'))
                            ->where('status_id', '=', 1)
                            ->whereIn('role_id', [1,2 ])
                             ->get();
                            $all_users_id = array();
                            foreach ($all_users as $key => $value) {
                                $all_users_id[] = $value["id"];
                            }
                        if(isset($request->user_id) && $request->user_id != null)
                            $query->where('user_id', $request->user_id);
                        else
                            $query->whereIn('user_id', $all_users_id);
                     })
                     ->groupBy('user_id')
                     
                    ->get();
        
        foreach ($model as $item){
            
            $item->name = User::getName($item->user_id);
        }
        $graph = $model;
            
        $controller = $this;

        return view('reports.user_tasks.index', compact('days','model','request', 'graph','controller','projects','users','task_statuses'));
    
    }


    public function taskTime(Request $request){
        // obtengo los usuarios activos
        $dates_array = $this->getDatesMontly($request);



        $task_statuses = Task::distinct()->select('status_id')
            ->whereBetween('created_at', $dates_array)
            ->get();

        $time_array = $this->getTimeArray($dates_array);

        $task_statuses = $this->elocuentToArrayStatus($task_statuses);
        
        $users = $this->getUsersFromDates($dates_array);
 
        $data = array();
        $task_statuses = TaskStatus::where('stage_id',1)->orderBy('weight', 'ASC')->get();
        
        
        foreach ($time_array as $time){
            $user_data = array();

            foreach($task_statuses as $status){
                $model = DB::table('tasks')
                     ->whereBetween('created_at', $time)
                     ->where('status_id', $status->id)
                     ->count('id');
                    $user_data[] = $model;
                    
            }
            
        
            $data[] = $user_data;

        }

            
        $controller = $this;

        return view('reports.tasks_time', compact( 
            'controller', 'request', 'users', 'time_array' ,'from_date', 'to_date', 'data', 'task_statuses'));
    
    }

    function elocuentToArray($model){
        $array = Array();
        foreach ($model as $item) {
            $array[] = $item->type_id;

        }
        return $array;
    }


    function elocuentToArray2($model){
        $array = Array();
        foreach ($model as $item) {
            $array[] = $item->creator_user_id;

        }
        return $array;
    }

    function elocuentToArrayStatus($model){
        $array = Array();
        foreach ($model as $item) {
            $array[] = $item->status_id;

        }
        return $array;
    }

    public function getDatesMontly($request){
        $to_date = Carbon::today()->subMonths(0); // ayer
        $from_date = Carbon::today()->subMonths(3);


        if(isset($request->from_date) && ($request->from_date!=null)){
            $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date);
            $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date);
        }

        $date_array = 
            Array($from_date->format('Y-m-d'), $to_date->addDays(1));
        return $date_array;
    }

    public function getTimeArray($dates_array){

        $from = $dates_array[0];
        $to = $dates_array[1];
        $from  = Carbon::createFromFormat('Y-m-d', $from);
        $to  = Carbon::createFromFormat('Y-m-d H:i:s', $to);
        $span = $from->diffInMonths($to);

        $time_array = array();
        
        for( $i=0; $i<$span; $i++ ){
            $time_array[] = 
                Array($from->format('Y-m-d'), $from->addMonths(1)->format('Y-m-d'));
        }
        return $time_array;       
    }

    public function getUsersFromDates($date_array){
        $users_id = Action::distinct()->select('creator_user_id')
            ->whereBetween('created_at', $date_array)
            ->whereNotNull('creator_user_id')
            ->get();


        $users_id = $this->elocuentToArray2($users_id);


        $users = User::where('status_id', '=', 1)
                    ->whereIn('id', $users_id)
                     ->get();
        return $users;
    }



}
