<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Carbon\Carbon;
use DB;

class Task extends Model
{
  
     public function taskMessage(){
        return $this->belongsTo('App\TaskMessage');
    }

     public function getCountMessages($id){
        $model =TaskMessage::where('task_id',$id)
                            ->count();
        return $model;

    }
     public function getUser($id){
        $model =User::find($id);
        return $model;
    }
  //use Searchable;
    //

    public function getDateInput($date){
    	return date('Y-m-d',strtotime($date));
    }

    public function project(){
    	return $this->belongsTo(Project::class);
    }
    public function user(){
    	return $this->belongsTo(User::class);

    }
    public function status(){
        return $this->belongsTo(TaskStatus::class);
    }
    public function task_status(){
        return $this->belongsTo(TaskStatus::class);
    }

    public function getChildrenPoints(){
        $count = 0;
        if(isset($this->children))
            foreach($this->children as $item)
                if(isset($item->points))
                    $count += $item->points;
        return $count;
    }

    public function nameSubstr($limit){
        
        $str = $this->name;
        
        if (strlen($str)>$limit){ 
            $str = substr($str,0, $limit)."...";
            //$str = "no cabe ". strlen($str) . "-". $len; 
        }else{
            $str = $str;
            $str = "cabe";
            
        }
        
        return $str;
    }
    
    public function children(){
        return $this->hasMany(Task::class, 'parent_id', 'id');
        //return Task::where('parent_id',$this->id)->get();
    }


    
    public function getChildrenTask($request, $statuses_id){
        $model = Task::
                    join('project_users', 'tasks.project_id', '=', 'project_users.project_id')
                    ->join('projects','tasks.project_id', '=', 'projects.id')
                    ->where('parent_id', $this->id)
                    ->where( 
                        function ($query) use ($request, $statuses_id){
                            
                            $query = $query->where('project_users.user_id',"=", \Auth::id());
                            if(isset($request->observer)){
                                $query = $query->where('observer_id',"=", \Auth::id());
                            }
                            
                            $query = $query->where('projects.status_id',"=", 3);
                            
                            if(isset($request->user_id) && ($request->user_id != null)) {  
                                $query = $query->where('tasks.user_id', "=", $request->user_id);
                            }
                            if(isset($request->project_id))   
                                $query = $query->where('tasks.project_id', "=", $request->project_id);

                            $str="";
                            if(sizeof($statuses_id)){
                                $str = "";
                                $query = $query->where(function ($query) use ($statuses_id, $str){
                                    foreach($statuses_id as $key=>$value){
                                        $query= $query->orwhere('tasks.status_id', "=", $value);

                                }});
                                
                            }else{
                                $query = $query->where('tasks.status_id', "=", 1);
                                var_dump($query);
                            }
                            // if week & year
                            if(isset($request->week)&& ($request->week!=null)){
                                $dates = $this->getStartAndEndDate($request->week, $request->year);
                                $request->from_date = $dates[0];
                                $request->to_date = $dates[1];
                            }
                            if(isset($request->from_date)&& ($request->from_date!=null)){
                                $query = $query->whereBetween('due_date', array($request->from_date, $request->to_date));
                            }else{
                                /*
                                $this->to_date = date('Y-m-d');
                                $this->from_date = \Carbon\Carbon::now()->subDays(7)->toDateString();
                                $query = $query->whereBetween('due_date', array($this->from_date, $this->to_date));
                                */
                            }
                            if(isset($request->querystr)){
                                $query = $query->where(function ($query) use ($request){
                                    $query = $query->orwhere('tasks.name', "like", "%".$request->querystr."%");
                                    $query = $query->orwhere('tasks.description', "like", "%".$request->querystr."%");
                                });
                            }
                            

                            

                })
                    ->select(DB::raw('tasks.*, DATEDIFF( now(), tasks.created_at ) as lead_time'))
                    
                    
                    ->orderBy('tasks.project_id', 'asc')
                    
                    
                    ->orderBy('tasks.status_id', 'asc')
                    ->get();

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

        return $model;
    }

    public function subTypeOptions(){
        return TaskType::where('parent_id', $this->type_id)->get();
    }    
    public function type(){
        return $this->belongsTo(TaskType::class);
    }   

    /**
     * Devuelve los datos de la tarea formateados para el modal de edición.
     *
     * @return array
     */
    public function forModal()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'project_id' => $this->project_id,
            'priority' => $this->priority,
            'due_date' => $this->due_date ? Carbon::parse($this->due_date)->format('Y-m-d') : null,
            'not_billing' => $this->not_billing,
            'user_id' => $this->user_id,
            'status_id' => $this->status_id,
            'points' => $this->points,
            'file_url' => $this->file_url,
            'url_finished' => $this->url_finished,
            'description' => $this->description,
            // Cualquier otra transformación de datos necesaria
        ];
    }

    public function getPointsAsTimeString()
    {
        $hours = $this->points; 

        // Convert hours to seconds
        $seconds = $hours * 3600;

        // Format seconds to hh:mm:ss
        $hoursFormatted = intval(floor($seconds / 3600));
        $minutesFormatted = intval(floor(($seconds / 60) % 60));
        $secondsFormatted = intval($seconds % 60);
        
        // Return time string
        return sprintf('%02d:%02d:%02d', $hoursFormatted, $minutesFormatted, $secondsFormatted);
    }

}
