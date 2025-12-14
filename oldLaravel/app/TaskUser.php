<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Task;
use App\User;

class TaskUser extends Model{
    function task(){
        return $this->belongsTo('App\Task', 'task_id');
    }

    function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}