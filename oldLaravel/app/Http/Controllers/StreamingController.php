<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class StreamingController extends Controller{
    public function index(){
        	return view("streaming.view");
            }
}