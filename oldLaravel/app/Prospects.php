<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prospects extends Model
{
    //
    public function getDateInput($date){
    	return date('Y-m-d',strtotime($date));
    }

    // public function tasks(){
    // 	return $this->hasMany(Task::class);
    // }
}
