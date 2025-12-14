<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Mail;

use App\Customer;
use App\CustomerStatus;
use App\CustomerStatusPhase;
use App\User;
use App\CustomerSource;
use App\CustomerHistory;
// use App\Account;
// use App\EmployeeStatus;
// use App\Mail;
use App\EmailBrochure;
use App\Action;
use App\Project;

use App\Email;
use App\ActionType;
use Auth;
use Carbon;
use App\SendWithData;
use App\CustomerMeta;

class CustomerController extends Controller
{

    protected $attributes = ['status_name'];

    protected $appends = ['status_name'];

    protected $status_name;

    public function __construct(){   $this->middleware('auth'); }

    public function indexJson(Request $request){
        return Customer::all();
    }

    public function index(Request $request){
        
        return $this->getCustomers($request);
    }

    public function userIcon($name)
    {
        $user = Users::find($name);
        return view('customers.index', compact('name'));
    }

    public function indexPhase($pid, Request $request){
        
        


        $users = $this->getUsers();
        $customer_options = CustomerStatus::where('stage_id', $pid)->get();
        $statuses = $this->getStatuses($request, $pid);
        $customersGroup = $this->countFilterCustomers($request, $statuses);
        $projects = Project::all();
        $pending_actions = $this->getPendingActions();
        $phase = CustomerStatusPhase::find($pid);


        $model = $this->getModel($request, $statuses, $pid);

        $sources = CustomerSource::all();


        return view('customers.index', compact('model', 'request', 'customer_options', 'customersGroup', 'users', 'sources', 'projects', 'pending_actions', 'phase'));
    }

    public function getPendingActions(){
        $model = Action::whereNotNull('due_date')
            ->whereNull('delivery_date')
            //->where('creator_user_id', "=", Auth::id())
            ->get();
        //dd($model);
        return $model;
    }

    public function getCustomers(Request $request){
        $users = $this->getUsers();
        $customer_options = CustomerStatus::all();
        $statuses = $this->getStatuses($request, 1);
        $model = $this->getModel($request, $statuses, 1);
        
        $customersGroup = $this->countFilterCustomers($request, $statuses);
            
        $projects = Project::all();
        
        $sources = CustomerSource::orderby('name')->get();

        $pending_action = Action::find($request->pending_action_id);
        //botón acciones
        $today = Carbon\Carbon::now();
        $action_options = ActionType::orderBy('weight', 'ASC')->get();
        $statuses_options = CustomerStatus::orderBy("weight", "ASC")->get();

        return view('customers.index', compact('model', 'today', 'request', 'customer_options', 'action_options', 'statuses_options',  'customersGroup', 'users', 'sources', 'projects', 'pending_action'));
    }

    public function saveAction(Request $request){
        
        $customer = Customer::find($request->customer_id);
        if (is_null($request->type_id)) {
            return back()->with('statustwo', 'El Cliente <strong>' . $customer->name . '</strong> no fue modificado!');
        }
        if(!isset($request->pending_action_id))
            $this->createNewAction($request);
        else
            $this->updateAction($request);
        $this->changeCustomerStatus($request, $customer);
        $this->changeCustomerScoring($request, $customer);

                
        return back()->with('statusone', 'El Cliente <strong>' . $customer->name . '</strong> fué modificado con éxito!');

    }

    public function excel(Request $request)
    {
        $users = $this->getUsers();
        $customer_options = CustomerStatus::all();
        $statuses = $this->getStatuses($request, 1);

        $model = $this->getModel($request, $statuses, 1);
        $customersGroup = $this->countFilterCustomers($request, $statuses);

        $sources = CustomerSource::all();

        return view('customers.excel', compact('model', 'request', 'customer_options', 'customersGroup', 'users', 'sources'));
    }

    public function leads(Request $request)
    {
        $users = $this->getUsers();
        $customer_options = CustomerStatus::all();
        $statuses = $this->getStatuses($request, 1);
        $model = $this->getModel($request, $statuses, 1);
        $customersGroup = $this->countFilterCustomers($request, $statuses);
        $projects = Project::all();
        $pending_actions = $this->getPendingActions();


        $sources = CustomerSource::all();


        return view('customers.index', compact('model', 'request', 'customer_options', 'customersGroup', 'query', 'users', 'sources', 'projects', 'pending_actions'));
    }

    public function getModel(Request $request, $statuses, $phase_id)
    {
        $model = $this->filterModel($request, $statuses, $phase_id);
        //dd($request);

        $model->getActualRows = $model->currentPage() * $model->perPage();

        if ($model->perPage() > $model->total())
            $model->getActualRows = $model->total();
        $model->phase_id = $phase_id;
        return $model;
    }



