<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('role_modules')) {
            Schema::create('role_modules', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('role_id');
                $table->unsignedInteger('module_id');
                $table->unsignedTinyInteger('created')->nullable();
                $table->unsignedTinyInteger('readed')->nullable();
                $table->unsignedTinyInteger('updated')->nullable();
                $table->unsignedTinyInteger('deleted')->nullable();
                $table->unsignedTinyInteger('list')->nullable();
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
                $table->foreign('module_id')->references('id')->on('modules')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_modules');
    }
};
