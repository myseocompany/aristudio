<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectMeta extends Model{

	public function data(){
        return $this->belongsTo('App\ProjectMetaData', 'meta_data_id', 'id'); //->where('parent_id', '35');        
    }
	
}