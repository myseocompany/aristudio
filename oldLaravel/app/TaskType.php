<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model{

    public static function getTree(&$data, $parent_id){
        
        $model = null;

        if($parent_id!=null)
            $model = TaskType::where('parent_id', $parent_id)->orderBy('slug')->get();
        else
            $model = TaskType::whereNull('parent_id')->orderBy('slug')->get();

        foreach($model as $item){
            $data[$item->id] = $item->slug . " - " . $item->name;
            $item->getTree($data, $item->id);
        }
        
    }
}