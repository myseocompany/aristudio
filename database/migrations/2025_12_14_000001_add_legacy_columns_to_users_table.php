<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('role_id')->nullable()->after('email');
            $table->unsignedInteger('daily_goal')->nullable()->after('role_id');
            $table->unsignedTinyInteger('status_id')->default(1)->after('daily_goal');
            $table->string('document', 250)->nullable()->after('status_id');
            $table->string('address', 250)->nullable()->after('document');
            $table->date('birth_date')->nullable()->after('address');
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('birth_date');
            $table->string('color', 20)->nullable()->after('hourly_rate');
            $table->integer('availability')->nullable()->after('color');
            $table->unsignedInteger('enterprise_id')->nullable()->after('availability');
            $table->unsignedInteger('facebook_id')->nullable()->after('enterprise_id');
            $table->string('phone', 250)->nullable()->after('facebook_id');
            $table->string('image_url', 200)->nullable()->after('phone');
            $table->string('position', 100)->nullable()->after('image_url');
            $table->date('entry_date')->nullable()->after('position');
            $table->date('termination_date')->nullable()->after('entry_date');
            $table->integer('contracted_hours')->nullable()->after('termination_date');
            $table->string('contract_type', 50)->nullable()->after('contracted_hours');
            $table->string('blood_type', 5)->nullable()->after('contract_type');
            $table->timestamp('last_login')->nullable()->after('blood_type');
            $table->string('arl', 250)->nullable()->after('last_login');
            $table->string('eps', 250)->nullable()->after('arl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role_id',
                'daily_goal',
                'status_id',
                'document',
                'address',
                'birth_date',
                'hourly_rate',
                'color',
                'availability',
                'enterprise_id',
                'facebook_id',
                'phone',
                'image_url',
                'position',
                'entry_date',
                'termination_date',
                'contracted_hours',
                'contract_type',
                'blood_type',
                'last_login',
                'arl',
                'eps',
            ]);
        });
    }
};
