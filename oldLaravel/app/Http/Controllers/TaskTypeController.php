<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\TaskType;


class TaskTypeController extends Controller{

	function index(Request $request){
		$model = TaskType::where(function ($query) use ($request
		){
			if(isset($request->parent_id))
				$query->where('parent_id', '=',$request->parent_id);
		})->get();

		$options = TaskType::whereNull('parent_id')->get();

		return view('task_type.index', compact('model', 'options'));
	}


	function store(Request $request){
		$model = new TaskType();

		$model->name = $request->name;
		//dd($request);
		//exit(0);
		if(isset($request->parent_id) && ($request->parent_id!=""))
			$model->parent_id = $request->parent_id;

		$model->save();

		return redirect('task_types'); 
	}

	function edit($id){
		$model = TaskType::find($id);

		$options = TaskType::whereNull('parent_id')->get();
		return view('task_type.edit', compact('model', 'options'));
	}

	function update(Request $request){
		$model = TaskType::find($request->id);

		$model->name = $request->name;
		//dd($request);
		//exit(0);
		if(isset($request->parent_id) && ($request->parent_id!=""))
			$model->parent_id = $request->parent_id;

		$model->save();

		return redirect('task_types'); 
	}
}
