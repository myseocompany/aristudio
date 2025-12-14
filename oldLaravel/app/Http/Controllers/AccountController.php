<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Account;


class AccountController extends Controller{

	public function __construct()
    {
        $this->middleware('auth');
    }
     

    public function index(Request $request){

    	$model = Account::where(function($query)use($request){
    		
    		if(isset($request->parent_id) ){
    			$query->where( "parent_id",'=', $request->parent_id);
    		}
    		else{
    			$query->where("parent_id", '0');
    		}
    			
    	})->get();



		return view('accounts.index',compact('model', 'request'));
    }


    
}