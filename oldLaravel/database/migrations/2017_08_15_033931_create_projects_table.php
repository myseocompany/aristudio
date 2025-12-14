<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('projects', function(Blueprint $table){
            $table->increments('id');
            $table->string('name','240');
            $table->text('description')->nullable();;
            $table->bigInteger('budget')->nullable();;
            $table->integer('status_id')->nullable();;
            $table->datetime('start_date')->nullable();;
            $table->datetime('finish_date')->nullable();;
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
        Schema::dropIfExists('projects');
    }
}
