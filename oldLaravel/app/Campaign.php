<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model{

	public function ProjectMetaData(){
        return $this->belongsToMany('App\ProjectMetaData','campaign_project_meta_data','campaign_id','project_meta_data_id'); //
    }

    public function project_meta_data(){
        return $this->belongsToMany('App\ProjectMetaData','campaign_project_meta_data','campaign_id','project_meta_data_id'); //
    }

    

	
}