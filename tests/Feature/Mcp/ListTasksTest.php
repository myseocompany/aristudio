<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\AriStudioServer;
use App\Mcp\Tools\CreateTask;
use App\Mcp\Tools\ListTasks;
use App\Mcp\Tools\UpdateTask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ListTasksTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('tasks');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('task_statuses');

        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('task_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('alias')->nullable();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('caption')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->unsignedBigInteger('updator_user_id')->nullable();
            $table->integer('priority')->nullable();
            $table->decimal('points', 8, 2)->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->boolean('value_generated')->default(false);
            $table->boolean('not_billing')->default(false);
            $table->timestamps();
        });
    }

    public function test_it_lists_tasks_with_filters(): void
    {
        $user = User::factory()->create(['name' => 'Nicolas']);

        DB::table('projects')->insert([
            'id' => 10,
            'name' => 'AMIA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('task_statuses')->insert([
            'id' => 20,
            'name' => 'Pendiente',
            'alias' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Task::query()->create([
            'name' => 'Preparar reporte MCP',
            'description' => 'Reporte de tareas expuesto por MCP',
            'project_id' => 10,
            'user_id' => $user->id,
            'status_id' => 20,
            'priority' => 2,
            'points' => 3.5,
            'due_date' => '2026-06-01 10:30:00',
            'value_generated' => true,
        ]);

        Task::query()->create([
            'name' => 'Otra tarea',
            'project_id' => 10,
            'status_id' => 20,
        ]);

        AriStudioServer::tool(ListTasks::class, [
            'project_id' => 10,
            'status_id' => 20,
            'user_id' => $user->id,
            'q' => 'MCP',
            'limit' => 10,
        ])
            ->assertOk()
            ->assertSee([
                '"count": 1',
                'Preparar reporte MCP',
                '"project": "AMIA"',
                '"user": "Nicolas"',
            ])
            ->assertDontSee('Otra tarea');
    }

    public function test_it_validates_limit(): void
    {
        AriStudioServer::tool(ListTasks::class, [
            'limit' => 101,
        ])->assertHasErrors([
            'The limit field must not be greater than 100.',
        ]);
    }

    public function test_create_task_requires_create_permission(): void
    {
        $user = User::factory()->create(['status_id' => 1]);

        DB::table('task_statuses')->insert([
            'id' => 20,
            'name' => 'Pendiente',
            'alias' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AriStudioServer::actingAs($user)
            ->tool(CreateTask::class, [
                'name' => 'Tarea sin permiso',
                'status_id' => 20,
            ])
            ->assertHasErrors(['No autorizado.']);
    }

    public function test_create_task_creates_task_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Nicolas',
            'status_id' => 1,
        ]);
        $assignee = User::factory()->create(['status_id' => 1]);

        $this->grantModulePermissions($user, '/tasks', ['create']);

        DB::table('projects')->insert([
            'id' => 10,
            'name' => 'AMIA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('task_statuses')->insert([
            'id' => 20,
            'name' => 'Pendiente',
            'alias' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AriStudioServer::actingAs($user->refresh())
            ->tool(CreateTask::class, [
                'name' => 'Crear pauta MCP',
                'description' => 'Brief generado desde Claude',
                'project_id' => 10,
                'user_id' => $assignee->id,
                'status_id' => 20,
                'due_date' => '2026-06-01 10:30:00',
                'priority' => 3,
                'points' => 2.5,
                'value_generated' => true,
            ])
            ->assertOk()
            ->assertSee([
                'Tarea creada.',
                'Crear pauta MCP',
                '"project_id": 10',
                '"user_id": '.$assignee->id,
            ]);

        $this->assertDatabaseHas('tasks', [
            'name' => 'Crear pauta MCP',
            'description' => 'Brief generado desde Claude',
            'project_id' => 10,
            'user_id' => $assignee->id,
            'status_id' => 20,
            'creator_user_id' => $user->id,
            'updator_user_id' => $user->id,
            'priority' => 3,
            'value_generated' => true,
        ]);
    }

    public function test_create_task_defaults_assignee_to_authenticated_user(): void
    {
        $user = User::factory()->create(['status_id' => 1]);

        $this->grantModulePermissions($user, '/tasks', ['create']);

        DB::table('task_statuses')->insert([
            'id' => 20,
            'name' => 'Pendiente',
            'alias' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AriStudioServer::actingAs($user->refresh())
            ->tool(CreateTask::class, [
                'name' => 'Tarea asignada por defecto',
                'status_id' => 20,
            ])
            ->assertOk();

        $this->assertDatabaseHas('tasks', [
            'name' => 'Tarea asignada por defecto',
            'user_id' => $user->id,
            'status_id' => 20,
            'priority' => 1,
            'value_generated' => true,
            'not_billing' => false,
        ]);
    }

    public function test_create_task_validates_required_fields(): void
    {
        $user = User::factory()->create(['status_id' => 1]);

        $this->grantModulePermissions($user, '/tasks', ['create']);

        AriStudioServer::actingAs($user->refresh())
            ->tool(CreateTask::class, [])
            ->assertHasErrors([
                'The name field is required.',
                'The status id field is required.',
            ]);
    }

    public function test_update_task_requires_update_permission(): void
    {
        $user = User::factory()->create(['status_id' => 1]);

        DB::table('task_statuses')->insert([
            'id' => 20,
            'name' => 'Pendiente',
            'alias' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $task = Task::query()->create([
            'name' => 'Tarea sin permiso',
            'status_id' => 20,
        ]);

        AriStudioServer::actingAs($user)
            ->tool(UpdateTask::class, [
                'id' => $task->id,
                'name' => 'No debe actualizar',
            ])
            ->assertHasErrors(['No autorizado.']);
    }

    public function test_update_task_updates_sent_fields(): void
    {
        $editor = User::factory()->create(['status_id' => 1]);
        $assignee = User::factory()->create(['status_id' => 1]);

        $this->grantModulePermissions($editor, '/tasks', ['update']);

        DB::table('projects')->insert([
            'id' => 10,
            'name' => 'AMIA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('task_statuses')->insert([
            'id' => 20,
            'name' => 'Pendiente',
            'alias' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $task = Task::query()->create([
            'name' => 'Tarea antes',
            'status_id' => 20,
            'value_generated' => true,
            'not_billing' => false,
        ]);

        AriStudioServer::actingAs($editor->refresh())
            ->tool(UpdateTask::class, [
                'id' => $task->id,
                'name' => 'Tarea actualizada MCP',
                'description' => 'Descripción actualizada',
                'project_id' => 10,
                'user_id' => $assignee->id,
                'due_date' => '2026-07-02 09:15:00',
                'priority' => 4,
                'points' => 5.25,
                'value_generated' => false,
                'not_billing' => true,
            ])
            ->assertOk()
            ->assertSee([
                'Tarea actualizada.',
                'Tarea actualizada MCP',
                '"project_id": 10',
                '"user_id": '.$assignee->id,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Tarea actualizada MCP',
            'description' => 'Descripción actualizada',
            'project_id' => 10,
            'user_id' => $assignee->id,
            'status_id' => 20,
            'priority' => 4,
            'value_generated' => false,
            'not_billing' => true,
            'updator_user_id' => $editor->id,
        ]);
    }

    public function test_update_task_keeps_booleans_when_omitted_and_can_clear_nullable_fields(): void
    {
        $editor = User::factory()->create(['status_id' => 1]);

        $this->grantModulePermissions($editor, '/tasks', ['update']);

        DB::table('task_statuses')->insert([
            'id' => 20,
            'name' => 'Pendiente',
            'alias' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $task = Task::query()->create([
            'name' => 'Tarea con datos',
            'description' => 'Debe limpiarse',
            'user_id' => $editor->id,
            'status_id' => 20,
            'due_date' => '2026-07-02 09:15:00',
            'value_generated' => true,
            'not_billing' => true,
        ]);

        AriStudioServer::actingAs($editor->refresh())
            ->tool(UpdateTask::class, [
                'id' => $task->id,
                'description' => null,
                'user_id' => null,
                'due_date' => null,
            ])
            ->assertOk();

        $task->refresh();

        $this->assertNull($task->description);
        $this->assertNull($task->user_id);
        $this->assertNull($task->due_date);
        $this->assertTrue((bool) $task->value_generated);
        $this->assertTrue((bool) $task->not_billing);
        $this->assertSame($editor->id, $task->updator_user_id);
    }

    public function test_update_task_validates_id(): void
    {
        $user = User::factory()->create(['status_id' => 1]);

        $this->grantModulePermissions($user, '/tasks', ['update']);

        AriStudioServer::actingAs($user->refresh())
            ->tool(UpdateTask::class, [
                'id' => 999,
                'name' => 'No existe',
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

    public function test_remote_server_accepts_passport_access_token(): void
    {
        Passport::actingAs(User::factory()->create());

        $response = $this->postJson('/mcp/aristudio', $this->initializePayload());

        $response->assertOk();
        $response->assertJsonPath('result.serverInfo.name', 'Ari Studio Server');
    }

    public function test_oauth_discovery_routes_are_available(): void
    {
        $this->getJson('/.well-known/oauth-protected-resource/mcp/aristudio')
            ->assertOk()
            ->assertJsonPath('scopes_supported.0', 'mcp:use');

        $this->getJson('/.well-known/oauth-authorization-server/mcp/aristudio')
            ->assertOk()
            ->assertJsonPath('grant_types_supported.0', 'authorization_code')
            ->assertJsonPath('scopes_supported.0', 'mcp:use');
    }

    public function test_oauth_dynamic_client_registration_creates_public_client(): void
    {
        $response = $this->postJson('/oauth/register', [
            'client_name' => 'Claude',
            'redirect_uris' => [
                'https://claude.ai/api/mcp/auth_callback',
            ],
        ]);

        $response->assertOk();
        $response->assertJsonPath('response_types.0', 'code');
        $response->assertJsonPath('scope', 'mcp:use');
        $response->assertJsonPath('token_endpoint_auth_method', 'none');
    }

    public function test_oauth_dynamic_client_registration_rejects_untrusted_redirects(): void
    {
        $this->postJson('/oauth/register', [
            'client_name' => 'Untrusted',
            'redirect_uris' => [
                'https://example.com/callback',
            ],
        ])->assertUnprocessable();
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
