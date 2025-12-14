<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
   

    public function task(){
    	return $this->hasMany(Project::class);
    }

    public function getProjectStatusOptionsById($id){
    	return TaskStatus::find($id)->name;
    }
    public function project(){
    	return $this->hasMany('\App\Project');
    }
    
}
