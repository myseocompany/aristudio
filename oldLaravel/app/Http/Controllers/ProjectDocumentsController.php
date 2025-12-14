<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\ProjectDocument;
use App\DocumentType;
use App\Project;
use App\Account;
use App\ProjectLogin;
// use App\Project;

class ProjectDocumentsController extends Controller
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
        $document_types = DocumentsType::all();
        $projects = Project::all();
        $accounts = Account::all();
        $model = ProjectDocument::paginate(10);
        return view('documents.index', compact('model','document_types','projects', 'accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
         $projects = Project::orderBy('name')
            ->join('project_users', 'projects.id', '=', 'project_users.project_id')
            ->selectRaw('project_users.id as project_users_id, projects.id as id, projects.name as name')
            ->where('project_users.user_id', \Auth::id())
            ->get();
        $document_types = DocumentsType::all();
        $accounts = Account::all();
        return view('documents.create', compact('document_types', 'projects','accounts'));
    }

    public function storeFromRequest(Request $request){ 
        $result = false;  
        $model = new ProjectDocument;
        $model->type_id = $request->document_type_id;
        $model->project_id = $request->project_id;
        $model->account_id = $request->account_id;
        $model->internal_id = $request->internal_id;
        $model->date = $request->date;
        $model->debit = $request->debit;
        $model->credit = $request->credit;
        $model->description = $request->description;


        $originName = $request->file('file')->getClientOriginalName();
        $fileName = pathinfo($originName, PATHINFO_FILENAME);
        $destinationPath = 'public/files/'.$request->project_id."/";
        $extension = $request->file('file')->getClientOriginalExtension();
        $fileName = $fileName.'_'.time().'.'.$extension;
        $path = $request->file('file')->move($destinationPath,$fileName);
        $url = asset($destinationPath.$fileName);  
        $model->url = $url;

        $result = $model->save();
        
        return $result;

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
        
        if ($this->storeFromRequest($request)) {
            return redirect('/projects/')->with('status', 'El documento fué añadido con exito');
        }
        
    }

    public function storeFromDocuments(Request $request)
    {
         
        
        if ($this->storeFromRequest($request)) {
            return redirect('/documents/')->with('status', 'El documento fué añadido con exito');
        }
        
    }

     public function storeFile(Request $request, $document){
        $path = "";

        if($request->hasFile('file')){
            $file     = $request->file('file');
            $path = $file->getClientOriginalName();

            $destinationPath = 'public/files/'.$request->customer_id;
            $file->move($destinationPath,$path);
            
            
        }    
        
       

        $document->url = $path;
        $document->save();





        return back();
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = ProjectDocument::find($id);
        $projects = Project::all();
        $accounts = Account::all();
        return view('documents.show', compact('model','projects','accounts'));
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
        $model = ProjectDocument::find($id);
        $accounts = Account::all();
        $document_types = DocumentType::all();
        $accounts_name = Account::where('id',$model->id_account)->get();
        $document_types_name = DocumentType::find($model->type_id);
        

        return view('documents.edit', compact('model','accounts','document_types','accounts_name','document_types_name'));
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
        $model = ProjectDocument::find($id);
        $model->type_id = $request->document_type_id;
        $model->project_id = $request->project_id;
        $model->account_id = $request->account_id;
        $model->internal_id = $request->internal_id;
        $model->date = $request->date;
        $model->debit = $request->debit;
        $model->credit = $request->credit;
        $model->description = $request->description;
        
        if($request->file('file') != null){

            $parting = explode('/'.$model->project_id.'/',$model->url);
            unlink(public_path('public/files/'.$model->project_id.'/'.$parting[1].''));

            $originName = $request->file('file')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $destinationPath = 'public/files/'.$request->project_id."/";
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileName = $fileName.'_'.time().'.'.$extension;
            $path = $request->file('file')->move($destinationPath,$fileName);
            $url = asset($destinationPath.$fileName);  

            $model->url = $url;
        }

        

        
        if ($model->save()) {
            return redirect('/projects/'.$model->project_id)->with('status', 'El documento fué actualizado con exito');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = ProjectDocument::find($id);
        $parting = explode('/'.$model->project_id.'/',$model->url);
        //dd($model->url);
       // unlink(public_path('public/files/'.$model->project_id.'/'.$parting[0].''));
        if ($model->delete()) {
            //File::delete('/public/'.$parting[1]);
            
            return back();
        }
    }
}
