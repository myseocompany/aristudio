<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProjectIndexSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('project_users');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_statuses');
        Schema::dropIfExists('project_types');

        Schema::create('project_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('project_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->decimal('weight', 8, 2)->default(0);
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('ads_budget', 12, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('project_users', function (Blueprint $table): void {
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    public function test_can_filter_projects_by_name(): void
    {
        $user = User::factory()->create();
        $this->grantModulePermissions($user, '/projects', ['list']);
        $this->actingAs($user->refresh());

        DB::table('project_statuses')->insert([
            ['id' => 3, 'name' => 'Activo', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('project_types')->insert([
            ['id' => 1, 'name' => 'Tipo A', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('projects')->insert([
            [
                'id' => 1,
                'name' => 'Proyecto Alfa',
                'status_id' => 3,
                'type_id' => 1,
                'weight' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Proyecto Beta',
                'status_id' => 3,
                'type_id' => 1,
                'weight' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->get(route('projects.index', ['q' => 'Alfa']));

        $response->assertOk();
        $response->assertSee('Proyecto Alfa');
        $response->assertDontSee('Proyecto Beta');
    }
}
