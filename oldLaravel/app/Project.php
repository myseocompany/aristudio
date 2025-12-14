<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProjectUser;
use DB;
use Carbon\Carbon;
use App\TaskStatus;

class Project extends Model
{
    //
    public function getDateInput($date){
    	return date('Y-m-d',strtotime($date));
    }

    public function tasks(){
    	return $this->hasMany(Task::class)->orderByDesc('due_date');
    }
    function document(){
        return $this->belongsTo('App\Document');
    }
    public function users(){
    	return $this->belongsToMany('App\User','project_users','project_id', 'user_id');
    }
    public function status(){
        return $this->belongsTo('App\ProjectStatus');
    }

    public function getStatusName(){
        return isset($this->status_id)?$this->status->name:"Without status";
    }
    

    public function getProjectStatusOptionsById(){
        $model = ProjectStatus::find($this->status_id);
        $str='Without Status';
        if (($model)!=null) {
            $str=$model->name;
        }
        return $str;
    }
    public function getProjectTypeOptionsById(){
        $model = ProjectType::find($this->type_id);
        $str='Without Type';
        if (($model)!=null) {
            $str=$model->name;
        }
        return $str;
    }

    public function getFinishedTask($period){
        $count = \DB::table('tasks')
                ->where('status_id', 3)
                ->where('project_id', $this->id)
                ->count('project_id');
        return $count;
    }

    public function getPlannedTask($period){
        $count = \DB::table('tasks')
                ->where('status_id', 1)
                ->where('project_id', $this->id)
                ->count('project_id');
        return $count;
    }

    public function countTaskByStatusAndDates($task_statuses, $request){


        $model = 0;
        $date = "";
        if(isset($request->from_date) && ($request->from_date!=null)){
            $to_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->to_date." 23:59:59");
            $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date);
            $date = array($from_date->format('Y-m-d'), $to_date->format('Y-m-d H:i:s'));
            //dd($date);
        }
        $data = array();
        $ts = array();
        foreach ($task_statuses as $item) {
            $ts[]= $item->id;
        }
        // procesos que entraron
        if(isset($request->from_date) && isset($request->to_date)){
                $model = Task::
                    where(function ($query) use ($request) {
                        //$query->where('project_id', '=', 8);
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
                            //dd($request->user_id);
                            $query->where('user_id', '=', $request->user_id);
                        else
                            $query->whereIn('user_id', $all_users_id);
                    })
                    ->whereBetween('due_date', $date)
                    ->where('project_id', '=', $this->id)
                    ->groupBy('status_id')
                    ->select(DB::raw('status_id as id, sum(points) as points, count(id) as count, count(if(points IS NULL OR points = 0 ,1,NULL)) as not_points'))
                    ->get();
                     //dd($model);
                    foreach ($model as $item) {
                        $data[$item->id] = array($item->points ,$item->count, $item->not_points);
                    }
            }
            //dd($data);
        return $data;
    }

    public function countTaskInventoryBydDate($to_date){
        $model = 0;
        // procesos que entraron
        if(isset($to_date)){
                $model = Task::
                    where('project_id', '=', $this->id)
                    ->where(function ($query)  {
                        $query = $query->where('status_id', '=', 1);
                        $query = $query->orWhere('status_id', '=', 8);
                        $query = $query->orWhere('status_id', '=', 2);
                        //$query = $query->orWhere('status_id', '=', 6);
                    })
                    ->whereDate('due_date', '<', $to_date)
                    ->sum('points');
                
            }
            //dd($model);
        return $model;
    }


}
