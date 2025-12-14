<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemoEmail;

class MailController extends Controller
{
    public function send()
    {
        $details = [
            'title' => 'Mail from Laravel',
            'body' => 'This is for testing email using smtp'
        ];
       
        Mail::to('nicolas@myseocompany.co')->send(new DemoEmail($details));
       
        return back()->with('message_sent','Your mail has been sent successfully!');
    }
}