    public function getUsers()
    {
        return  User::orderBy('name')
            ->where('users.status_id', 1)
            ->get();
    }

    public function getStatuses(Request $request, $step)
    {
        $statuses;
        if (isset($request->from_date) || ($request->from_date != ""))
            $statuses = $this->getAllStatusID();
        else
            $statuses = $this->getStatusID($request, $step);

            
        return $statuses;
    }







    public function filterModel(Request $request, $statuses, $phase_id){   
        //dd($request, $statuses, $phase_id);
        //dd($statuses);
        $status_array = Array();
        foreach($statuses as $item)
            $status_array[] = $item;
        $dates = $this->getDates($request);

        
        $model = Customer::select('address','total_sold','bought_products','scoring','customers.id','customers.status_id', 'customers.project_id','customers.user_id','customers.source_id', 'customers.created_at', 'customers.updated_at',
        'customers.name','customers.phone','customers.email')
            ->where(
            // Búsqueda por...
            function ($query) use ($request, $phase_id, $status_array, $dates) {
                $date_at = $request->created_updated === "updated" ? 'customers.updated_at' : 'customers.created_at';

                if(isset($request->from_date) && $request->from_date != "") {
                    $query->whereBetween($date_at, $dates);
                    
                }

                if (isset($request->user_id)  && ($request->user_id != null))
                    $query->where('user_id', $request->user_id);
                if (isset($request->source_id)  && ($request->source_id != null))
                    $query->where('source_id', $request->source_id);
                if (isset($request->status_id)  && ($request->status_id != null))
                    $query->where('customers.status_id', $request->status_id);

                if(isset($request->search) && $request->search != "") {
                    $query->where(function ($query) use ($request) {
                            $search = html_entity_decode($request->search );
                            $search = trim($request->search); // Eliminar espacios en blanco
                            $search = preg_replace('/\s+/', ' ', $search); // Eliminar múltiples espacios en blanco
                            $search = urldecode($request->search);
                            $search = trim(preg_replace('/\s+/', ' ', $search));

                            //dd($search);
                            $query->orwhere('customers.name', "like", "%" . $search . "%");
                            $query->orwhere('customers.email',   "like", "%" . $search . "%");
                            $query->orwhere('customers.document', "like", "%" . $search . "%");
                            $query->orwhere('customers.position', "like", "%" . $search . "%");
                            $query->orwhere('customers.business', "like", "%" . $search . "%");
                            $query->orwhere('customers.phone',   "like", "%" . $search . "%");
                            $query->orwhere('customers.phone2',   "like", "%" . $search . "%");
                            $query->orwhere('customers.notes',   "like", "%" . $search . "%");
                            $query->orwhere('customers.city',    "like", "%" . $search . "%");
                            $query->orwhere('customers.country', "like", "%" . $search . "%");
                            $query->orwhere('customers.bought_products', "like", "%" . $search . "%");
                            $query->orwhere('customers.status_temp', "like", "%" . $search . "%");
                            $query->orwhere('customers.contact_name', "like", "%" . $search . "%");
                            $query->orwhere('customers.contact_phone2', "like", "%" . $search . "%");
                            $query->orwhere('customers.contact_email', "like", "%" . $search . "%");
                            $query->orwhere('customers.contact_position', "like", "%" . $search . "%");
                           
                        }
                    );
                }
                
                //dd($query->toSql(), $query->getBindings());  
            }
            
            
        )   ->orderBy('customers.status_id', 'asc')
            ->orderBy('created_at', 'desc')
           ->paginate(50);

        
            
        return $model;
    }

