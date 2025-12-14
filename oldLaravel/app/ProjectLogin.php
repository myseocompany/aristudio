<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Laravel\Scout\Searchable;

class ProjectLogin extends Model
{
    
    public function project(){
        return $this->belongsTo('App\Project');
    }

    public function account() {
        return $this->hasMany('App\Account');
    }


    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
    }
}
