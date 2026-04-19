<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectBrief;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProjectBriefTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('project_meta_datas');
        Schema::dropIfExists('project_users');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_statuses');
        Schema::dropIfExists('project_types');

        Schema::create('project_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });

        Schema::create('project_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });

        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->string('name');
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('ads_budget', 12, 2)->nullable();
            $table->integer('weekly_pieces')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->integer('monthly_points_goal')->nullable();
            $table->integer('weight')->nullable();
            $table->date('start_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->integer('lead_target')->nullable();
            $table->decimal('sales', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('project_users', function (Blueprint $table): void {
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('project_meta_datas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('value', 1000);
            $table->unsignedTinyInteger('type_id')->nullable();
            $table->integer('weight')->nullable();
            $table->timestamps();
        });
    }

    public function test_project_can_have_multiple_briefs_with_answers(): void
    {
        $user = User::factory()->create();
        $this->grantModulePermissions($user, '/projects', ['read', 'create', 'update', 'delete']);

        Schema::getConnection()->table('project_statuses')->insert(['id' => 3, 'name' => 'Running']);
        Schema::getConnection()->table('project_types')->insert(['id' => 1, 'name' => 'Web']);

        $project = Project::query()->create([
            'name' => 'Proyecto Brief',
            'type_id' => 1,
            'status_id' => 3,
            'color' => '#10b981',
        ]);

        Schema::getConnection()->table('project_meta_datas')->insert([
            ['id' => 1, 'parent_id' => null, 'value' => 'Producto', 'type_id' => 1, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'parent_id' => 1, 'value' => '¿De qué trata tu empresa?', 'type_id' => 4, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'parent_id' => null, 'value' => 'Canales activos', 'type_id' => 3, 'weight' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'parent_id' => 3, 'value' => 'Instagram', 'type_id' => null, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'parent_id' => 3, 'value' => 'Google', 'type_id' => null, 'weight' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->actingAs($user)->post(route('projects.briefs.store', $project), [
            'title' => 'Brief inicial',
            'notes' => 'Primera conversación',
            'answers' => [
                2 => 'Vende automatización industrial.',
            ],
            'selected_options' => [
                3 => [4, 5],
            ],
        ]);

        $brief = ProjectBrief::query()->firstOrFail();

        $response->assertRedirect(route('projects.briefs.show', [$project, $brief]));
        $this->assertDatabaseHas('project_briefs', [
            'project_id' => $project->id,
            'title' => 'Brief inicial',
            'notes' => 'Primera conversación',
        ]);
        $this->assertDatabaseHas('project_brief_answers', [
            'project_brief_id' => $brief->id,
            'project_meta_data_id' => 2,
            'value' => 'Vende automatización industrial.',
        ]);
        $this->assertDatabaseHas('project_brief_answers', [
            'project_brief_id' => $brief->id,
            'project_meta_data_id' => 4,
            'value' => 'on',
        ]);
        $this->assertDatabaseHas('project_brief_answers', [
            'project_brief_id' => $brief->id,
            'project_meta_data_id' => 5,
            'value' => 'on',
        ]);

        $secondResponse = $this->actingAs($user)->post(route('projects.briefs.store', $project), [
            'title' => 'Brief pauta',
            'answers' => [
                2 => 'Nueva línea de servicio.',
            ],
        ]);

        $secondResponse->assertRedirect();
        $this->assertSame(2, $project->briefs()->count());

        $showResponse = $this->actingAs($user)->get(route('projects.briefs.show', [$project, $brief]));

        $showResponse->assertOk();
        $showResponse->assertSee('Brief inicial');
        $showResponse->assertSee('¿De qué trata tu empresa?');
        $showResponse->assertSee('Vende automatización industrial.');
        $showResponse->assertSee('Instagram');
    }

    public function test_migration_imports_legacy_project_metas_as_initial_briefs(): void
    {
        Schema::dropIfExists('project_brief_answers');
        Schema::dropIfExists('project_briefs');
        Schema::dropIfExists('project_metas');

        Schema::create('project_metas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('meta_data_id')->nullable();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('project_metas')->insert([
            [
                'project_id' => 99,
                'meta_data_id' => 2,
                'value' => 'Respuesta anterior',
                'created_at' => '2025-01-01 10:00:00',
                'updated_at' => '2025-01-01 10:00:00',
            ],
            [
                'project_id' => 99,
                'meta_data_id' => 4,
                'value' => 'on',
                'created_at' => '2025-01-01 10:05:00',
                'updated_at' => '2025-01-01 10:05:00',
            ],
        ]);

        $briefsMigration = require database_path('migrations/2026_04_19_231916_create_project_briefs_table.php');
        $answersMigration = require database_path('migrations/2026_04_19_231917_create_project_brief_answers_table.php');

        $briefsMigration->up();
        $answersMigration->up();

        $this->assertDatabaseHas('project_briefs', [
            'project_id' => 99,
            'title' => 'Brief legado',
        ]);

        $briefId = DB::table('project_briefs')->where('project_id', 99)->value('id');

        $this->assertDatabaseHas('project_brief_answers', [
            'project_brief_id' => $briefId,
            'project_meta_data_id' => 2,
            'value' => 'Respuesta anterior',
        ]);
        $this->assertDatabaseHas('project_brief_answers', [
            'project_brief_id' => $briefId,
            'project_meta_data_id' => 4,
            'value' => 'on',
        ]);
    }
}
