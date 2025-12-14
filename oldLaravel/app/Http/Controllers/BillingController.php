<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Project;
use App\Metadata;
use App\User;
use App\Task;
use App\TaskStatus;
use App\ProjectStatus;
use App\ProjectType;
use App\Role;
use App\ProjectUser;
use App\ProjectLogin;
use App\Account;
use App\DocumentType;
use App\ProjectDocument;


class BillingController extends Controller{

	public function __construct()
    {
        $this->middleware('auth');
    }
     

    public function index(Request $request){

    	$model= ProjectDocument::where('type_id',1)
            ->orderBy('date', 'DESC')
            ->get();
    	$projects = Project::where('status_id', 3)->orderBy('name', 'ASC')->get();
        $accounts = Account::all();
        $document_types = DocumentType::all();

         $projects = Project::orderBy('name')
            ->selectRaw('projects.id as id, projects.name as name')
            ->where('projects.status_id', 3)
            ->get();


		return view('billing.index',compact('model','projects','accounts','document_types'));
    }


    
}