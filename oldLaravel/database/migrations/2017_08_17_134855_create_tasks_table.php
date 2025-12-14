<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         Schema::create('tasks', function(Blueprint $table){
            $table->increments('id');
            $table->string('name','240');
            $table->text('description')->nullable();;
            $table->integer('points')->nullable();;
            $table->integer('project_id')->nullable();;
            $table->integer('user_id')->nullable();;
            $table->integer('status_id')->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('tasks');
    }
}
