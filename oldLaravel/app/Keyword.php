<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Keyword extends Model
{
    //use Searchable;
    //
    public function getDateInput($date){
    	return date('Y-m-d',strtotime($date));
    }
    
    // Buscador
    //   public function scopeName($query, $name) {
    //     if (trim($name) != '') {
    //         $query->where('name', "LIKE", "%$name%")
    //               ->orWhere('description', "LIKE", "%$name%");
    //     }
    // }


    // public function scopeSearch($query, $s) {
    //     return $query->where('name', 'LIKE', '%'.$s.'%')
    //                  ->orwhere('description', 'LIKE', '%'.$s.'%');
    // }
    /*
    public function searchableAs(){
        return 'task_id';
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
    }
    */
    
}
