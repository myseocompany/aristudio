<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomerStatus;
use App\KnowledgeManagement;
use DB;

class KnowledgeManagementTypeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $model = KnowledgeManagement::get();
        return view('knowledge_management.index', compact('model'));
    }

}
