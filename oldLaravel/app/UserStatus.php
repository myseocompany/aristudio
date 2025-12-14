<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
   

    public function user(){
    	return $this->hasMany(User::class);
    }

    
    

    
}
