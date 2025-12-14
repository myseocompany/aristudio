<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('role_modules', function (Blueprint $table) {
            $table->unsignedTinyInteger('view_scope')
                ->default(0)
                ->comment('0: solo asignados, 1: todos')
                ->after('list');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_modules', function (Blueprint $table) {
            $table->dropColumn('view_scope');
        });
    }
};
