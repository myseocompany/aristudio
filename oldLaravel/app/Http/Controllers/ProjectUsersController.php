<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\ProjectUser;
use App\Metadata;
use App\User;
use App\Task;
use App\TaskStatus;

// php artisan make:controller ProjectController -r

class ProjectUsersController extends Controller
{
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
        $projectusers = ProjectUser::all();
        return view('projects.index', compact('projectusers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('projectusers.create');
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
        $model = new ProjectUser;

        // $model->id = $request->id;
        $model->project_id = $request->project_id;
        $model->user_id = $request->user_id;

        $model->save();

        return redirect('projects');

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
        $project_user = ProjectUsers::find($id);
        //$task_status_options_JSON = Metadata::getTaskStatusOptions();
        //$task_status_options = json_decode($task_status_options_JSON["value"], true);
        $users = User::all();
        $project = Project::all();

        return view('projectusers.show', compact('project','taskStatus','users'));
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
        $project = Project::find($id);

        return view('projects.edit', compact('project'));
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
        $model = Project::find($id);

        
        $model->name = $request->name;
        $model->description = $request->description;
        $model->budget = $request->budget;
        $model->start_date = $request->start_date;
        $model->finish_date = $request->finish_date;

        
        $model->save();

        return redirect('/projects');
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
}
