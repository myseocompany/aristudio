<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomerStatus;
use App\KnowledgeManagement;
use App\KnowledgeManagementType;
use DB;

class KnowledgeManagementController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $model = KnowledgeManagement::get();
        $types = KnowledgeManagementType::get();
        return view('knowledge_management.index', compact('model', 'types'));
    }

}
