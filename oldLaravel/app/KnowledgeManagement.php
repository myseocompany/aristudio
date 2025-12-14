<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Laravel\Scout\Searchable;

class KnowledgeManagement extends Model{

	function type(){
        return $this->belongsTo('App\KnowledgeManagementType');
    }
}
