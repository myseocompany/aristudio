<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Action;
use App\CustomerMeta;
use Carbon;

// use Laravel\Scout\Searchable;

class Customer extends Model
{
    function actions(){
        return $this->hasMany('App\Action');
    }
    function customer_files(){
        return $this->hasMany('App\CustomerFile');
    }
    public function status(){
    	return $this->belongsTo('App\CustomerStatus');
    }

    function user(){
        return $this->belongsTo('App\User');
    }

    function project(){
        return $this->belongsTo('App\Project');
    }

      function source(){
        return $this->belongsTo('App\CustomerSource');
    }
    // function employee_files(){
    // 	return $this->hasMany('App\EmployeeFile');
    // }

    public function searchableAs(){
        return 'employee_id';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
    }

    public function countActions(){
        $count = Action::where('customer_id','=',$this->id)->count();

        return $count;
    }

    public function countInActions(){
        $count = Action::leftJoin('action_types', 'action_types.id', 'type_id')
            ->where('customer_id','=',$this->id)
            ->where('outbound', '=', 0)
            ->where('actions.status_id', '=', 1)
            
            ->count();

        return $count;
    }

    public function countOutActions(){
        $count = Action::leftJoin('action_types', 'action_types.id', 'type_id')
            ->where('customer_id','=',$this->id)
            ->where('outbound', '=', 1)
            ->whereNotNull('creator_user_id')
            ->where('actions.status_id', '=', 1)
            ->count();

        return $count;
    }

    public function phoneAsCode($phone){
        
        if(strlen($phone)>10)
            return true;
        else
            return false;
    }

    public function getPhoneWith57($phone){
        if(strlen($phone)>10)
            return $phone;
        elseif( strlen($phone) == 10 )
            return "57".$phone;
        else
            return "";
    }

    public function getPhoneStr(){
        $phone = "";
        if(isset($this->phone))
            $phone = $this->phone;
        elseif(isset($this->phone2))
            $phone = $this->phone2;
        return $phone;
    }

    public function cleanPhone($phone){
        $newPhone = $phone;
        $str = substr($phone, 0, 3);
        if(substr($phone, 0, 3) == "p:+")
            $newPhone = substr($phone, 3, strlen($phone));
        if(substr($phone, 0, 1) == "+")
            $newPhone = substr($phone, 1, strlen($phone));
        return $newPhone;


    }

    public function hasAValidPhone(){
        
        $phone = $this->cleanPhone($this->getPhoneStr());
        if($this->phoneAsCode($phone)){
            /*
            $number = substr($phone, -10);
            $ind = str_replace($number, "", $phone);
            if ($ind =='+57' || $ind=="57" || $ind == "54")
                return true;
            else
                return false;
            */
                //echo $phone."*";
                return true;

        }else{
            //echo $phone."_";
            if( $phone=="" || strlen($phone) < 10)
                return false;
            else
                return true; 
        }     
    }

    public function getPhone(){
        $phone = "";
        $phone =  $this->getPhoneWith57($this->cleanPhone($this->getPhoneStr()) ) ;
        return $phone;
    }

    public function gender(){
        return $this->belongsTo('App\CustomerMetaData', 'meta_gender_id')->where('parent_id', '1');        
    }
    public function economic_activity(){
        return $this->belongsTo('App\CustomerMetaData', 'meta_economic_activity_id')->where('parent_id', '2');        
    }
    public function income(){
        return $this->belongsTo('App\CustomerMetaData', 'meta_income_id')->where('parent_id', '3');        
    }
    public function investment(){
        return $this->belongsTo('App\CustomerMetaData', 'meta_investment_id')->where('parent_id', '4');        
    }

    public function houseMates(){
        return $this->belongsToMany('App\CustomerMetaData', 'customer_metas', 'customer_id', 'meta_data_id')->where('meta_data_type_id', '1');        
    }

    public function fundingSource(){
        return $this->belongsToMany('App\CustomerMetaData', 'customer_metas', 'customer_id', 'meta_data_id')->where('meta_data_type_id', '2');        
    }

    public function finalFundingSource(){
        return $this->belongsToMany('App\CustomerMetaData', 'customer_metas', 'customer_id', 'meta_data_id')->where('meta_data_type_id', '3');        
    }    
    // $cmdid es la definicion, $cmid es el valor
    public function getMeta($mid){
        $model = CustomerMeta::where('customer_id', $this->id)
            ->where('meta_data_id', $mid)
            ->first();
        return $model;
    }

    public function createdDays(){
        $created = new Carbon\Carbon($this->created_at);
        $now = Carbon\Carbon::now();
        $difference = ($created->diff($now)->days < 1)
            ? 'hoy'
            : $created->diffInDays($now) . ' dias';
        return $difference;
    }
}
