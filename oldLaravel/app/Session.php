<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Laravel\Scout\Searchable;

class Session extends Model{
	function customer(){
    	return $this->belongsTo('App\Customer');
    }
}