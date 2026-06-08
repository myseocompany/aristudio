<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\AriStudioServer;
use App\Mcp\Tools\ListProjects;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ListProjectsTest extends TestCase
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
            $table->string('color')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->integer('weekly_pieces')->nullable();
            $table->decimal('ads_budget', 12, 2)->nullable();
            $table->integer('lead_target')->nullable();
            $table->integer('monthly_points_goal')->nullable();
            $table->decimal('sales', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('project_users', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    public function test_it_lists_projects_with_filters(): void
    {
        $user = User::factory()->create(['status_id' => 1]);

        DB::table('project_statuses')->insert([
            'id' => 3,
            'name' => 'Running',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('project_types')->insert([
            'id' => 2,
            'name' => 'Marketing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $amia = Project::query()->create([
            'name' => 'AMIA',
            'description' => 'Proyecto de contenidos MCP',
            'type_id' => 2,
            'status_id' => 3,
            'color' => '#10b981',
            'weight' => 1,
        ]);

        Project::query()->create([
            'name' => 'Otro proyecto',
            'description' => 'No debe aparecer',
            'type_id' => 2,
            'status_id' => 3,
            'weight' => 2,
        ]);

        DB::table('project_users')->insert([
            'project_id' => $amia->id,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AriStudioServer::tool(ListProjects::class, [
            'status_id' => 3,
            'type_id' => 2,
            'q' => 'MCP',
            'limit' => 10,
        ])
            ->assertOk()
            ->assertSee([
                '"count": 1',
                'AMIA',
                'Proyecto de contenidos MCP',
                '"status": "Running"',
                '"type": "Marketing"',
                '"users_count": 1',
            ])
            ->assertDontSee('Otro proyecto');
    }

    public function test_it_lists_active_projects_by_default(): void
    {
        DB::table('project_statuses')->insert([
            [
                'id' => 3,
                'name' => 'Running',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Paused',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Project::query()->create([
            'name' => 'Proyecto activo',
            'status_id' => 3,
            'weight' => 1,
        ]);

        Project::query()->create([
            'name' => 'Proyecto pausado',
            'status_id' => 4,
            'weight' => 2,
        ]);

        AriStudioServer::tool(ListProjects::class, [
            'limit' => 10,
        ])
            ->assertOk()
            ->assertSee([
                '"count": 1',
                'Proyecto activo',
            ])
            ->assertDontSee('Proyecto pausado');
    }

    public function test_it_validates_limit(): void
    {
        AriStudioServer::tool(ListProjects::class, [
            'limit' => 101,
        ])->assertHasErrors([
            'The limit field must not be greater than 100.',
        ]);
    }

    public function test_remote_server_requires_oauth_access_token(): void
    {
        $this->postJson('/mcp/aristudio', $this->initializePayload())
            ->assertUnauthorized();
    }

    /**
     * @return array<string, mixed>
     */
    private function initializePayload(): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => 'test-request',
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2025-11-25',
                'capabilities' => [],
                'clientInfo' => [
                    'name' => 'phpunit',
                    'version' => '1.0.0',
                ],
            ],
        ];
    }
}
