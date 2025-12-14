<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\Metadata;
use App\User;
use App\Project;
use App\TaskVersion;

class TaskVersionController extends Controller
{
    
    protected $attributes = ['project_name'];

    protected $appends = ['project_name'];

    protected $project_name;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $tasks   =    Task::all();
        foreach ($tasks as $model){
            if(isset($model->project_id))
            $model->project_name = Project::find($model->project_id)->name;
            if(isset($model->user_id))
            $model->user_name = User::find($model->user_id)->name;
            if(isset($model->status_id))
            $model->status_name = Metadata::getTaskStatusOptionsById($model->status_id);
        
        }
        $model = $tasks;

        return view('tasks.index', compact('model'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        $task_status_options_JSON = Metadata::getTaskStatusOptions();
        $task_status_options = json_decode($task_status_options_JSON["value"], true);
        $users = User::all();
        $projects = Project::all();

        //dd($task_status_options);
        return view('tasks.create', compact('task_status_options', 'users', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $model = new Task;

        $model->id = $request->id;
        $model->name = $request->name;
        $model->points = $request->points;
        $model->description = $request->description;
        $model->project_id = $request->project_id;
        $model->user_id = $request->user_id;
        $model->status_id = $request->status_id;

        
        $model->save();

        //dd($request);
        if( isset($request->from) && ($request->from == "project")){
            return back();
        }

        return redirect('/tasks');
    }


   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $model = Task::find($id);
        if(isset($model->project_id))
        $model->project_name = Project::find($model->project_id)->name;
        if(isset($model->user_id))
        $model->user_name = User::find($model->user_id)->name;
        if(isset($model->status_id))
        $model->status_name = Metadata::getTaskStatusOptionsById($model->status_id);
         
        
        return view('tasks.show', compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $model = Task::find($id);
        
        $task_status_options_JSON = Metadata::getTaskStatusOptions();
        $task_status_options = json_decode($task_status_options_JSON["value"], true);
        $users = User::all();
        $projects = Project::all();

        //dd($task_status_options);
        return view('tasks.edit', compact('model','task_status_options', 'users', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        //
        $model = Task::find($id);
        

        $model->name = $request->name;
        $model->points = $request->points;
        $model->description = $request->description;
        $model->project_id = $request->project_id;
        $model->user_id = $request->user_id;
        $model->status_id = $request->status_id;

        
        $model->save();

        return redirect('/tasks/'.$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getProject_nameAttribute(){
        
        return $this->id."j";   
    }
}
