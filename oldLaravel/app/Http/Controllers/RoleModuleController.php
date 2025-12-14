<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use DB;
use App\Action;
use App\Customer;
use App\CustomerStatus;
use App\Email;
use Mail;
use App\DateTime;
use App\RoleModule;
use App\Module;
use Auth;


class RoleModuleController extends Controller
{
    public function index( Request $request){
    	if(Auth::user()->getRoleModule(Auth::user()->role_id,7) == 7){
        $role_modules = RoleModule::orderBy('role_id','ASC')->get();
        $roles = Role::all();
        $modules = Module::all();
        return view('role_modules.index', compact('role_modules','roles', 'modules'));
        }else{
            return redirect("http://myseo.com.co/");
        }
    }
}
