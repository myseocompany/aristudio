<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ProjectDocument;
use App\Project;
use App\Account;
use App\ProjectLogin;
use App\DocumentType;
use App\ProjectStatus;
use App\ProjectType;
use App\ProjectUser;

class DocumentController extends Controller
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
    public function index(Request $request)
    {
        

        $model= ProjectDocument::where('type_id', 1)->orderBy('date', 'DESC')->get();
        $document_types = DocumentType::all();
        $accounts = Account::all();
        $projects = Project::where('status_id', 3)->orderBy('name', 'ASC')->get();


        return view('documents.index', compact('model','projects','accounts','document_types'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $model = ProjectDocument::find($id);
        $projects = Project::all();
        $accounts = Account::all();
        return view('documents.show', compact('model','projects','accounts'));
    }


}
