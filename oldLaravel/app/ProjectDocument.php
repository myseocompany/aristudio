<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Laravel\Scout\Searchable;

class ProjectDocument extends Model
{
    // use Searchable;
    //
    // function account(){
    //  return $this->belongsTo('App\Account');
    // }
    public function project(){
        return $this->belongsTo('App\Project');
    }

    public function type(){
        return $this->belongsTo('App\DocumentType');
    }
    public function account() {
        return $this->belongsTo('App\Account');
    }


 
    // function employee_files(){
    //  return $this->hasMany('App\EmployeeFile');
    // }



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