    public function filterModelFull(Request $request, $statuses)
    {

        //        $model = Customer::wherein('customers.status_id', $statuses)
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) {

                $date_at = $request->created_updated === "updated" ? 'customers.updated_at' : 'customers.created_at';

                if(isset($request->from_date) && $request->from_date != "") {
                    $query->whereBetween($date_at, $dates);
                }

                if (isset($request->user_id)  && ($request->user_id != null))
                    $query = $query->where('user_id', $request->user_id);
                if (isset($request->source_id)  && ($request->source_id != null))
                    $query = $query->where('source_id', $request->source_id);
                if (isset($request->project_id)  && ($request->project_id != null))
                    $query = $query->where('project_id', $request->project_id);
                if (isset($request->kpi)  && ($request->kpi != null))
                    $query = $query->where('kpi', $request->kpi);
                if (isset($request->search)) {
                    $query = $query->orwhere('customers.name', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.email',   "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.document', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.business', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.position', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.phone',   "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.phone2',   "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.notes',   "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.city',    "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.country', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.bought_products', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.status_temp', "like", "%" . $request->search . "%");
                    $query = $query->orwhere('customers.contact_name', "like", "%" . $request->search . "%");
                }
            }


        )
            ->orderBy('status_id', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
            

        return $model;
    }

    public function getDates($request){
        $to_date = Carbon\Carbon::today()->subDays(0); // ayer
        $from_date = Carbon\Carbon::today()->subDays(3000);
        if(isset($request->from_date) && ($request->from_date!=null)){
            
            
            $from_date = Carbon\Carbon::createFromFormat('Y-m-d', $request->from_date);
            $to_date = Carbon\Carbon::createFromFormat('Y-m-d', $request->to_date);
        }

        $to_date = $to_date->format('Y-m-d')." 23:59:59";
        $from_date = $from_date->format('Y-m-d');

        return array($from_date, $to_date); 
    }

    public function countFilterCustomers($request, $statuses){
        //$customersGroup = Customer::wherein('customers.status_id', $statuses)
        $dates = $this->getDates($request);
        
        $customersGroup = CustomerStatus::wherein('customer_statuses.id', $statuses)
            ->leftJoin("customers", 'customers.status_id', '=', 'customer_statuses.id')
            ->where(
                
                // Búsqueda por...
                function ($query) use ($request, $dates) {
                    $date_at = $request->created_updated === "updated" ? 'customers.updated_at' : 'customers.created_at';

                    if(isset($request->from_date) && $request->from_date != "") {
                        $query->whereBetween($date_at, $dates);
                    }
                    if (isset($request->user_id)  && ($request->user_id != null))
                        $query->where('customers.user_id', $request->user_id);
                    if (isset($request->source_id)  && ($request->source_id != null))
                        $query->where('customers.source_id', $request->source_id);
                        
                        if (isset($request->status_id)  && ($request->status_id != null))
                        $query->where('customers.status_id', $request->status_id);
                    if (isset($request->scoring)  && ($request->scoring != null))
                        $query->where('customers.scoring', $request->scoring);
                    
                    if (isset($request->project_id)  && ($request->project_id != null)){
                        if($request->project_id != 0)
                        $query->where('project_id', $request->project_id);
                        if($request->project_id == 0)
                        $query->whereNull('project_id');
                        
                    }
                        
                        
                          
                    if (isset($request->search)  && ($request->search != null))
                        $query->where(function ($innerQuery) use ($request) {
                            $innerQuery->orwhere('customers.name', "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.email',   "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.document', "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.position', "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.business', "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.phone',   "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.phone2',   "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.notes',   "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.city',    "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.country', "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.bought_products', "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.status_temp', "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.contact_name', "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.contact_phone2', "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.contact_email', "like", "%" . $request->search . "%");
                            $innerQuery->orwhere('customers.contact_position', "like", "%" . $request->search . "%");
                        }
                    );
                    
                    
                }
            )
            ->select(DB::raw('customer_statuses.id as status_id, count(customers.id) as status_count'))
            ->groupBy('customer_statuses.id')
            ->groupBy('weight')

            ->orderBy('weight', 'ASC')

            ->get();
        
            
        foreach ($customersGroup as $item) {
            $included = false;
            foreach ($statuses as $status => $value) {
                if ($value == $item->status_id) {
                    $included = true;
                }
            }
            if ($included) {
                $item->color = CustomerStatus::getColor($item->status_id);
                $item->name = CustomerStatus::getName($item->status_id);
                $item->id = $item->status_id;
            }
        }
        

        //dd($customersGroup->toSql(), $customersGroup->getBindings());

        return $customersGroup;
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $customers_statuses = CustomerStatus::orderBy('stage_id', 'ASC')->orderBy('weight', 'ASC')->get();
        $customer_sources = CustomerSource::all();
        $projects = Project::where('status_id', 3)
            ->orderby("weight", "ASC")
            ->get(); // running
        return view('customers.create', compact('customers_statuses', 'users', 'customer_sources', 'projects'));
    }

    public function storeMetaDataFromSelect($model, $mid, $select ){
        if(is_array($select)){
            foreach($select as $item){
                if($this->isValidMeta($item)){
                    $meta = new CustomerMeta;
                    $meta->customer_id = $model->id;
                    $meta->meta_data_id = $item;
                    $meta->meta_data_type_id = $mid;
                    $meta->save();
                }
            }
        }else{
            if($this->isValidMeta($select)){
                $meta = new CustomerMeta;
                $meta->customer_id = $model->id;
                $meta->meta_data_id = $select;
                $meta->meta_data_type_id = $mid;
                $meta->save();
            }
        }  
    }

    public function insertMetaData($cid, $mid, $mdid){
        $model = CustomerMeta::where('customer_id', $cid)
            ->where('meta_data_id', $mid)
            ->first();


        if(!$model){
            
            $new = new CustomerMeta;
            $new->customer_id = $cid;
            $new->meta_data_id = $mid;
            $new->meta_data_type_id = $mdid;
            $new->save();
        

        }

    } 

    public function selectToArray($select){
        $array = Array();

        if(is_array($select)){
            
            foreach($select as $item){
                if($this->isValidMeta($item))
                    $array[] = $item;    
            }
        }else{
            if($this->isValidMeta($select))
                    $array[] = $select; 
        }


        return $array;
    }

    public function elocuentToArray($model){
        $array = Array();

        if($model)    
            foreach($model as $item)
                $array[] = $item->id;    


        return $array;
    }    

    public function updateMetaDataFromSelect( $model, $mid, $select ){
        $newMeta = $this->selectToArray($select);
        
        // busco las metadata actuales
        $cm = CustomerMeta::select('id')
            ->where('customer_id', $model->id)
            ->where('meta_data_type_id', $mid)
            ->where('customer_id', $model->id)
            //->whereNotIn('meta_data_id', $select)
            ->get();

        // eliminar las actuales  que no están en la lista

        if($cm){
            CustomerMeta::destroy($this->elocuentToArray($cm));
        }
        
        foreach($newMeta as $item){
            $this->insertMetaData($model->id, $item, $mid);
        }  
        
    }    
        
    public function isValidMeta($meta){
        $valid = true;
        if($meta == "Seleccione...")
            $valid = false;
        return $valid;
    }
    public function storeFromRequest(Request $request){

        $model = new Customer;
        $model->name = $request->name;
        $model->document = $request->document;
        $model->position = $request->position;
        $model->business = $request->business;
        $model->phone = $request->phone;
        $model->phone2 = $request->phone2;
        $model->email = $request->email;
        $model->notes = $request->notes;

        $model->pathology = $request->pathology;
        $model->total_sold = $request->total_sold;
        $model->bought_products = $request->bought_products;
        
        $model->hobbie = $request->hobbie;
        $model->facebook_url = $request->facebook_url;
        $model->instagram_url = $request->instagram_url;

        $model->kpi = $request->kpi;
        $model->address = $request->address;
        $model->city = $request->city;
        $model->country = $request->country;
        $model->department = $request->department;
        $model->bought_products = $request->bought_products;
        $model->status_id = $request->status_id;
        $model->user_id = $request->user_id;
        $model->source_id = $request->source_id;
        $model->technical_visit = $request->technical_visit;
        $model->project_id = $request->project_id;
        $model->birthday = $request->birthday;
        //$model->first_installment_date = $request->first_installment_date;

        //datos de contacto
        $model->contact_name = $request->contact_name;
        $model->contact_phone2 = $request->contact_phone2;
        $model->contact_email = $request->contact_email;
        $model->contact_position = $request->contact_position;
        $model->scoring = $request->scoring;

        if(isset($request->meta_gender_id)&& ($this->isValidMeta($request->meta_gender_id)))
            $model->meta_gender_id = $request->meta_gender_id;
        if(isset($request->meta_economic_activity_id)&& ($this->isValidMeta($request->meta_economic_activity_id)))
            $model->meta_economic_activity_id = $request->meta_economic_activity_id;
        if(isset($request->meta_income_id)&& ($this->isValidMeta($request->meta_income_id)))
            $model->meta_income_id = $request->meta_income_id;
        /*
        if(isset($request->meta_investment_id)&& ($this->isValidMeta($request->meta_investment_id)))
            $model->meta_investment_id = $request->meta_investment_id;
        */



        if ($model->save()) {
            /*
            $this->storeMetaDataFromSelect($model, 1, $request->meta_house_mates_id);
            $this->storeMetaDataFromSelect($model, 2, $request->meta_funding_source_id);
            $this->storeMetaDataFromSelect($model, 3, $request->meta_final_fundig_source_id);
            */

            //$this->sendWelcomeMail($model);

            //$this->sendMail(1, $model);
            return redirect('customers/'.$model->id.'/show')->with('status', 'El Cliente <strong>' . $model->name . '</strong> fué añadido con éxito!');
        }
       // dd( $model );
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //    $count = $this->isEqual($request);
        $similar = $this->getSimilar($request);
        
        if (($similar == null) || ($similar->count() == 0)) 
            
            return $this->storeFromRequest($request);
        else
            return redirect('/customers/'.$similar[0]->id.'/show');
        
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        $model = Customer::find($id);
        $actions = Action::where('customer_id', '=', $id)->orderby("created_at", "DESC")->get();
        $action_options = ActionType::all();
        $histories = CustomerHistory::where('customer_id', '=', $id)->get();
        $email_options = Email::all();
        $statuses_options = CustomerStatus::orderBy("weight", "ASC")->get();
        $actual = true;
        $today = Carbon\Carbon::now();

        $pending_action = Action::find($request->pending_action_id);
        

        return view('customers.show', compact('model', 'histories', 'actions', 'action_options', 'email_options', 'statuses_options', 'actual', 'today', 'pending_action'));
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Customer::find($id);
        $customer_statuses = CustomerStatus::orderBy("stage_id", "ASC")->orderBy("weight", "ASC")->get();
        $customer_sources = CustomerSource::all();
        $users = User::all();

        $projects = Project::all();



        return view('customers.edit', compact('model', 'customer_statuses',  'users', 'customer_sources', 'projects'));
    }


    public function assignMe($id)
    {
        $model = Customer::find($id);
        if (is_null($model->user_id) || $model->user_id == 0) {
            $user =  Auth::id();
            $model->user_id = $user;
            $model->save();
        }
        return back();
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
        $model = Customer::find($id);


        $cHistory = new CustomerHistory;
        $cHistory->saveFromModel($model);

        $model->name = $request->name;
        $model->document = $request->document;
        $model->position = $request->position;
        $model->business = $request->business;
        $model->phone = $request->phone;
        $model->email = $request->email;
        $model->notes = $request->notes;

        $model->pathology = $request->pathology;
        $model->hobbie = $request->hobbie;
        $model->facebook_url = $request->facebook_url;
        $model->instagram_url = $request->instagram_url;

        
        $model->kpi = $request->kpi;
        $model->phone2 = $request->phone2;
        $model->department = $request->department;
        $model->address = $request->address;
        $model->city = $request->city;
        $model->country = $request->country;
        $model->technical_visit = $request->technical_visit;
        $model->bought_products = $request->bought_products;
        $model->total_sold = $request->total_sold;
        
        $model->user_id = $request->user_id;
        $model->source_id = $request->source_id;
        $model->status_id = $request->status_id;
        $model->project_id = $request->project_id;
        $model->birthday = $request->birthday;
        $model->scoring = $request->scoring;
        //$model->first_installment_date = $request->first_installment_date;


        //$model->meta->gender_id = $request->meta_gender_id;



        //datos de contacto
        $model->contact_name = $request->contact_name;
        $model->contact_phone2 = $request->contact_phone2;
        $model->contact_email = $request->contact_email;
        $model->contact_position = $request->contact_position;

        if(isset($request->meta_gender_id)&& ($this->isValidMeta($request->meta_gender_id)))
            $model->meta_gender_id = $request->meta_gender_id;
        if(isset($request->meta_economic_activity_id)&& ($this->isValidMeta($request->meta_economic_activity_id)))
            $model->meta_economic_activity_id = $request->meta_economic_activity_id;
        if(isset($request->meta_income_id)&& ($this->isValidMeta($request->meta_income_id)))
            $model->meta_income_id = $request->meta_income_id;
        /*
        if(isset($request->meta_investment_id)&& ($this->isValidMeta($request->meta_investment_id)))
            $model->meta_investment_id = $request->meta_investment_id;
        */

        if ($model->save()) {
            /*
            $this->updateMetaDataFromSelect($model, 1, $request->meta_house_mates_id);
            $this->updateMetaDataFromSelect($model, 2, $request->meta_funding_source_id);
            $this->updateMetaDataFromSelect($model, 3, $request->meta_final_fundig_source_id);
            */



            return redirect('/customers/'.$id.'/show')->with('statusone', 'El Cliente <strong>' . $model->name . '</strong> fué modificado con éxito!');
        }
    }

    public function storeActionText($customer_id, $action_id, $action_note)
    {

        $model = new Action;
        $model->note = $action_note;
        $model->type_id = $action_id; 


        $model->customer_id = $customer_id;

        $model->save();

        return back();
    }
    
    public function activate($id)
    {
        $model = Customer::find($id);


        $cHistory = new CustomerHistory;
        $cHistory->saveFromModel($model);

        $model->status_id = 2; // seguimiento
        if($model->scoring==0)
            $model->scoring = 1;
        

        if ($model->save()) {
            $this->storeActionText($id, 4, 'Se activó desde un email');
            return redirect('https://trujillogutierrez.com.co/');
        }
    }
    public function unsubscribe($id)
    {
        $model = Customer::find($id);


        $cHistory = new CustomerHistory;
        $cHistory->saveFromModel($model);

        $model->status_id = 9; // descartado
        

        if ($model->save()) {
            $this->storeActionText($id, 4, 'Se dio de baja desde un email');
            return redirect('https://trujillogutierrez.com.co/');
        }
    }
    // Color



    public function filterCustomers($request)
    {
        return Customer::where(
                function ($query) use ($request) {
                    if (sizeof($request->status_id))
                        $query->where('customers.status_id', "=", $request->status_id);
                }
            )
            ->select(DB::raw('customers.*'))

            ->orderBy('customers.status_id', 'asc', 'created_at', 'asc')
            ->paginate(20);
    }

    function getStatusID($request, $stage_id)
    {
        $url = $request->fullurl();
        $paramenters = explode("&", $url);
        $res = array();
        foreach ($paramenters as $key => $value) {
            if (strpos($value, "status_id") !== false && (str_replace("status_id=", "", $value) != 0)) {
                $res[] = str_replace("status_id=", "", $value);
            }
        }
        if (!count($res)) {

            $model = CustomerStatus::where("stage_id", $stage_id)
                ->orderBy("weight", "ASC")
                ->get();
            //$model = CustomerStatus::all();

            foreach ($model as $item)
                $res[] = $item->id;
        }

        return $res;
    }
    // Enviar email
    public function sendMail($id, $user)
    {
        $model = Email::find($id);
        $subjet = 'Gracias por escribirnos';


        Email::raw($model->body, function ($message) use ($user, $subjet) {
            $message->from('noresponder@mqe.com.co', 'Maquiempanadas');

            $message->to($user->email, $user->user_name)->subject($subjet);
        });
    }

    public function mail($cui)
    {
        //$model = Email::find(1);
        $customer = Customer::find($cui);
        $subjet = 'Bro';

        //dd($customer);
        /*
    Mail::raw($model->body, function ($message) use ($customer, $subjet){
        $message->from('noresponder@mqe.com.co', 'Maquiempanadas');

        $message->to($customer->email, $customer->user_name)->subject($subjet);   
    });
*/

        $emailcontent = array(
            'subject' => 'Gracias por contactarme',
            'emailmessage' => 'Este es el contenido',
            'customer_id' => $cui
        );

        //dd($emailcontent);
        // Mail::send('emails.brochure', $emailcontent, function ($message) use ($customer){

        //         $message->subject('MQE');

        //         $message->to('nicolas@myseocompany.co');

        //     });


    }

    function getAllStatusID()
    {

        $res = array();
        $model = CustomerStatus::orderBy("weight", "ASC")->get();
        //$model = CustomerStatus::all();

        foreach ($model as $item)
            $res[] = $item->id;
        return $res;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Customer::find($id);
        $model->actions()->delete();

        if ($model->delete()) {

            return redirect('customers')->with('statustwo', 'El Cliente <strong>' . $model->name . '</strong> fué eliminado con éxito!');
        }
    }

    public function saveAPICustomer($request)
    {

        $model = new Customer;
        $model->name        = $request->name;
        $model->phone       = $request->phone;
        $model->phone2      = $request->phone2;
        $model->email       = $request->email;
        $model->country     = $request->country;
        $model->city        = $request->city;
        $model->project_id  = $request->project_id;
        if(is_int($model->source_id ))
            $model->source_id  = $request->source_id;
        
        if(isset($request->platform) && ($request->platform == 'fb'))
        $model->source_id = 6;
        elseif(isset($request->platform) && ($request->platform == 'ig'))
        $model->source_id = 40;


        $model->notes       = $request->notes . ' ' . $request->email;


        $model->bought_products = $request->bought_products;
        $model->cid = $request->cid;
        $model->src = $request->src;
        $model->department = $request->department;
        $model->status_id = 1;



        $model->save();

        return $model;
    }



    public function isEqual($request)
    {
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) {
                if (isset($request->user_id)  && ($request->user_id != null))
                    $query->where('user_id', $request->user_id);

                if (isset($request->source_id)  && ($request->source_id != null))
                    $query->where('source_id', $request->source_id);

                if (isset($request->status_id)  && ($request->status_id != null))
                    $query->where('status_id', $request->status_id);

                if (isset($request->business)  && ($request->business != null))
                    $query->where('business', $request->business);

                if (isset($request->phone)  && ($request->phone != null))
                    $query->where('phone', $request->phone);

                if (isset($request->email)  && ($request->email != null))
                    $query->where('email', $request->email);

                if (isset($request->phone2)  && ($request->phone2 != null))
                    $query->where('phone2', $request->phone2);

                if (isset($request->notes)  && ($request->notes != null))
                    $query->where('notes', $request->notes);

                if (isset($request->city)  && ($request->city != null))
                    $query->where('city', $request->city);

                if (isset($request->country)  && ($request->country != null))
                    $query->where('country', $request->country);
            }
        )
            ->count();
        return $model;
    }

    public function getSimilar($request)
    {
        if(
            (isset($request->phone)  && ($request->phone != null)) ||
            (isset($request->phone2)  && ($request->phone2 != null)) ||
            (isset($request->email)  && ($request->email != null))

        ){
            $model = Customer::where(
                // Búsqueda por...
                function ($query) use ($request) {
                    if (isset($request->phone)  && ($request->phone != null))
                        $query->orwhere('phone', $request->phone);
                    if (isset($request->phone)  && ($request->phone != null))
                        $query->orwhere('phone2', $request->phone);

                    if (isset($request->phone2)  && ($request->phone2 != null))
                        $query->orwhere('phone', $request->phone2);
                    if (isset($request->phone2)  && ($request->phone2 != null))
                        $query->orwhere('phone2', $request->phone2);

                    if (isset($request->email)  && ($request->email != null))
                        $query->orwhere('email', $request->email);
                }
            )
                ->get();
        }else{
            $model = null;
        }
           
        return $model;
    }

    public function getEmailByProjectId($project_id)
    {

        $project_id_laquinta = '1';
        $project_id_torres = '2';


        $email_id_laquinta = 2;
        $email_id_torres = 4;

        $email_id = 0;


        switch ($project_id) {
            case $project_id_laquinta:
                $email_id = $email_id_laquinta;
                break;
            case $project_id_torres:
                $email_id = $email_id_torres;
                break;
        }

        return $email_id;
    }

    public function sendWelcomeMail($customer)
    {
        $email_id = $this->getEmailByProjectId($customer->project_id);


        if ($email_id != 0) {
            $email = Email::find($email_id);
            // $email, $user, $count, $sended_at
            Email::addEmailQueue($email, $customer, 0, Carbon\Carbon::now());
            $this->storeEmailAction($email, $customer, "Correo automático de bienvenida");
        }
    }

    public function redirectingPage1()
    {

        return redirect('https://trujillogutierrez.com.co/site/gracias-la-quinta.html');
    }

    public function redirectingPage2()
    {

        return redirect('https://trujillogutierrez.com.co/site/gracias-torres-del-bosque.html');
    }

    public function saveAPI(Request $request)
    {
        
        //Customer::create($request->all());
        $count = $this->isEqual($request);
        
        if (is_null($count) || ($count == 0)) {
            $similar = $this->getSimilar($request);


            if ($similar->count() == 0) {

                $model = $this->saveAPICustomer($request);

                $this->sendWelcomeMail($model);
                if($request->project_id=='1'){
                    return $this->redirectingPage1();
                }else{
                    return $this->redirectingPage2();
                }
            }
            // este cliente ya existe. Se agrega una nueva nota
            else {

                $model = $similar[0];


                $this->storeActionAPI($request, $model->id);
                $this->updateCreateDate($request, $model->id);
                if($request->project_id=='1'){
                    return $this->redirectingPage1();
                }else{
                    return $this->redirectingPage2();
                }
            }
        } else {


            $similar = $this->getSimilar($request);


            $model = $similar[0];


            $this->storeActionAPI($request, $model->id);
            $this->updateCreateDate($request, $model->id);
            if($request->project_id=='1'){
                return $this->redirectingPage1();
            }else{
                return $this->redirectingPage2();
            }
        }
        if($request->project_id=='1'){
            return $this->redirectingPage1();
        }else{
            return $this->redirectingPage2();
        }
        
    }

    public function storeActionAPI(Request $request, $customer_id)
    {

        $model = new Action;

        $str = "";
        if (isset($request->phone))
            $str .= " telefono1:" . $request->phone;
        if (isset($request->phone2))
            $str .= " telefono2:" . $request->phone2;
        if (isset($request->email))
            $str .= " email:" . $request->email;
        if (isset($request->city))
            $str .= " ciudad:" . $request->city;
        if (isset($request->country))
            $str .= " pais:" . $request->country;

        if (isset($request->name))
            $str .= " Nombre:" . $request->name;

        $model->note = $request->notes . $str;
        $model->type_id = 16; // actualización


        $model->customer_id = $customer_id;

        $model->save();

        return back();
    }


    public function updateCreateDate(Request $request, $customer_id)
    {

        $customer = Customer::find($customer_id);
        $model = new Action;


        $model->note = "se actualizó la fecha de creación " . $customer->created_at;
        $model->type_id = 16; // actualización
        $model->customer_id = $customer_id;
        $model->save();


        $mytime = Carbon\Carbon::now();
        $customer->created_at = $mytime->toDateTimeString();
        $customer->status_id = 19;
        $customer->save();


        return back();
    }

    public function storeEmailAction($mail, $customer, $note)
    {
        $today = Carbon\Carbon::now();
        // envio mail
        $action_type_id = 2;

        $model = new Action;

        $model->note = $note;
        $model->type_id = $action_type_id;
        $model->creator_user_id = 0;
        $model->customer_id = $customer->id;


        $model->delivery_date = $today;
        $model->save();
    }

    public function changeCustomerStatus($request, $customer){
        if (!is_null($request->status_id)) {
            $cHistory = new CustomerHistory;
            $cHistory->saveFromModel($customer);
            $customer->status_id = $request->status_id;
            $customer->save();
        }    
    }

    public function changeCustomerScoring($request, $customer)
    {
        if (!is_null($request->scoring)) {
            $cHistory = new CustomerHistory;
            $cHistory->saveFromModel($customer);            
            $customer->scoring = $request->scoring;
            $customer->save();
        }
    }
    
    public function createNewAction($request){
        $due_date = Carbon\Carbon::parse($request->due_date);

        $model = new Action;

        $model->type_id = $request->type_id;
        $model->creator_user_id = Auth::id();
        $model->customer_id = $request->customer_id;
        $model->note = $request->note;
        
        if (isset($request->due_date)&&($request->due_date!="")) {
            $model->due_date = $due_date;
        }
        $model->save();
    }

    public function updateAction($request){
        $today = Carbon\Carbon::now();

        $model = Action::find($request->pending_action_id);

        $model->type_id = $request->type_id;
        $model->creator_user_id = Auth::id();
        //$model->customer_id = $request->customer_id;
        $model->note = $model->note. " / ". $request->note;
        $model->delivery_date = $today; 
        $model->save();  
        
    }
    public function storeAction(Request $request)
    {  
        //dd($today);
        $customer = Customer::find($request->customer_id);
        if (is_null($request->type_id)) {
            return back()->with('statustwo', 'El Cliente <strong>' . $customer->name . '</strong> no fue modificado!');
        }
        if(!isset($request->pending_action_id))
            $this->createNewAction($request);
        else
            $this->updateAction($request);
        
        $this->changeCustomerStatus($request, $customer);

        $this->changeCustomerScoring($request, $customer);
        
                
        return redirect('/customers/' . $request->customer_id . '/show')->with('statusone', 'El Cliente <strong>' . $customer->name . '</strong> fué modificado con éxito!');
    }

    public function storeMail(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        $email = Email::find($request->email_id);

        $count = Email::sendUserEmail($request->customer_id, $email->subject, $email->view, $email->id);  
        if($count>0){
            Action::saveActionManually($request->customer_id, $request->email_id, 5);
        }else{
            Action::saveActionManually($request->customer_id, $request->email_id, 2);
        } 
        
        //Email::addEmailQueue($email, $customer, 0, Carbon\Carbon::now());
        /*
	    $emailcontent = array (
			'subject' => $email->subject,
			'emailmessage' => 'Este es el contenido',
			'customer_id' => $model->id,
			'email_id' => $email->id,
            'model' => $model,
			 );
        
        Mail::send($email->view, $emailcontent, function ($message) use ($model, $email){
                    $message->subject($email->subject);
                    $message->to($model->email);
            });
        */
        //Action::saveAction($customer->id,$email->id, 2);
        return back();
    }  
}