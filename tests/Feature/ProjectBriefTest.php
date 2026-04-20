<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectBrief;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectBriefTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('project_meta_datas');
        Schema::dropIfExists('project_metas');
        Schema::dropIfExists('project_logins');
        Schema::dropIfExists('campaign_project_meta_data');
        Schema::dropIfExists('campaigns');
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

        Schema::create('project_metas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('meta_data_id')->nullable();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('project_logins', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('name');
            $table->string('user')->nullable();
            $table->string('password')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('campaign_project_meta_data', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('project_meta_data_id');
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
        $this->assertNotEmpty($brief->public_token);
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

    public function test_guest_can_fill_public_brief_link_without_login(): void
    {
        Schema::getConnection()->table('project_statuses')->insert(['id' => 3, 'name' => 'Running']);
        Schema::getConnection()->table('project_types')->insert(['id' => 1, 'name' => 'Web']);

        $project = Project::query()->create([
            'name' => 'Proyecto Cliente',
            'type_id' => 1,
            'status_id' => 3,
        ]);

        Schema::getConnection()->table('project_meta_datas')->insert([
            ['id' => 10, 'parent_id' => null, 'value' => 'Empresa', 'type_id' => 1, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'parent_id' => 10, 'value' => 'Describe tu empresa', 'type_id' => 4, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'parent_id' => null, 'value' => 'Tipo de cliente', 'type_id' => 2, 'weight' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'parent_id' => 12, 'value' => 'B2B', 'type_id' => null, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $brief = ProjectBrief::query()->create([
            'project_id' => $project->id,
            'title' => 'Brief para cliente',
        ]);

        $this->assertGuest();

        $formResponse = $this->get(route('public.briefs.edit', $brief->public_token));

        $formResponse->assertOk();
        $formResponse->assertSee('Brief para cliente');
        $formResponse->assertSee('Describe tu empresa');

        $submitResponse = $this->put(route('public.briefs.update', $brief->public_token), [
            'title' => $brief->title,
            'answers' => [
                11 => 'Somos una empresa industrial.',
            ],
            'selected_options' => [
                12 => [13],
            ],
        ]);

        $submitResponse->assertRedirect(route('public.briefs.edit', $brief->public_token));
        $this->assertDatabaseHas('project_brief_answers', [
            'project_brief_id' => $brief->id,
            'project_meta_data_id' => 11,
            'value' => 'Somos una empresa industrial.',
        ]);
        $this->assertDatabaseHas('project_brief_answers', [
            'project_brief_id' => $brief->id,
            'project_meta_data_id' => 13,
            'value' => 'on',
        ]);
    }

    public function test_guest_can_create_project_brief_without_login(): void
    {
        Storage::fake('public');

        Schema::getConnection()->table('project_statuses')->insert(['id' => 3, 'name' => 'Running']);
        Schema::getConnection()->table('project_types')->insert(['id' => 1, 'name' => 'Web']);

        $project = Project::query()->create([
            'name' => 'Proyecto Publico',
            'type_id' => 1,
            'status_id' => 3,
        ]);

        Schema::getConnection()->table('project_meta_datas')->insert([
            ['id' => 20, 'parent_id' => null, 'value' => 'Archivos', 'type_id' => 5, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'parent_id' => 20, 'value' => 'Logo', 'type_id' => null, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'parent_id' => null, 'value' => 'Accesos', 'type_id' => 1, 'weight' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'parent_id' => 30, 'value' => 'Cuentas', 'type_id' => null, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->assertGuest();

        $formResponse = $this->get(route('projects.briefs.create', $project));

        $formResponse->assertOk();
        $formResponse->assertSee('Crear brief');
        $formResponse->assertSee('type="file"', false);
        $formResponse->assertSee('access_logins', false);

        $submitResponse = $this->post(route('projects.briefs.store', $project), [
            'title' => 'Brief desde cliente',
            'files' => [
                21 => UploadedFile::fake()->create('logo.pdf', 100, 'application/pdf'),
            ],
            'access_logins' => [
                [
                    'name' => 'WordPress',
                    'user' => 'cliente',
                    'password' => 'secreto',
                    'url' => 'https://example.com/wp-admin',
                ],
            ],
        ]);

        $brief = ProjectBrief::query()->firstOrFail();

        $submitResponse->assertRedirect(route('public.briefs.edit', $brief->public_token));
        $this->assertDatabaseHas('project_logins', [
            'project_id' => $project->id,
            'name' => 'WordPress',
            'user' => 'cliente',
            'password' => 'secreto',
            'url' => 'https://example.com/wp-admin',
        ]);

        $answer = DB::table('project_brief_answers')
            ->where('project_brief_id', $brief->id)
            ->where('project_meta_data_id', 21)
            ->value('value');

        $storedFile = json_decode((string) $answer, true);

        $this->assertIsArray($storedFile);
        $this->assertSame('logo.pdf', $storedFile['name']);
        Storage::disk('public')->assertExists($storedFile['path']);
    }

    public function test_create_brief_uses_answered_project_meta_groups_when_the_project_has_legacy_answers(): void
    {
        Schema::getConnection()->table('project_statuses')->insert(['id' => 3, 'name' => 'Running']);
        Schema::getConnection()->table('project_types')->insert(['id' => 1, 'name' => 'Web']);

        $project = Project::query()->create([
            'name' => 'Proyecto con metadata',
            'type_id' => 1,
            'status_id' => 3,
        ]);

        Schema::getConnection()->table('project_meta_datas')->insert([
            ['id' => 40, 'parent_id' => null, 'value' => 'Grupo lleno', 'type_id' => 1, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 41, 'parent_id' => 40, 'value' => 'Pregunta llena', 'type_id' => 4, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 50, 'parent_id' => null, 'value' => 'Grupo vacio', 'type_id' => 1, 'weight' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 51, 'parent_id' => 50, 'value' => 'Pregunta vacia', 'type_id' => 4, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('project_metas')->insert([
            'project_id' => $project->id,
            'meta_data_id' => 41,
            'value' => 'Respuesta previa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('projects.briefs.create', $project));

        $response->assertOk();
        $response->assertSee('Grupo lleno');
        $response->assertSee('Pregunta llena');
        $response->assertDontSee('Grupo vacio');
        $response->assertDontSee('Pregunta vacia');
    }

    public function test_create_brief_uses_web_design_campaign_groups_when_project_has_no_legacy_answers(): void
    {
        Schema::getConnection()->table('project_statuses')->insert(['id' => 3, 'name' => 'Running']);
        Schema::getConnection()->table('project_types')->insert(['id' => 1, 'name' => 'Web']);

        $project = Project::query()->create([
            'name' => 'Proyecto web',
            'type_id' => 1,
            'status_id' => 3,
        ]);

        Schema::getConnection()->table('project_meta_datas')->insert([
            ['id' => 60, 'parent_id' => null, 'value' => 'Grupo campaña', 'type_id' => 1, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 61, 'parent_id' => 60, 'value' => 'Pregunta campaña', 'type_id' => 4, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 70, 'parent_id' => null, 'value' => 'Grupo fuera campaña', 'type_id' => 1, 'weight' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 71, 'parent_id' => 70, 'value' => 'Pregunta fuera campaña', 'type_id' => 4, 'weight' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('campaigns')->insert([
            'id' => 4,
            'name' => 'Brief Web Design',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('campaign_project_meta_data')->insert([
            'campaign_id' => 4,
            'project_meta_data_id' => 61,
        ]);

        $response = $this->get(route('projects.briefs.create', $project));

        $response->assertOk();
        $response->assertSee('Grupo campaña');
        $response->assertSee('Pregunta campaña');
        $response->assertDontSee('Grupo fuera campaña');
        $response->assertDontSee('Pregunta fuera campaña');
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
