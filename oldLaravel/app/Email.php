<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Mail;
use App\SendWithData;

class Email extends Model{

	public static function addEmailQueue($email, $item, $count,$sended_at){
        $email_queue = new EmailQueue;
        $email_queue->user_id = $item->id;
        $email_queue->email_id = $email->id;
        $email_queue->subject = $email->subject;
        $email_queue->view = $email->view;
        $email_queue->available_at = Email::getDelay($count,$sended_at);
        $email_queue->email = $item->email;
        $email_queue->save();    
    }

     public static function getDelay($seconds,$sended_at){
        if(is_null($sended_at))
			$current_time = Carbon::now(-5);
		else
			$current_time=Carbon::createFromFormat('Y-m-d H:i:s', $sended_at, 'Europe/London');

        return $current_time->addSeconds($seconds);

    }

    public static function isValidEmail($email){

    	return filter_var($email, FILTER_VALIDATE_EMAIL);

    }

    public static function sendUserEmail($cid, $subject, $view, $eid){
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
            
            return count(Mail::failures());
         
        }  
    }
}
