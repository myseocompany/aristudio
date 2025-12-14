<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Metadata;
use App\Campaign;
use App\Project;
use App\CampaignProjectMeta;
use App\ProjectMetaData;
use DB;
use App\ProjectMeta;
use App\ProjectLogin;
use App\ProjectDocument;







class MetadataController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*
    public function __construct()
    {
        $this->middleware('auth');
    }
*/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $model =    Metadata::all();
        return view('metadatas.index', compact('model'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        //
        return view('metadatas.create');
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
        $model = new Metadata;

        $model->id = $request->id;
        $model->name = $request->name;

        $model->value = $request->value;

        
        $model->save();

        return redirect('/metadatas');
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
        $model = Metadata::find($id);

        return view('metadatas.show', compact('model'));
    
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
        $model = Metadata::find($id);

        return view('metadatas.edit', compact('model'));
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
        $model = Metadata::find($id);

        
        $model->name = $request->name;
        $model->value = $request->value;
        
        $model->save();

        return redirect('/metadatas');
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


    public function createProject($pid, $cid){
    
        $projects = Project::find($pid);
        $campaign = Campaign::find($cid);
        
        return view('meta_data.create', compact('projects','campaign'));

    } 

    public function createBranding($id){       
        $projects = Project::find($id);
        $campaign = Campaign::find(2);
        //dd($campaign);
        return view('meta_data.2.create', compact('projects','campaign'));

    } 
      public function showBranding($id){       
        $projects = Project::find($id);
        $campaign = Campaign::find(2);
        $project_meta = ProjectMeta::where('project_id',$id)->get();
        return view('meta_data.2.show', compact('projects','campaign','project_meta'));

    } 



     public function createProjectNew(){
        $campaign = Campaign::find(1);
        return view('meta_data.createProject', compact('campaign'));

    } 

    public function createProjectBrief(){
        $campaign = Campaign::find(3);
        return view('meta_data.create_project_brief', compact('campaign'));

    } 

    public function showProject($pid, $cid){
      
        $project = Project::find($pid);
        $campaign = Campaign::find($cid);
        
        

        $project_document = ProjectDocument::where('project_id',$pid)->get();


        $project_meta = ProjectMeta::where('project_id',$pid)->get();

        //dd($project_meta[0]->meta_data_id);
        return view('meta_data.show', compact('project','campaign','project_meta','project_document'));

    } 
    

public function saveProjectCampaing(Request $request,$id){

    



         if(isset($request->name)){
                 $project = new Project;
                 $project->name = $request->name;
                 $project->save();
                 $id = $project->id;
            }

         $model = Project::find($id);
         //dd($id);
         $this->deleteProjectCampaignData($model->id, $request->campaign_id);
            
        foreach ($request->all() as $key => $value) {
            if (str_contains($key, 'meta_') ) { //determina si una cadena contiene una subcadena determinada
                $meta_data_id = (int)substr($key,5);//quitamos meta_  
                if(is_array($value)){
                    foreach ($value as $item  ) {
                        $this->saveProjectMeta($request, $meta_data_id,$item,$model->id);
                    }
                }else
                if( !is_null($value)){
                     $this->saveProjectMeta($request, $meta_data_id,$value,$model->id);
                }
             }
        }

        foreach ($request->all() as $key => $value) {
            if (str_contains($key, 'radio_')) { //determina si una cadena contiene una subcadena determinada
                $meta_data_id = (int)substr($key,6);//quitamos radio_  
                if(is_array($value)){
                    foreach ($value as $item  ) {
                        $this->saveProjectMeta($request, $meta_data_id,$item,$model->id);
                    }
                }else
                if( !is_null($value)){
                     $this->saveProjectMeta($request, $meta_data_id,$value,$model->id);
                }
             }
        }

        foreach ($request->all() as $key => $value) {
            if (str_contains($key, 'check_')) { //determina si una cadena contiene una subcadena determinada
                $meta_data_id = (int)substr($key,6);//quitamos check_  
                if(is_array($value)){
                    foreach ($value as $item  ) {
                        $this->saveProjectMeta($request, $meta_data_id,$item,$model->id);
                    }
                }else
                if( !is_null($value)){
                     $this->saveProjectMeta($request, $meta_data_id,$value,$model->id);
                }
             }
        }

//dd($request->all());
         foreach ($request->all() as $key => $value) {
            if (str_contains($key, 'login_')) { //determina si una cadena contiene una subcadena determinada
                 
                $meta_data_id = (int)substr($key,6);//quitamos login_  
                if(is_array($value)){
                    $size = (sizeof($value))/4;
                    $position = 0 ;
                    for($i=0; $i<$size; $i++){
                        foreach ($value as $key => $item  ) {
                            if( !is_null($item)){
                               $modelProject = new ProjectLogin;
                                //$model->meta_data_id = $meta_data_id;
                                $modelProject->project_id = $model->id;
                                $modelProject->name       = $value[$position];
                                $modelProject->url        = $value[$position+1];
                                $modelProject->user       = $value[$position+2];
                                $modelProject->password   = $value[$position+3];
                                if($key == $position){
                                    $modelProject->save();
                                }
                            }
                        }
                        $position = $position+4;
                    }       
                }
            }
        }
          
        $array = array("Logos de la empresa","Manual de marca","Activos de mercadeo (Imágenes, videos, textos)");
           


        $files = $request->file();

        foreach ($files as $key => $value) {
            if($request->hasFile($key)){       
                $modelDocument = new ProjectDocument;
                $originName = $request->file($key)->getClientOriginalName();
                $fileName = pathinfo($originName, PATHINFO_FILENAME);
                $destinationPath = 'public/files/'.$request->project_id."/";
                $extension = $request->file($key)->getClientOriginalExtension();
                $fileName = $fileName.'_'.time().'.'.$extension;
                $path = $request->file($key)->move($destinationPath,$fileName);
                $url = asset($destinationPath.$fileName);  
                $modelDocument->url = $url;
                $modelDocument->project_id = $model->id;
                $modelDocument->type_id = 5; 
                //$modelDocument->description = "aaaa" ; 
                $modelDocument-> save();
            }
        }

          //$this->saveCustomer2($model->id,$request->nit,$request->position,$request->namecontact);
           
        return redirect('https://myseocompany.co/');
        
    }

function deleteProjectCampaignData($project_id, $campaign_id){
      
    $parents = CampaignProjectMeta::where('campaign_id', $campaign_id)
                                 ->select(DB::raw('project_meta_data_id meta_data_id'))
                                 ->get();
    
    $children = ProjectMetaData::whereIn('parent_id', $parents->toArray())
                                ->select(DB::raw('id meta_data_id'))
                               // ->union($parents)
                                ->get();  
   
    $data = [];               
    foreach ($children as $item ) {
       $data[] = (int)$item->meta_data_id;
    }
    foreach ($parents as $item ) {
       $data[] = (int)$item->meta_data_id;
    }


    $model = ProjectMeta::whereIn('meta_data_id', $data)
                         ->where('project_id', $project_id)
                         ->delete();                    
                                                                                        
}
public function saveProjectMeta($request, $meta_data_id, $value, $project_id){
    $model = new ProjectMeta;
    $model->meta_data_id = $meta_data_id;
    $model->project_id = $project_id;

    if($meta_data_id == 55){
        
        if($request->hasFile('meta_55')){     
            $originName = $request->file('meta_55')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $destinationPath = 'public/files/'.$request->project_id."/";
            $extension = $request->file('meta_55')->getClientOriginalExtension();
            $fileName = $fileName.'_'.time().'.'.$extension;
            $path = $request->file('meta_55')->move($destinationPath,$fileName);
            
            $url = asset($destinationPath.$fileName);  
            $model->value = $url;
        }
    }else{

        $model->value = $value;
    }

    $model->save();
            
}







}
