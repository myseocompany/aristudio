<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\AriStudioServer;
use App\Mcp\Tools\UpdateProject;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UpdateProjectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('projects');

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
    }

    public function test_update_project_updates_description(): void
    {
        $editor = User::factory()->create(['status_id' => 1]);

        $this->grantModulePermissions($editor, '/projects', ['update']);

        $project = Project::query()->create([
            'name' => 'Proyecto inicial',
            'description' => 'Descripcion anterior',
        ]);

        AriStudioServer::actingAs($editor->refresh())
            ->tool(UpdateProject::class, [
                'id' => $project->id,
                'description' => 'Descripcion actualizada desde MCP',
            ])
            ->assertOk()
            ->assertSee([
                'Proyecto actualizado.',
                'Proyecto inicial',
                'Descripcion actualizada desde MCP',
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Proyecto inicial',
            'description' => 'Descripcion actualizada desde MCP',
        ]);
    }

    public function test_update_project_validates_missing_project(): void
    {
        $editor = User::factory()->create(['status_id' => 1]);

        $this->grantModulePermissions($editor, '/projects', ['update']);

        AriStudioServer::actingAs($editor->refresh())
            ->tool(UpdateProject::class, [
                'id' => 999,
                'description' => 'No existe',
            ])
            ->assertHasErrors([
                'The selected id is invalid.',
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
