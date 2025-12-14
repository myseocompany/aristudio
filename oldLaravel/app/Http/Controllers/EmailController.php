<?php



namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Email;
use App\Customer;
use Carbon\Carbon;
use DB;
use App\EmailQueue;
use Mail;
use App\Action;
use App\SendWithData;

use Illuminate\Mail\Mailable;



class EmailController extends Controller
{
    
    
    public function store($id){
        $email = Email::find($id);
        $model=null;

        switch ($id) {
            case '1':
                $model=$this->getCustomersByStatus(1);
                break;
             case '2':
                $model=$this->getCustomersByCountry('usa');
                break;   
            case '3':
            	$model=$this->getCustomersBySource(21);
                break;
            case '4':
                $model=$this->getCustomersByStatus(7);
                break;
            case '5':
                $model=$this->getCustomersBySource(22);
                break;
            case '6':
                $model=$this->getCustomersBySource(19);
                break; 
			case '13':
				$model=$this->getCustomersByCity("Bogotá");
				break;
			case '14':
				$model=$this->getCustomersByCity("Myseolandia");
				break;
            default:
                # code...
                break;
        }
        $count = 1;
        if(!is_null($email->sended_at))
			$sended_at=$email->sended_at;
		else
			$sended_at=0;
        foreach($model as $item){
            Email::addEmailQueue($email, $item, $count*10,$sended_at);
            $count++;
        }

    }

    public function getCustomersZeroActions(){ 
        $model = DB::table('actions')
            ->select('customers.id', 'project_id', DB::raw('COUNT(actions.id)'))
            ->rightJoin('customers', 'customers.id', '=', 'actions.customer_id')          
            ->whereNotNull('email')
            ->where('email', '<>', 'NA')  
            ->where('email', '<>', 'SIN CORREO ELECTRÓNICO')
            ->groupBy('customers.id', 'project_id')
            ->having( DB::raw('COUNT(actions.id)'), '=', '0')
            ->get();
        dd($model);  


        foreach($model as $item){
            Email::addEmailQueue($email, $item, $count*10,$sended_at);
            $count++;
            if(project_id==1)
                $sended_at=1;
            if(project_id==2)
                $sended_at=3;
        }  
    }


    public function getCustomersByStatus($id){

        // $model = DB::table('customers')
        //     ->select('customers.id', 'name', 'email', DB::raw('COUNT(actions.id)'))
        //     ->leftJoin('actions', 'customers.id','=','customer_id')
        //     ->whereNotNull('email')
        //     ->where('email','<>','')
        //     ->where('email','<>',' ')
        //     ->where('status_id','=',$id)
        //     ->groupBy('customers.id')
        //     ->groupBy('name')
        //     ->groupBy('email')
        //     ->having(DB::raw('COUNT(actions.id)'),"=",'0')
        //     ->get();

        $model = DB::table('customers')
            ->select('customers.id', 'name', 'email')
            ->whereNotNull('email')
            ->where('email','<>','')
            ->where('email','<>',' ')
            ->where('email','<>','null@null.es')
            ->where('email','<>','null@null.com')
            ->where('email','<>','NUll@nul.es')
            ->where('email','<>','notiene@gmail.com')
            ->where('email','<>','nodio@gmail.com')
            ->where('email','<>','noenvio@gmail.com')
            ->where('email','<>','No@valido.com')
            ->where('email','<>','No@valido.com')
            ->where('status_id','=',$id)
            ->get();

            return $model;
    }

    public function getCustomersByCountry($country){
        $model = DB::table('customers')
            ->select('customers.id', 'name', 'email')
            ->whereNotNull('email')
            ->where('email','<>','')
            ->where('email','<>',' ')
            ->where('email','<>','null@null.es')
            ->where('email','<>','null@null.com')
            ->where('email','<>','NUll@nul.es')
            ->where('email','<>','notiene@gmail.com')
            ->where('email','<>','nodio@gmail.com')
            ->where('email','<>','noenvio@gmail.com')
            ->where('email','<>','No@valido.com')
            ->where('email','<>','No@valido.com')
            ->whereRaw('LOWER(`country`) LIKE ? ',[trim(strtolower($country)).'%'])
            ->get();

            return $model;
    }
    
    public function getCustomersByCity($city){
        $model = DB::table('customers')
            ->select('customers.id', 'name', 'email')
            ->whereNotNull('email')
            ->where('email','<>','')
            ->where('email','<>',' ')
            ->where('email','<>','null@null.es')
            ->where('email','<>','null@null.com')
            ->where('email','<>','NUll@nul.es')
            ->where('email','<>','notiene@gmail.com')
            ->where('email','<>','nodio@gmail.com')
            ->where('email','<>','noenvio@gmail.com')
            ->where('email','<>','No@valido.com')
            ->where('email','<>','No@valido.com')
            ->whereRaw('LOWER(`city`) LIKE ? ',[trim(strtolower($city)).'%'])
            ->get();
            return $model;
    }

    public function getCustomersBySource($src){
    	$model = DB::table('customers')
    	   ->select('customers.id', 'name', 'email')
    	   ->where('source_id','=',$src)
    	   ->get();

    	   return $model;
    }

    public function getDelay($seconds){



        $current_time = Carbon::now(-5);

        return $current_time->addSeconds($seconds);

    }

    public function send(){

        $model = EmailQueue::
            where('available_at','<',Carbon::now())
            ->where('email','<>', '')
            ->where('email','<>', ' ')
            ->whereNotNull('email')
            ->get();
            
        $max = 500;
        $count = 0;
        foreach($model as $item){
			//dd($item->email);
            if($count<$max){
                if (filter_var($item->email, FILTER_VALIDATE_EMAIL)) {
                    $count = Email::sendUserEmail($item->user_id, $item->subject, $item->view, $item->email_id);  
                    $cid = $item->user_id;
                    $eid = $item->email_id;
                    if($count>0){
                        Action::saveAction($cid, $eid, 5);
                    }else{
                        Action::saveAction($cid, $eid, 2);
                    }         
                    $this->destroyEmailQueue($item->id);
                }
            }
            $count++;
        }
    }

    public function destroyEmailQueue($id){
        $model = EmailQueue::find($id);
        $model->delete();

    }

    public function sendUserEmail($cid, $subject, $view, $eid){
        //dd($cui);
        $model = Customer::find($cid);


        if($model){

            $email = Email::find($eid);

            $emailcontent = array (
                'subject' => $subject,
                'emailmessage' => 'Este es el contenido',
                'customer_id' => $cid,
                'email_id' => $eid,
                'name' => $model->name,
                'view' => $view,
                'to' => $model->email,
                'from_email' => $email->from_email,
                'from_name' => $email->from_name,
                'model' => $model,


             );
            Mail::to($model->email)->send(new SendWithData($emailcontent));
/*
            Mail::send($view, $emailcontent, function ($message) use ($model, $subject){
                    $message->subject($subject);

                    $message->to($model->email);
            });
 */           
            if(count(Mail::failures())>0){
                Action::saveAction($cid, $eid, 5);
            }else{
                Action::saveAction($cid, $eid, 2);
            }
         
        }  
    }

    
    
}

