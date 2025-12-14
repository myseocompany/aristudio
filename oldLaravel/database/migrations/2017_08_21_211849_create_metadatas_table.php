<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetadatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $_tablename = 'metadatas';
        //
        Schema::create($_tablename, function(Blueprint $table){
            $table->increments('id');
            $table->string('name','240');
            $table->text('value')->nullable();
            
        });

        DB::table($_tablename)->insert(["name" => "project_status","value" =>"{'1':'planned','2':'started', '3':'finished', '4':'canceled', '5':'suspended'}"]); 

        DB::table($_tablename)->insert(['name' => 'task_status','value' =>'{"1":"planned","2":"started", "3":"finished", "4":"canceled", "5":"suspended"}']);  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('metadatas');
    }
}
