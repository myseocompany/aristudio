<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;



use App\Task;

class Report extends Model
{
	public function countTaskByTag(
		$null_finish_date, $from_date, $to_date, $task_tag_type, $task_io_id, $task_io_type_id, $task_type_id){

		$model = 0;
		// procesos que entraron
		if(isset($from_date) && isset($to_date)){
				$model = Task::
					join('task_tags', 'tasks.id', '=', 'task_tags.task_id')
					->where(function ($query) use ($task_io_type_id, $from_date, $to_date, $null_finish_date, $task_io_id) {
						if($task_io_type_id==1 ) {
							$query = $query->where('input_id', '=', $task_io_id);
						}
						if($task_io_type_id==0 ) {
							$query = $query->where('output_id', '=', $task_io_id);
						}
						if($task_io_type_id==-1 ) {
							$query = $query->whereDate('reception_date', '<', $from_date);
						}else{
							$query = $query->whereBetween('reception_date', array($from_date, $to_date));
						}
						
						if($null_finish_date==null)
							$query = $query->whereNull('finish_date');
					})
					->where('task_tags.tag_id', '=', $task_tag_type->id)
					->where('tasks.type_id', '=', $task_type_id)
					->count();
				
			}
			
			
		

		return $model;
	}
	
}