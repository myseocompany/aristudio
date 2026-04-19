<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_briefs', function (Blueprint $table): void {
            $table->string('public_token', 64)->nullable()->unique()->after('created_by');
        });

        DB::table('project_briefs')
            ->whereNull('public_token')
            ->orderBy('id')
            ->get(['id'])
            ->each(function ($brief): void {
                DB::table('project_briefs')
                    ->where('id', $brief->id)
                    ->update(['public_token' => Str::random(48)]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_briefs', function (Blueprint $table): void {
            $table->dropUnique(['public_token']);
            $table->dropColumn('public_token');
        });
    }
};
