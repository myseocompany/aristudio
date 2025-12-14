<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
   

    public function task(){
    	return $this->hasMany(Task::class);
    }

    public function getTaskStatusOptionsById($id){
    	return TaskStatus::find($id)->name;
    }
    public static function getColor($id){
    	return TaskStatus::find($id)->color;	
    }
    public static function getName($id){
    	return TaskStatus::find($id)->name;	
    }

    
}
