<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectMetaData extends Model{
	public static function getOptions($id){
		return ProjectMetaData::where('parent_id', $id)->get();
	}

	public static function getName($id){
		return ProjectMetaData::where('id', $id)->first()->name;
	}



	public static function getColor($id){
		return ProjectMetaData::where('id', $id)->first()->color;
	}


    function ProjectMetaDataChildren(){
     	
     	return $this->hasMany('App\ProjectMetaData','parent_id')->orderBy('weight');;
     	//return ProjectMetaData::where('parent_id',$this->id)->get();

     }
     function isMultiple(){
     	$res=true;
     	if($this->type_id==1 || $this->type_id==4){
     		$res=false;
     	}
     	return $res;
     }

     function getInputType(){

     	$str="";
     	switch ($this->type_id) {
     		case 1:
     			$str="text";
     			break;
     		case 2:
     			$str="radio";
     			break;
     		case 3:
     			$str="checkbox";
     			break;
     		case 4:
     			$str="textarea";
                      
     			break;
     		case 5:
                    $str="file";
                    break;
     		default:
     			# code...
     			break;
     	}
     	return $str;
     }
	 public function getAnswer($pid, $project_meta_data){
		$model = ProjectMeta::where("project_id", $pid)
			->where("meta_data_id", $project_meta_data->id)->first();
		
		$str = "";
		$parent = ProjectMeta::where("id", $project_meta_data->parent_id)->first();

		if($model)
			$str = $model->value;
		/*
		if($parent->type_id == 3 )
			$str = "x";
		*/	
		
		return  $str;
     			 
	 }
}