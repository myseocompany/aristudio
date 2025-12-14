<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Prospects;
use App\Metadata;
use App\User;

// php artisan make:controller ProjectController -r

class ProspectsController extends Controller
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
        $prospects =     Prospects::all();
        return view('prospects.index', compact('prospects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('prospects.create');
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
        $model = new Prospects;

        $model->id = $request->id;
        $model->client = $request->client;
        $model->task = $request->task;
        $model->description = $request->description;

        $model->save();

        return redirect('/prospects');

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
        $project = Project::find($id);
        $task_status_options_JSON = Metadata::getTaskStatusOptions();
        $task_status_options = json_decode($task_status_options_JSON["value"], true);
        $users = User::all();


        return view('prospects.show', compact('prospects','task_status_options','users'));
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
        $project = Prospects::find($id);

        return view('prospects.edit', compact('project'));
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

        return redirect('/prospects');
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
