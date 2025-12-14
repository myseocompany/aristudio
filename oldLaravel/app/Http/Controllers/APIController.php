<?php
//JYP
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Mail;

use App\Customer;
use App\CustomerStatus;
use App\AudienceCustomer;
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
use App\FincaRaiz;
use App\Product;
use App\Reference;
use App\Session;
use App\CustomerMeta;
use App\Sinco;
use App\Log;

class APIController extends Controller
{

    protected $attributes = ['status_name'];

    protected $appends = ['status_name'];

    protected $status_name;

    public function __construct(){    }

    public function index(Request $request)
    {
        return $this->customers($request);
    }

    


    /*******************
    Desarrollador: Nicolas Navarro
    Objeto: recibir datos de dialogflow

    ******************/
    public function opendialog(Request $request){
        
        $data = $request->json()->all();

        if(array_key_exists("queryResult", $data) && array_key_exists("action", $data["queryResult"])){
            $action =  $data["queryResult"]['action'];
            $return = "";
            switch ($action) {
                case 'saveCustomer':
                    $return = $this->saveCustomerDialogMsn($request);
                    break;
                case 'loadPrices':
                    $return = $this->loadPrices($request);
                    break;
                case 'loadLocation':
                    $return = $this->loadLocation($request);
                    break;
                
                default:
                    $return = $this->getDefault();
                    break;
            }
        }else{
            $return = $this->getJSON('no existe el objeto ["queryResult"]["action"]');
            //$return = $this->getDefault();
        }
        return $return;
    }
    public function getDefault(){
        return response()->json(array(
            "fulfillmentText" => 'Error: accion desconocida',
        ));    
    }
    public function getJSON($str){
        return response()->json(array(
            "fulfillmentText" => $str,
        ));    
    }

