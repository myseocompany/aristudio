<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TasksVersion extends Model
{
    //
    public function getDateInput($date){
    	return date('Y-m-d',strtotime($date));
    }

    public function projec(){
    	return $this->belongsTo(TaskVersion::class);
    }
    public function status(){
        return $this->belongsTo(TaskStatus::class);
    }
}
