<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
   

    // public function task(){
    // 	return $this->hasMany(Project::class);
    // }

    // public function getProjectTypeOptionsById($id){
    // 	return TaskStatus::find($id)->name;
    // }
     function document(){
    	return $this->belongsTo('App\Documents');
    }

    
}