    public function saveCustomerDialogMsn(Request $request){
        $data = $request->json()->all();

        $params = $data["queryResult"]['parameters'];

        $action =  $data["queryResult"]['action'];
        $project_id  = $params["project_id"];


        $request->name        = $params["name"];
        $request->phone       = $params["phone"];
        $request->email       = $params["email"];
        
        $request->project_id  = $params["project_id"];

        if(isset($params["session"])){
            $request->session_id = $params["session"];
        }else{
            $request->session_id = $this->getSession($request);
        }

        //$request->source_id = $params["source_id"];; // FB Messenger

        $this->saveAPI($request);
        if($project_id==1){
            return response()->json(array(
                "fulfillmentMessages" => array(
                    $this->getQuickReplies('Proyecto Árnika  
 Seleccione una opción:
', array("Precios", "Ubicación", "Asesor 📲", "Menú principal")),
                )
            ));
        }else if($project_id==2){
            return response()->json(array(
                "fulfillmentMessages" => array(
                    $this->getQuickReplies('Proyecto Paseo del bosque  
 Seleccione una opción:
', array("Precios", "Ubicación", "Asesor 📲", "Menú principal")),
                )
            ));
        }else if($project_id==3){
            return response()->json(array(
                "fulfillmentMessages" => array(
                    $this->getQuickReplies('Proyecto Entreguaduas  
 Seleccione una opción:
', array("Precios", "Ubicación", "Asesor 📲", "Menú principal")),
                )
            ));
        }else{
            return response()->json(array(
                
                "fulfillmentMessages" => array(
                    $this->getFulfillmentText("Error")
                )
            ));
        }  
    }

    public function loadPrices(Request $request){
        $data = $request->json()->all();

        $params = $data["queryResult"]['parameters'];

        $action =  $data["queryResult"]['action'];
        $project_id  = $params["project_id"];

        $customer = null;

        if(isset($params["session"])){
            $session_id = $params["session"];
        }else{
            $session_id = $this->getSession($request);
        }

        if($project_id==1){
            return response()->json(array(
                "fulfillmentMessages" => array(
                    $this->getFulfillmentText("1️⃣ Tipo 1 / 70,30 m2
Desde $297,936,000.
2️⃣ Tipo 2 / 68,10 m2
Desde $280,777,000.
3️⃣ Tipo 3 / 68,60 m2
Desde $318,556,000.
4️⃣ Tipo 4 / 68,20 m2
Desde $286,188,000.
5️⃣ Tipo 5 / 42,80 m2
Desde $156,000,000.
6️⃣ Tipo 6 / 52,20 m2
Desde $258,713,000.
7️⃣ Tipo 7 / 70,00 m2
Desde $329,000,000.
8️⃣ Comunicarse con asesor 📲
0️⃣ Volver 

Si desea más información digite el número correspondiente"),  
                ),
                "outputContexts" => array(
                    $this->getOutputContexts("jypbotmsn-ewpl",$session_id,"validArnikaPricesMenu",$customer), 
                )
            ));
        }else if($project_id==2){
            return response()->json(array(
                "fulfillmentMessages" => array(
                    $this->getFulfillmentText("1️⃣ Tipo 1 / 96.93 m2
Desde $525,000,000.
2️⃣ Tipo 2 / 115.31 m2
Desde $653,000,000.
3️⃣ Tipo 3 / 89.35 m2
Desde $542,000,000.
4️⃣ Tipo 4 / 114.80 m2
Desde $717,000,000.
5️⃣ Tipo 5 / 114.80
Desde $717,000,000
6️⃣Tipo 6 / 89.35 m2
Desde $472,177,000
8️⃣ Comunicarse con asesor 📲
0️⃣ Volver 

Si desea más información digite el número correspondiente")
                ),
                "outputContexts" => array(
                    $this->getOutputContexts("jypbotmsn-ewpl",$session_id,"validBosquePricesMenu",$customer), 
                )
            ));
        }else if($project_id==3){
            return response()->json(array(
                "fulfillmentMessages" => array(
                    $this->getFulfillmentText("1️⃣ Tipo 1 / 54,98 m2 (3 alcs)
2️⃣ Tipo 2 / 48,34 m2 (2 alcs)
3️⃣ Comunicarse con asesor 📲
0️⃣ Volver 

Si desea más información digite el número correspondiente")
                ),
                "outputContexts" => array(
                    $this->getOutputContexts("jypbotmsn-ewpl",$session_id,"validEntreguaduasPricesMenu",$customer), 
                )
            ));
        }else{
            return response()->json(array(
                "fulfillmentMessages" => array(
                    $this->getFulfillmentText("Error")
                )
            ));
        }  
    }

    public function loadLocation(Request $request){
        $data = $request->json()->all();

        $params = $data["queryResult"]['parameters'];

        $action =  $data["queryResult"]['action'];
        $project_id  = $params["project_id"];

        $this->saveAPI($request);
        if($project_id==1){
            return response()->json(array(
                "fulfillmentMessages" => array(
                    $this->getFulfillmentText("Sector leonora."),
                    $this->getQuickReplies('¿Te puedo ayudar en algo más?', array("Volver al menú", "Asesor 📲")),
                )
            ));
        }else if($project_id==2){
            return response()->json(array(
                "fulfillmentMessages" => array(
                    $this->getFulfillmentText("Sector San Marcel Contiguo al bosque popular."),
                    $this->getQuickReplies('¿Te puedo ayudar en algo más?', array("Volver al menú", "Asesor 📲")),
                )
            ));

        }else if($project_id==3){
            return response()->json(array(
                "fulfillmentMessages" => array(
                    $this->getFulfillmentText("Avenida el Ferrocarril y Diagonal 66."),
                    $this->getQuickReplies('¿Te puedo ayudar en algo más?', array("Volver al menú", "Asesor 📲")),
                )
            ));
        }else{
            return response()->json(array(
                "fulfillmentMessages" => array(
                    $this->getFulfillmentText("Error")
                )
            ));
        }  
    }

    public function saveResponse(Request $request){
        $data = $request->json()->all();
        $params = $data["queryResult"]['parameters'];
        $action =  $data["queryResult"]['action'];
        $response = 0;
        if(isset($params["response"]))
            $response = $params["response"];
    
        $customer_id = "";

        $session_id = $this->getSession($request);
        $session = Session::where('session_id', $session_id)->first();
        if($session){
            $customer = Customer::find($session->customer_id);
            if($customer){
                $customer_id = $customer->id;  
            }
        }

        $model = new CustomerMeta;
        $model->meta_data_type_id = 35;
        $model->customer_id = $customer_id;
        $model->response = $response;
        $model->save();

        if($model){
            $texto0 = 'Para mi fue un gusto atenderlo. ¡Hasta luego!';
        }else{
            $texto0 = 'Error';
        }
        return response()->json(array(
            "fulfillmentMessages" => array(
                $this->getFulfillmentText($texto0),
            )
        ));
    }

    public function getSession(Request $request){
        $data = $request->json()->all();
        $name = $data["queryResult"]["outputContexts"][0]['name'];
        $start = strpos($name, "sessions/") + 9;
        $end = strpos($name, "/contexts");
        $str = substr($name, $start, ($end-$start));        
        return $str;
    }

    public function saveSession($customer_id, $session_id){
        $model = Session::where('session_id', $session_id)->first();
        if(!$model){
            $model = new Session;     
        }
        $model->session_id = $session_id;
        $model->customer_id = $customer_id;
        $model->save();
        return $model;
    }

    public function getMinPriceBlackWorkDosQuebradas(){
        $min_price_black_work = Product::where('status_id',5)
                                ->where('price_black_work','>',0)
                                ->whereIn('category_id',array(1,2,3))
                                ->whereNotNull('price_black_work')
                                ->min('price_black_work');
        return number_format($min_price_black_work);
    }
    public function getMinPriceBlackWork($category_id){
        $min_price_black_work = Product::where('status_id',5)
                                ->where('price_black_work','>',0)
                                ->where('category_id',$category_id)
                                ->whereNotNull('price_black_work')
                                ->min('price_black_work');
        return number_format($min_price_black_work);
    }

    public function getMinPriceSemiFinished($category_id){
        $min_price_semi_finished = Product::where('status_id',5)
                                ->where('price_semi_finished','>',0)
                                ->where('category_id',$category_id)
                                ->whereNotNull('price_semi_finished')
                                ->min('price_semi_finished');
        return number_format($min_price_semi_finished);
    }

    public function getMinPriceFullyFinished($category_id){
        $min_price_fully_finished = Product::where('status_id',5)
                                ->where('price_fully_finished','>',0)
                                ->where('category_id',$category_id)
                                ->whereNotNull('price_fully_finished')
                                ->min('price_fully_finished');
        return number_format($min_price_fully_finished);
    }

    public function getMinArea($category_id){
        $min_built_area = Product::where('status_id',5)
                                ->where('built_area','>',0)
                                ->where('category_id',$category_id)
                                ->whereNotNull('built_area')
                                ->min('built_area');
        return number_format($min_built_area);
    }

    public function getMinAreaDosQuebradas(){
        $min_built_area = Product::where('status_id',5)
                                ->where('built_area','>',0)
                                ->whereIn('category_id',array(1,2,3))
                                ->whereNotNull('built_area')
                                ->min('built_area');
        return number_format($min_built_area);
    }


    public function getFulfillmentText($str){

        return array(
                    "text" => array(
                        "text"=>array($str),
                        
                    ),
                    
                );
    }

    public function getQuickReplies($title, $messages){
        return array(
            "quickReplies" => array(
                "title"=>$title,
                    "quickReplies" => $messages
                )   
            );
    }

    public function getOutputContexts($projects, $sessions, $contexts, $model){
        //dd($sessions);
        $phone = ""; 
        $email = ""; 
        $country = ""; 
        $customer_name = "";
        if($model != null){
            $phone=$model->phone;
            $email=$model->email;
            $country=$model->country;
            $customer_name=$model->name;
        }

        $name = "projects/" . $projects . "/agent/sessions/" . $sessions . "/contexts/" . $contexts;
        return array(
            "name"=>$name,
            'lifespanCount' => 5,
                'parameters' => array(
                    'phone' => $phone,
                    'email' => $email,
                    'country' => $country,
                    'name' => $customer_name,
                )
        );
    }

    /***********Fin de Dialog flow***********/

    public function getPendingActions()
    {
        $model = Action::whereNotNull('due_date')
            ->whereNull('delivery_date')
            ->where('creator_user_id', "=", Auth::id())
            ->get();
        //dd($model);
        return $model;
    }

    public function customers(Request $request)
    {
        $users = $this->getUsers();
        $customer_options = CustomerStatus::all();
        $statuses = $this->getStatuses($request, 1);

        $model = $this->getModel($request, $statuses, 'customers');
        $customersGroup = $this->countFilterCustomers($request, $statuses);

        $projects = Project::all();

        $sources = CustomerSource::orderby('name')->get();

        $pending_actions = $this->getPendingActions();
        //dd($pending_actions);

        return view('customers.index', compact('model', 'request', 'customer_options', 'customersGroup', 'query', 'users', 'sources', 'projects', 'pending_actions'));
    }

    public function leads(Request $request)
    {
        $users = $this->getUsers();
        $customer_options = CustomerStatus::all();
        $statuses = $this->getStatuses($request, 1);
        $model = $this->getModel($request, $statuses, 'leads');
        $customersGroup = $this->countFilterCustomers($request, $statuses);
        $projects = Project::all();
        $pending_actions = $this->getPendingActions();


        $sources = CustomerSource::all();


        return view('customers.index', compact('model', 'request', 'customer_options', 'customersGroup', 'query', 'users', 'sources', 'projects', 'pending_actions'));
    }

    public function getModel(Request $request, $statuses, $action)
    {
        $model = $this->filterModel($request, $statuses);


        $model->getActualRows = $model->currentPage() * $model->perPage();

        if ($model->perPage() > $model->total())
            $model->getActualRows = $model->total();
        foreach ($model as $items) {
            if (isset($items->status_id)) {
                $status = CustomerStatus::find($items->status_id);
                if (isset($status))
                    $items->status_name = $status->name;
            }
        }
        $model->action = $action;
        return $model;
    }

    public function getUsers()
    {
        return  User::orderBy('name')
            ->where('users.status_id', 5)
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


    public function filterModel(Request $request, $statuses)
    {

        //        $model = Customer::wherein('customers.status_id', $statuses)
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) {

                if (isset($request->from_date) && ($request->from_date != null)) {
                    if (isset($request->user_id)  && ($request->user_id != null))
                        $query = $query->whereBetween('updated_at', array($request->from_date, $request->to_date));
                    else
                        $query = $query->whereBetween('created_at', array($request->from_date, $request->to_date));
                }
                if (isset($request->user_id)  && ($request->user_id != null))
                    $query = $query->where('user_id', $request->user_id);
                if (isset($request->source_id)  && ($request->source_id != null))
                    $query = $query->where('source_id', $request->source_id);
                if (isset($request->project_id)  && ($request->project_id != null))
                    $query = $query->where('project_id', $request->project_id);
                if (isset($request->kpi)  && ($request->kpi != null))
                    $query = $query->where('kpi', $request->kpi);
                if (isset($request->status_id)  && ($request->status_id != null))
                    $query = $query->where('status_id', $request->status_id);
                if (isset($request->project_id)  && ($request->project_id != null))
                    $query = $query->where('project_id', $request->project_id);
                if (isset($request->search)) {
                    $query = $query->where(
                        function ($innerQuery) use ($request) {
                            $innerQuery = $innerQuery->orwhere('customers.name', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.email',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.document', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.position', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.business', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.phone',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.phone2',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.notes',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.city',    "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.country', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.bought_products', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.status_temp', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_name', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_phone2', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_email', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_position', "like", "%" . $request->search . "%");
                        }
                    );
                }
            }


        )
            ->orderBy('status_id', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate();

        return $model;
    }

    public function filterModelFull(Request $request, $statuses)
    {

        //        $model = Customer::wherein('customers.status_id', $statuses)
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) {

                if (isset($request->from_date) && ($request->from_date != null)) {
                    if (isset($request->user_id)  && ($request->user_id != null))
                        $query = $query->whereBetween('updated_at', array($request->from_date, $request->to_date));
                    else
                        $query = $query->whereBetween('created_at', array($request->from_date, $request->to_date));
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

    public function countFilterCustomers($request, $statuses)
    {
        //$customersGroup = Customer::wherein('customers.status_id', $statuses)

        $customersGroup = Customer::wherein('customers.status_id', $statuses)
            ->rightJoin("customer_statuses", 'customers.status_id', '=', 'customer_statuses.id')
            ->where(
                // Búsqueda por...
                function ($query) use ($request) {
                    if (isset($request->from_date) && ($request->from_date != null)) {
                        if (isset($request->user_id)  && ($request->user_id != null))
                            $query = $query->whereBetween('customers.updated_at', array($request->from_date, $request->to_date));
                        else
                            $query = $query->whereBetween('customers.created_at', array($request->from_date, $request->to_date));
                    }
                    if (isset($request->user_id)  && ($request->user_id != null))
                        $query = $query->where('customers.user_id', $request->user_id);
                    if (isset($request->source_id)  && ($request->source_id != null))
                        $query = $query->where('customers.source_id', $request->source_id);
                    if (isset($request->status_id)  && ($request->status_id != null))
                        $query = $query->where('customers.status_id', $request->status_id);

                    if (isset($request->project_id)  && ($request->project_id != null))
                        $query = $query->where('project_id', $request->project_id);
                    if (isset($request->kpi)  && ($request->kpi != null))
                            $query = $query->where('kpi', $request->kpi);
                        $query = $query->where(
                        function ($innerQuery) use ($request) {
                            $innerQuery = $innerQuery->orwhere('customers.name', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.email',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.document', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.position', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.business', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.phone',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.phone2',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.notes',   "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.city',    "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.country', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.bought_products', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.status_temp', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_name', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_phone2', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_email', "like", "%" . $request->search . "%");
                            $innerQuery = $innerQuery->orwhere('customers.contact_position', "like", "%" . $request->search . "%");
                        }
                    );
                }
            )
            ->select(DB::raw('customers.status_id as status_id, count(customers.id) as count'))
            ->groupBy('status_id')
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
        $customers_statuses = CustomerStatus::all();
        $customer_sources = CustomerSource::all();
        $projects = Project::all();
        return view('customers.create', compact('customers_statuses', 'users', 'customer_sources', 'customersGroup', 'projects'));
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

        //datos de contacto
        $model->contact_name = $request->contact_name;
        $model->contact_phone2 = $request->contact_phone2;
        $model->contact_email = $request->contact_email;
        $model->contact_position = $request->contact_position;




        if ($model->save()) {

            $this->sendWelcomeMail($model);

            //$this->sendMail(1, $model);
            return redirect('customers/'.$model->id.'/show')->with('status', 'El Cliente <strong>' . $model->name . '</strong> fué añadido con éxito!');
        }
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
        
        if ($similar->count() == 0) 
            
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
        $customer_statuses = CustomerStatus::orderBy("weight", "ASC")->get();
        $customer_sources = CustomerSource::all();
        $users = User::all();

        $projects = Project::all();



        return view('customers.edit', compact('model', 'customer_statuses', 'customersGroup', 'users', 'customer_sources', 'projects'));
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
        $model->kpi = $request->kpi;
        $model->phone2 = $request->phone2;
        $model->department = $request->department;
        $model->address = $request->address;
        $model->city = $request->city;
        $model->country = $request->country;
        $model->technical_visit = $request->technical_visit;
        $model->bought_products = $request->bought_products;
        $model->user_id = $request->user_id;
        $model->source_id = $request->source_id;
        $model->status_id = $request->status_id;
        $model->project_id = $request->project_id;


        //datos de contacto
        $model->contact_name = $request->contact_name;
        $model->contact_phone2 = $request->contact_phone2;
        $model->contact_email = $request->contact_email;
        $model->contact_position = $request->contact_position;

        if ($model->save()) {
            return redirect('leads')->with('statusone', 'El Cliente <strong>' . $model->name . '</strong> fué modificado con éxito!');
        }
    }

    // Color



    public function filterCustomers($request)
    {
        return Customer::where(
                function ($query) use ($request) {
                    if (sizeof($request->status_id))
                        $query = $query->where('customers.status_id', "=", $request->status_id);
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
            $message->from('ventas@constructorajyp.com', 'Constructora JYP');

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
        if ($model->delete()) {
            return redirect('customers')->with('statustwo', 'El Cliente <strong>' . $model->name . '</strong> fué eliminado con éxito!');
        }
    }

    public function saveLogFromRequest(Request $request){
        $model = new Log();
        $model->request = json_encode($request->all());
        $model->save();
    }
    

    public function saveAPICustomer($request, $model){

        

        $model->name        = $request->name;
        $model->phone       = $request->phone;
        $model->phone2      = $request->phone2;
        $model->email       = $request->email;
        $model->country     = $request->country;
        $model->city        = $request->city;
        $model->notes        = $request->notes;
        

        if (isset($request->interes)){
            if($request->interes == "Entreguaduas"){
                $model->project_id = 3;
            }else if($request->interes == "Mirador de Estambul"){
                $model->project_id = 2;
            }else if($request->interes == "Arnika"){
                $model->project_id = 1;
            }
            else if($request->interes == "Paseo del Bosque" || $request->interes == "Paseo del Bosque Torre II"){
                $model->project_id = 4;
            }
            else if($request->interes == "Otro"){
            }
        }

        if (isset($request->project_id)){
            if($request->project_id == 3){
                $model->project_id = 3;
            }else if($request->project_id == 2){
                $model->project_id = 2;
            }else if($request->project_id == 1){
                $model->project_id = 1;
            }
            else if($request->project_id == 4){
                $model->project_id = 4;
            }
            else if($request->project_id == ""){
            }
        }




        $model->rooms  = $request->rooms;
        
        $model->source_id  = 10;
        
        if (isset($request->source_id)){
         $model->source_id  = $request->source_id;
        }



        if( ($request->source_id != null)  && (intval($request->source_id )!=0))
            $model->source_id  = $request->source_id;
        else{
            if(isset($request->source_id) ) {
                switch ($request->source_id) {
                    case 'ig':
                        $model->source_id = 40;
                        break;
                    case 'fb':
                        $model->source_id = 6;
                        break;
                    default:
                        break;
                }
            }
        }

        $model->bought_products = $request->product;
        $model->cid = $request->cid;
        $model->src = $request->src;
        if(isset($request->time_to_buy))
            $model->notes .= "tiempo: " . $request->time_to_buy." | nuevo: ".$request->new_or_used. " | presupuesto familiar: ".$request->family_income." | presupuesto->casa: ".$request->budget."  ".$request->notes;    
        
        $model->department = $request->department;
        
        if(isset($request->session_id))
            $model->session_id = $request->session_id;
        
        

        /*UTM - Campañas Facebook Ads*/
        if(isset($request->ad_name) && $request->ad_name != ""){
            $model->ad_name = $request->ad_name;
        }
        if(isset($request->adset_name) && $request->adset_name != ""){
            $model->adset_name = $request->adset_name;
        }
        if(isset($request->campaign_name) && $request->campaign_name != ""){
            $model->campaign_name = $request->campaign_name;
        }
        if(isset($request->form_name) && $request->form_name != ""){
            $model->form_name = $request->form_name;
        }
        if(isset($request->source_name) && $request->source_name != ""){
            $model->source_name = $request->source_name;
        }
        





        $model->save();
        $this->saveSession($model->id, $request->session_id);
        /*
        $this->sendEmail($model);
        */
        // guardo una acción
        $this->addAction($model);
        // envio mail al usuario administrador desde la plantilla 1
        $model->project = $model->interes;
        
        //Email::notifyByEmail($model, 31, "Se ha creado un nuevo prospecto", "emails.email1", 1);

        return $model;
    }


   

    public function addAction($model){
        
        $action = new Action;
        $action->note = ":Solicitó datos desde ";
        if(isset($model->source))
            $action->note .= $model->source->name;
        $action->note .= " el " . $model->created_at;

        $action->type_id = $this->customerSourceToActionType($model->source_id); // visita web
        //$action->note .= "fuente ".$model->source_id." accion " . $action->type_id;
        $action->customer_id = $model->id;
        $action->save();

    }

    public function customerSourceToActionType($source_id){
        $action_id = 0;
        switch ($source_id) {
            case 10: //  Web
                $action_id = 3;
                break;
            case 12: // Whatsapp
                $action_id = 8;
                break;
            case 15: // Calculadora Web
                $action_id = 3;
                break;
            case 26: // Whatsapp
                $action_id = 8;
                break;
            case 33: // redes sociales
                $action_id = 6;
                break;
            case 6: // fb lead
                $action_id = 6;
                break;
            case 39: // fb Messenger
                $action_id = 6;
                break;
            case 40: // ínstagram
                $action_id = 7;
                break;
            case 41: // chat bot
                $action_id = 6;
                break;
            
            
        }
        return $action_id;
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
            ->first();
        return $model;
    }

    public function isEqualCount($request){
        //dd($request);
        $model = Customer::where(
                // Búsqueda por...
                 function ($query) use ($request) {
                    if(isset($request->user_id)  && ($request->user_id!=null))
                        $query = $query->where('user_id', $request->user_id);
 
                    if(isset($request->source_id)  && ($request->source_id!=null))
                        $query = $query->where('source_id', $request->source_id);

                    if(isset($request->status_id)  && ($request->status_id!=null))
                        $query = $query->where('status_id', $request->status_id);
                    
                    if(isset($request->business)  && ($request->business!=null))
                        $query = $query->where('business', $request->business);

                    if(isset($request->phone)  && ($request->phone!=null))
                        $query = $query->where('phone', $request->phone);

                    if(isset($request->email)  && ($request->email!=null))
                        $query = $query->whereRaw('lower(email) = lower("'.$request->email.'")');

                    if(isset($request->phone2)  && ($request->phone2!=null))
                        $query = $query->where('phone2', $request->phone2);

                    if(isset($request->notes)  && ($request->notes!=null))
                        $query = $query->where('notes', $request->notes);

                    if(isset($request->city)  && ($request->city!=null))
                        $query = $query->where('city', $request->city);

                    if(isset($request->country)  && ($request->country!=null))
                        $query = $query->where('country', $request->country);

                })
            ->count();
        return $model;
    }

    public function getSimilar($request)
    {
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) {
                if (isset($request->phone)  && ($request->phone != null))
                    $query = $query->orwhere('phone', $request->phone);
                if (isset($request->phone)  && ($request->phone != null))
                    $query = $query->orwhere('phone2', $request->phone);

                if (isset($request->phone2)  && ($request->phone2 != null))
                    $query = $query->orwhere('phone', $request->phone2);
                if (isset($request->phone2)  && ($request->phone2 != null))
                    $query = $query->orwhere('phone2', $request->phone2);

                if (isset($request->email)  && ($request->email != null))
                    $query = $query->orwhere('email', $request->email);
            }
        )
            ->first();
        return $model;
    }

    public function getSimilarCount($request)
    {
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) {
                if (isset($request->phone)  && ($request->phone != null))
                    $query = $query->orwhere('phone', $request->phone);
                if (isset($request->phone)  && ($request->phone != null))
                    $query = $query->orwhere('phone2', $request->phone);

                if (isset($request->phone2)  && ($request->phone2 != null))
                    $query = $query->orwhere('phone', $request->phone2);
                if (isset($request->phone2)  && ($request->phone2 != null))
                    $query = $query->orwhere('phone2', $request->phone2);

                if (isset($request->email)  && ($request->email != null))
                    $query = $query->orwhere('email', $request->email);
            }
        )
            ->get();
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

    public function redirectingPage3($text)
    {
        return redirect('https://trujillogutierrez.com.co/site/gracias-calculadora.html?text='.$text);
    }

    public function redirectingPage4(){
        return redirect('https://trujillogutierrez.com.co/site/gracias.html');
    }

    public function sendToWP($phone){
        return redirect('https://wa.me/57'.$phone);
    }

    public function saveAPI(Request $request){ 
        
        $this->saveLogFromRequest($request);


        $model = $this->saveUniqueCustomer($request);
        /*
       if(isset($request->source_id) && !is_null($request->source_id)){
            if($request->source_id == 67 && $request->interes == "sin-definir"){
            }else{
                $model = $this->saveUniqueCustomer($request);
                return redirect('https://www.myseo.com.co');
            }

            
       }
       */
    }

    public function saveAPIForm(Request $request){

        $model = new Customer;

        $model->name        = $request->nombre;
        $model->phone       = $request->celular;
        $model->email       = $request->email;
        $model->notes        = $request->comentarios;

        $model->save();
        
        return $model;
        
    }









    public function getURl($url){
        // Crear un nuevo recurso cURL
        $ch = curl_init();

        // Configurar URL y otras opciones apropiadas
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);

        // Capturar la URL y pasarla al navegador
        curl_exec($ch);

        // Cerrar el recurso cURL y liberar recursos del sistema
        curl_close($ch);
    }

    public function saveCustomerCalculate(Request $request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        $model = $this->isEqual($request);  
        if (!$model) {
            $similar = $this->getSimilarCount($request);

            if($request->token!=null){

                if ($similar->count() == 0 || $similar->count() == null) {
                    $model = new Customer;
                    $model = $this->saveAPICustomer($request,$model);
                    $this->sendWelcomeMail($model);
                    /* Validar si el envio del formulario viene desde la calculadora */
                    if( $request->source_id == 15 ){
                        $this->getURL("https://trujillogutierrez.com.co/site/gracias-calculadora.html");
                        return response()->json(['yes' => 'Validado correctamente']);;
                    }else{
                        if($request->project_id=='1'){
                            return $this->redirectingPage1();
                        }else{
                            return $this->redirectingPage2();
                        }
                    }
                }

                else {
                    $model = $similar[0];
                    $this->updateAPICustomer($request, $model->id);

                    if( $request->source_id == 15 ){
     
                        return response()->json(['yes' => 'Validado correctamente']);;
                    }else{
                        if($request->project_id=='1'){
                            return $this->redirectingPage1();
                        }else{
                            return $this->redirectingPage2();
                        }
                    }
                }

            }

        } else {

            $this->updateAPICustomer($request, $model->id);
            if( $request->source_id == 15 ){

                return response()->json(['yes' => 'Validado correctamente']);;
            }else{
                if($request->project_id=='1'){
                    return $this->redirectingPage1();
                }else{
                    return $this->redirectingPage2();
                }
            }
        }

        if( $request->source_id == 15 ){

            return response()->json(['yes' => 'Validado correctamente']);;
        }else{
            if($request->project_id=='1'){
                return $this->redirectingPage1();
            }else{
                return $this->redirectingPage2();
            }
        }
        
    }

    public function storeActionAPI(Request $request, $customer_id){

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



        if(isset($request->source_id))
            $str .= " Fuente:" . $request->source_id;

        //$model->note = $request->notes . $str;
        $model->type_id = 16; // actualización
        $source_id = $this->getCustomerSource($request);
        $model->type_id = $this->customerSourceToActionType($source_id); // actualización

        //dd($request);

        $model->customer_id = $customer_id;

        $model->save();

        return back();
    }


    

    public function getCustomerSource(Request $request){
        $source_id = 10;
        if( ($request->source_id != null)  && (intval($request->source_id )!=0))
            $source_id  = $request->source_id;
        else{
            if(isset($request->source_id) ) {
                switch ($request->source_id) {
                    case 'ig':
                        $source_id = 40;
                        break;
                    case 'fb':
                        $source_id = 6;
                        break;
                    default:
                        break;
                }
            }
        }   
        return $source_id;
    }

    public function updateAPICustomer(Request $request, $customer_id){

        $action = new Action;

        /*
        $action->note = "se actualizaron los datos del cliente ";
        $action->type_id = 16; // actualización
        $action->customer_id = $customer_id;
        $action->save();
        */

        $model = Customer::find($customer_id);
        
        $model->name        = $request->name;
        $model->phone       = $request->phone;
        $model->phone2      = $request->phone2;
        $model->email       = $request->email;
        $model->country     = $request->country;
        $model->city        = $request->city;
        $model->project_id  = $request->project_id;
        $model->rooms  = $request->rooms;

        
        $model->source_id  = $this->getCustomerSource($request);
        
        

        $model->bought_products = $request->product;
        $model->cid = $request->cid;
        $model->src = $request->src;
        $model->department = $request->department;
        $model->notes .= " tiempo: " . $request->time_to_buy." | nuevo: ".$request->new_or_used. " | presupuesto familiar: ".$request->family_income." | presupuesto casa: ".$request->budget."  ".$request->notes;
        $model->status_id = 5;

        $model->save();

        // guardo una acción
        $this->addAction($model);
    }

    public function storeEmailAction($mail, $customer, $note)
    {
        $today = Carbon\Carbon::now();
        // envio mail
        $action_type_id = 2;

        $model = new Action;

        $model->note = $note;
        $model->type_id = $action_type_id;
        $model->creator_user_id = Auth::id();
        $model->customer_id = $model->customer_id;


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

    

    public function excel(Request $request)
    {
        $users = $this->getUsers();
        $customer_options = CustomerStatus::all();
        $statuses = $this->getStatuses($request, 2);

        $model = $this->filterModelFull($request, $statuses);
        $customersGroup = $this->countFilterCustomers($request, $statuses);

        $sources = CustomerSource::all();

        return view('customers.excel', compact('model', 'request', 'customer_options', 'customersGroup', 'query', 'users', 'sources'));
    }

    /*
    public function updateAPICustomer2(Request $request, $customer_id)
    {

        $customer = Customer::find($customer_id);
        $model = new Action;


        $model->note = "se actualizaron los datos del cliente " . $customer->created_at;
        $model->type_id = 16; // actualización
        $model->customer_id = $customer_id;
        $model->save();


        $mytime = Carbon\Carbon::now();
        //$customer->created_at = $mytime->toDateTimeString();
        $customer->status_id = 19;
        if($customer->name=="")
            $customer->name = $request->name;
        if($customer->phone=="")
            $customer->phone = $request->phone;
        if($customer->email=="")
            $customer->email = $request->email;
        
        $customer->project_id = $request->project_id;
        $customer->source_id = $request->source_id;

        
        $customer->save();

        $this->addAction($customer);


        return back();
    }
    */


    public function trackWPAction($cid,  $aid, $tid, $msg,  Request $request)
    {
        $msg = urldecode($msg);
            //dd($request->header('User-Agent'));
            $model = Customer::find($cid);

            if($model){
                
                $model->status_id  = 4;
                $model->save();
                $cHistory = new CustomerHistory;
                $cHistory->saveFromModel($model);
                
                
                $cAudience = AudienceCustomer::where('audience_id', $aid)
                ->Where('customer_id', $cid)
                ->first();
                $cAudience->sended_at = Carbon\Carbon::now();
                if($cAudience->save()){
                    echo "guardado";
                }
                $this->saveAction($cid, null, $tid, $msg);

                //return redirect('http://testtrujillo.quirky.com.co/audiences/');

                /*
                if($pid==1)// la quinta
                    return redirect('https://www.youtube.com/watch?v=rr0pEeyLS58');
                if($pid==2)// la quinta
                    return redirect('https://www.youtube.com/watch?v=QkQSQ_tTebc'); 
                */
            }
            
        
    }

    public function trackAction($cid,  $sid, $tid, $pid, Request $request)
    {
        //dd($request->header('User-Agent');
        if(strpos($request->header('User-Agent'), "WhatsApp") === false   ){
            $model = Customer::find($cid);
            if($model){
                //$model->notes .= $request;
                if($sid == 9)
                    $model->status_id  = $sid;
                if($sid == 1){
                    $model->status_id  = 3;
                    $model->scoring = 1;
                }
                
                $cHistory = new CustomerHistory;
                $cHistory->saveFromModel($model);
                
                $model->save();

                $cAudience = AudienceCustomer::where('customer_id', $cid)->first();
                $cAudience->sended_at = Carbon\Carbon::now();
                if($cAudience->save()){
                    echo "guardado";
                }

               
                
                if($sid == 9){ // descartado
                    $this->saveAction($cid, null, $tid, "No le interesa recibir más información".$request->header('User-Agent')." NO -".strpos($request->header('User-Agent'), "WhatsApp") );

                    return redirect('https://trujillogutierrez.com.co/site/darse-de-baja.html');
                    
                }
                if( ($sid == 1) ){ 
                    $this->saveAction($cid, null, $tid, "Le interesa recibir más información".$request->header('User-Agent'));
                    
                    if($pid==1)// la quinta
                        return redirect('https://www.youtube.com/watch?v=rr0pEeyLS58');
                    if($pid==2)// la quinta
                        return redirect('https://www.youtube.com/watch?v=QkQSQ_tTebc');
                      
                       
                }
            }else{
                return redirect('https://trujillogutierrez.com.co/');   
            }
        }
    }


    public  function saveAction($cid, $oid, $tid, $str){
        $str = urldecode($str);
        $model = new Action;
        $model->customer_id = $cid;
        $model->object_id = $oid;
        $model->type_id = $tid;
        $model->note = $str;
        date_default_timezone_set('America/Bogota');
        $date = date('Y-m-d H:i:s');
        $model->delivery_date= $date;
        $model->creator_user_id = 4;
        $model->save();
        
    }






    //FINCA RAIZ
    function getToken(){
        $fincaRaiz = new FincaRaiz();
        return $fincaRaiz->doLogin();   
    }

    
    public function getResponse(Request $product){
        $product = $this->getToken();
        $product_size = count($product);
        for ($i=0; $i<$product_size; $i++) {
            if(isset($product[$i]["Ciudad_Proyecto"])){
                $cityProject = $product[$i]["Ciudad_Proyecto"];
                if($cityProject = "Manizales"){
                    $cityProjectId = 1;
                }else if($cityProject = "Dosquebradas"){
                    $cityProjectId = 2;
                }
            }
            $request = new Customer;
            if(isset($product[$i]["ContactName"])){
                $request->name = $product[$i]["ContactName"];
            }
            if(isset($product[$i]["Email"])){
                $request->email = $product[$i]["Email"];
            }
            if(isset($product[$i]["Phone"])){
                $request->phone = $product[$i]["Phone"];
            }
            if(isset($product[$i]["Description"])){
                $request->notes = $product[$i]["Description"];
            }
            if(isset($product[$i]["SendDate"])){
                $date = $product[$i]["SendDate"];
                $date = str_replace("T", " ", $date);
                $request->created_at = $date;
            }
            $request->project_id = $cityProjectId;
            $request->source_id = 4;//FINCA RAIZ
            
            $this->saveAPIFR($request);

            //PENDIENTES POR IMPLEMENTAR
            //$departamentoCorrespondencia = $product[$i]["Departamento_para_Correspondencia"];
        } 
    }


    public function saveAPIFR($request){
        $model = $this->isEqual($request);
        if(!$model){
            $model = $this->getSimilar($request);
            if(!$model){
                $model = new Customer;
                $model = $this->saveAPICustomerFR($request, $model);
            }
        }
        
        //$this->storeActionAPIFR($request, $model->id);
    }

    public function storeActionAPIFR($request, $customer_id){
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
        if(isset($request->source_id))
            $str .= " Fuente:" . $request->source_id;
        //$model->note = $request->notes . $str;
        $model->type_id = 16; // actualización
        //$source_id = $this->getCustomerSource($request);
        $source_id = $request->source_id;
        //$model->type_id = $this->customerSourceToActionType($source_id); // actualización
        //dd($request);
        $model->customer_id = $customer_id;
        $model->save();
        return back();
    }


    public function saveAPICustomerFR($request, $model){

        
        $model->name        = $request->name;
        $model->phone       = $request->phone;
        $model->phone2      = $request->phone2;
        $model->email       = $request->email;
        $model->country     = $request->country;
        $model->city        = $request->city;
        $model->notes        = $request->notes;
        $model->created_at        = $request->created_at;
        if (isset($request->project_id)){
            $model->project_id  = $request->project_id;
        }
        $model->rooms  = $request->rooms;
        
        $model->source_id  = 10;
        
        if( ($request->source_id != null)  && (intval($request->source_id )!=0))
            $model->source_id  = $request->source_id;
        else{
            if(isset($request->source_id) ) {
                switch ($request->source_id) {
                    case 'ig':
                        $model->source_id = 40;
                        break;
                    case 'fb':
                        $model->source_id = 6;
                        break;
                    default:
                        break;
                }
            }
        }

        $model->bought_products = $request->product;
        $model->cid = $request->cid;
        $model->src = $request->src;
        if(isset($request->time_to_buy))
            $model->notes .= "tiempo: " . $request->time_to_buy." | nuevo: ".$request->new_or_used. " | presupuesto familiar: ".$request->family_income." | presupuesto->casa: ".$request->budget."  ".$request->notes;    
        
        $model->department = $request->department;
        $model->status_id = 5;

        $model->save();

        // guardo una acción
        $this->addAction($model);
        
        return $model;
    }

    public function updateFromWebPage(Request $request){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        $model = $this->saveUniqueCustomer($request); 
        $reference = $this->saveReference($request, $model->id);
        return $reference->id;
    }

    
    public function saveUniqueCustomer(Request $request){
        $model = $this->isEqual($request);
        if(!$model){
            $model = $this->getSimilar($request);
            if(!$model){
                $model = new Customer;
            }
        }
        $model = $this->saveAPICustomer($request, $model);
        return $model;
    }

    public function saveReference(Request $request, $cid){
        $reference = new Reference;
        $reference->document_number = $request["document_number"];
        $reference->name = $request["name"];
        $reference->note = $request["note"];
        $reference->value = $request["value"];
        $reference->email = $request["email"];
        $reference->phone = $request["phone"];
        $reference->address = $request["address"];
        $reference->project = $request["project"];
        $reference->apartment = $request["apartment"];

    
        
        $reference->customer_id = $cid;
        $reference->save();
        return $reference;
    }

    public function updateStatus(Request $request){
        $data = $request->json()->all();
        $transaction = $data["data"]["transaction"];
        $status = $transaction["status"];
        $id = $transaction["id"];
        $amount_in_cents = $transaction["amount_in_cents"];
        $reference = $transaction["reference"];
        $ustomer_email = $transaction["customer_email"];
        $currency = $transaction["currency"];
        $payment_method_type = $transaction["payment_method_type"];
        $redirect_url = $transaction["redirect_url"];
        $status = $transaction["status"];
        $shipping_address = $transaction["shipping_address"];
        $payment_link_id = $transaction["payment_link_id"];
        $payment_source_id = $transaction["payment_source_id"];

        $model = Reference::find($reference);
        $model->status_id = $status;
        $model->save();
    }


    //SINCO
    function getTokenSinco(){
        $sinco = new Sinco();
        return $sinco->doLogin();   
    }
    //Enviar correo Notificación creación customer

public function sendEmail($customer)
    {
        $email_id = 1;
        
        $email = Email::find($email_id);

        $count = Email::sendUserEmailWelcome($customer->id, $email->subject, $email->view, $email->id);  
        $this->storeEmailAction($email, $customer, "Correo automático de bienvenida");
    
        return back();

    }

}
