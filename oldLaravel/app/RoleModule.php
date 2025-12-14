<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class RoleModule extends Model{
	public function role(){
        return $this->belongsTo('App\Role');
    }
	public function module(){
        return $this->belongsTo('App\Module');
    }
}