<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

// use App\Project;

class ProjectUser extends Authenticatable
{

    // public function projects(){
    //     return $this->belongsToMany(Projects::class);
    // }
    public $timestamps = false;
}