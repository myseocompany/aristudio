<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('project_logins')) {
            return;
        }

        Schema::table('project_logins', function (Blueprint $table): void {
            if (Schema::hasColumn('project_logins', 'is_active')) {
                $table->boolean('is_active')->default(false)->change();
            }
        });

        DB::table('project_logins')->update(['is_active' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('project_logins')) {
            return;
        }

        Schema::table('project_logins', function (Blueprint $table): void {
            if (Schema::hasColumn('project_logins', 'is_active')) {
                $table->boolean('is_active')->default(true)->change();
            }
        });
    }
};
