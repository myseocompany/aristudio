<?php

namespace App;
use DB;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Project;
use App\RoleModule;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id','phone','document'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


  public function getRoleModule($role_id,$module_id){

        $role = RoleModule::where('role_id',$role_id)
                            ->where('module_id',$module_id)
                            ->first();
        if($role){
            $module = $role->module_id;
        }else{
            $module = 1; 
        }
         
        return $module;
    }

 public function getPermitsRoleModule($role_id,$module_id,$created,$readed,$updated,$deleted,$list){
            
            if($created==1){
                $role_module = RoleModule::where('role_id',$role_id) 
                            ->where('module_id',$module_id)
                            ->where('created',$created)
                            ->first();

                     if($role_module){
                        $module = 1;
                     } else{
                         $module = 2;
                     }      
            }
            if($updated==1){
                 $role_module = RoleModule::where('role_id',$role_id) 
                            ->where('module_id',$module_id)
                            ->where('updated',$updated)
                            ->first();

                     if($role_module){
                        $module = 1;
                     } else{
                         $module = 2;
                     } 

            }
            if($readed==1){
                 $role_module = RoleModule::where('role_id',$role_id) 
                            ->where('module_id',$module_id)
                            ->where('readed',$readed)
                            ->first();

                     if($role_module){
                        $module = 1;
                     } else{
                         $module = 2;
                     } 
            }

            if($deleted==1){
                $role_module = RoleModule::where('role_id',$role_id) 
                            ->where('module_id',$module_id)
                            ->where('deleted',$deleted)
                            ->first();

                     if($role_module){
                        $module = 1;
                     } else{
                         $module = 2;
                     } 
                
            }
              if($list==1){
                $role_module = RoleModule::where('role_id',$role_id) 
                            ->where('module_id',$module_id)
                            ->where('list',$list)
                            ->first();

                     if($role_module){
                        $module = 1;
                     } else{
                         $module = 2;
                     } 
                
            }
        //dd($role_module);
        return $module;
    }




  public function roleModule(){
        return $this->belongsTo('App\ModuleRole');
    }

    public function projects(){
        return $this->hasMany(Projects::class);
    }
    public function role(){
        return $this->belongsTo('App\Role');
    }
    public function status(){
        return $this->belongsTo('App\UserStatus');
    }

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
    public static function getName($id){
        $name = "";
        if(isset($id)&&($id!=0)){
            $name = User::find($id)->name;
        }
        return $name;
    }

    public function getShortName($num_chars){
        $name = $this->name;
        if(strlen($name)>$num_chars)
            $name = substr($name, 0, $num_chars);
        return $name;
    }

    public function hasRole($str){
        
        $has = false;
        
        if(isset($this->role)){
           $model = $this->role;
           if($model->name == $str){
            $has = true;
           } 
        }
        return $has;    
    }

    public function getTodayTaskFromProject($pid, $request, $statuses_id){
        //$from = Carbon\Carbon::now();
        //$today = new DateTime();
        $from = date('Y-m-d');
        $to = date('Y-m-d', strtotime(' +1 day'));

        if(isset($request->from_date)){
            $from = Carbon::parse($request->from_date);
            $to = Carbon::parse($request->to_date)->addHours(23);
            $to = $to->addMinutes(59);


        }
        
        //$to = $from->addDays(1,'day');
        
        $model = Task::where('project_id',$pid)
            ->where('user_id',$this->id)
            ->whereBetween('due_date', [$from, $to])
            ->whereIn('status_id', $statuses_id)
            ->get();
        if( ($this->id==1) && ($pid == 90))
        //    dd($to);
        ;
        return $model;
    }

    public function countTaskInventoryBydDate($to_date){
        $model = 0;
        // procesos que entraron
        if(isset($to_date)){
                $model = Task::
                    where('user_id', '=', $this->id)
                    ->whereIn('status_id', [1, 8, 2, 58] )
                    
                    ->whereDate('due_date', '<=', $to_date)
                    ->count();
                
            }
        return $model;
    }   

    // nico 2021-06-02
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
                /*
                    where(function ($query) use ($request) {
                        //$query->where('project_id', '=', 8);
                        //if(isset($query->user_id))
                          //  $query->where('user_id', '=', $user_id);
                    })
                    */
                    whereBetween('due_date', $date)
                    ->where('user_id', '=', $this->id)
                    ->groupBy('status_id')
                    ->select(DB::raw('status_id as id, sum(points) as points,  count(id) as count'))
                    ->get();
                    
                foreach ($model as $item) {
                    $data[$item->id] = array($item->points ,$item->count);
                }
            }
            
        return $data;

    }


    public function getUser($uid){
        $user = User::where('id',$uid)->get();
        return $user;
    }
    public function getProjects(){
        $model = Project::orderBy('weight')
            ->join('project_users', 'projects.id', '=', 'project_users.project_id')
            ->selectRaw('project_users.id as project_users_id, projects.id as id, projects.name as name, projects.color as color')
            ->where('project_users.user_id', $this->id)
            ->where('projects.status_id', 3)
            
            ->get();
            return $model;
    }

    
}