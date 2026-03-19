<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TasksPointsSummaryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('tasks');
        Schema::dropIfExists('project_users');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('task_statuses');
        Schema::dropIfExists('task_types');

        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('status_id')->nullable();
            $table->decimal('weight', 8, 2)->default(0);
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('project_users', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('task_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('pending')->default(true);
            $table->unsignedBigInteger('status_id')->default(1);
            $table->string('alias')->nullable();
            $table->string('color')->nullable();
            $table->string('background_color')->nullable();
            $table->unsignedInteger('weight')->default(0);
            $table->timestamps();
        });

        Schema::create('task_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('copy')->nullable();
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

        if (! Schema::hasColumn('users', 'status_id')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->unsignedBigInteger('status_id')->nullable()->default(1);
            });
        }

        if (! Schema::hasColumn('users', 'image_url')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('image_url')->nullable();
            });
        }
    }

    public function test_it_shows_sum_of_points_for_filtered_tasks(): void
    {
        $user = User::factory()->create([
            'status_id' => 1,
        ]);

        $this->grantModulePermissions($user, '/tasks', ['list']);
        $this->actingAs($user->refresh());

        DB::table('projects')->insert([
            'id' => 1,
            'name' => 'Proyecto Demo',
            'status_id' => 3,
            'weight' => 1,
            'color' => '#000000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('task_statuses')->insert([
            'id' => 1,
            'name' => 'Pendiente',
            'pending' => true,
            'status_id' => 1,
            'weight' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Task::create([
            'name' => 'Tarea A',
            'project_id' => 1,
            'user_id' => $user->id,
            'status_id' => 1,
            'points' => 1.25,
            'due_date' => now(),
        ]);

        Task::create([
            'name' => 'Tarea B',
            'project_id' => 1,
            'user_id' => $user->id,
            'status_id' => 1,
            'points' => 0.75,
            'due_date' => now(),
        ]);

        Task::create([
            'name' => 'Tarea C',
            'project_id' => 1,
            'user_id' => null,
            'status_id' => 1,
            'points' => 5,
            'due_date' => now(),
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee('Descargar CSV');
        $response->assertSee('2.00 pts');
        $response->assertDontSee('5.00 pts');
    }

    public function test_it_exports_filtered_tasks_as_csv(): void
    {
        $user = User::factory()->create([
            'status_id' => 1,
        ]);
        $otherUser = User::factory()->create([
            'status_id' => 1,
        ]);

        $this->grantModulePermissions($user, '/tasks', ['list']);
        $this->actingAs($user->refresh());

        DB::table('projects')->insert([
            'id' => 1,
            'name' => 'Proyecto Demo',
            'status_id' => 3,
            'weight' => 1,
            'color' => '#000000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('task_statuses')->insert([
            'id' => 1,
            'name' => 'Pendiente',
            'pending' => true,
            'status_id' => 1,
            'weight' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Task::create([
            'name' => 'Tarea Alpha',
            'project_id' => 1,
            'user_id' => $user->id,
            'status_id' => 1,
            'value_generated' => true,
            'points' => 3.50,
            'due_date' => now(),
        ]);

        Task::create([
            'name' => 'Tarea Beta',
            'project_id' => 1,
            'user_id' => $user->id,
            'status_id' => 1,
            'value_generated' => true,
            'points' => 2.25,
            'due_date' => now(),
        ]);

        Task::create([
            'name' => 'Tarea Alpha Externa',
            'project_id' => 1,
            'user_id' => $otherUser->id,
            'status_id' => 1,
            'value_generated' => true,
            'points' => 5.00,
            'due_date' => now(),
        ]);

        $response = $this->get(route('tasks.export', [
            'q' => 'Alpha',
            'user_id' => $user->id,
            'from_date' => now()->startOfMonth()->toDateString(),
            'to_date' => now()->endOfMonth()->toDateString(),
        ]));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment; filename=', (string) $response->headers->get('content-disposition'));

        $csvContent = $response->streamedContent();
        $this->assertStringContainsString('ID,Tarea,Proyecto,Estado,Responsable,"Genera valor",Puntos,Vence,Entrega,Creada', $csvContent);
        $this->assertStringContainsString('Tarea Alpha', $csvContent);
        $this->assertStringNotContainsString('Tarea Beta', $csvContent);
        $this->assertStringNotContainsString('Tarea Alpha Externa', $csvContent);
    }

    public function test_tasks_export_with_empty_user_filter_includes_all_users_tasks(): void
    {
        $user = User::factory()->create([
            'status_id' => 1,
        ]);
        $otherUser = User::factory()->create([
            'status_id' => 1,
        ]);

        $this->grantModulePermissions($user, '/tasks', ['list']);
        $this->actingAs($user->refresh());

        DB::table('projects')->insert([
            'id' => 1,
            'name' => 'Proyecto Demo',
            'status_id' => 3,
            'weight' => 1,
            'color' => '#000000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('task_statuses')->insert([
            'id' => 1,
            'name' => 'Pendiente',
            'pending' => true,
            'status_id' => 1,
            'weight' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Task::create([
            'name' => 'Tarea Alpha',
            'project_id' => 1,
            'user_id' => $user->id,
            'status_id' => 1,
            'value_generated' => true,
            'points' => 3.50,
            'due_date' => now(),
        ]);

        Task::create([
            'name' => 'Tarea Alpha Externa',
            'project_id' => 1,
            'user_id' => $otherUser->id,
            'status_id' => 1,
            'value_generated' => true,
            'points' => 5.00,
            'due_date' => now(),
        ]);

        $response = $this->get('/tasks/export?from_date='
            .now()->startOfMonth()->toDateString()
            .'&to_date='
            .now()->endOfMonth()->toDateString()
            .'&q=Alpha&status_id=&project_id=&user_id=');

        $response->assertOk();

        $csvContent = $response->streamedContent();
        $this->assertStringContainsString('Tarea Alpha', $csvContent);
        $this->assertStringContainsString('Tarea Alpha Externa', $csvContent);
    }

    public function test_tasks_export_requires_list_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('tasks.export'));

        $response->assertForbidden();
    }

    public function test_client_role_loads_team_tasks_by_default(): void
    {
        $client = User::factory()->create([
            'role_id' => 4,
            'status_id' => 1,
        ]);

        $teamUserA = User::factory()->create(['status_id' => 1]);
        $teamUserB = User::factory()->create(['status_id' => 1]);

        $this->grantModulePermissions($client, '/tasks', ['list']);
        $this->actingAs($client->refresh());

        DB::table('projects')->insert([
            [
                'id' => 10,
                'name' => 'Proyecto Cliente',
                'status_id' => 3,
                'weight' => 1,
                'color' => '#000000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 20,
                'name' => 'Proyecto Oculto',
                'status_id' => 3,
                'weight' => 2,
                'color' => '#111111',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('project_users')->insert([
            'project_id' => 10,
            'user_id' => $client->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('task_statuses')->insert([
            'id' => 1,
            'name' => 'Pendiente',
            'pending' => true,
            'status_id' => 1,
            'weight' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Task::create([
            'name' => 'Tarea Equipo A',
            'project_id' => 10,
            'user_id' => $teamUserA->id,
            'status_id' => 1,
            'points' => 1.00,
            'due_date' => now(),
        ]);

        Task::create([
            'name' => 'Tarea Equipo B',
            'project_id' => 10,
            'user_id' => $teamUserB->id,
            'status_id' => 1,
            'points' => 2.00,
            'due_date' => now(),
        ]);

        Task::create([
            'name' => 'Tarea Oculta',
            'project_id' => 20,
            'user_id' => $teamUserA->id,
            'status_id' => 1,
            'points' => 9.00,
            'due_date' => now(),
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee('3.00 pts');
        $response->assertDontSee('9.00 pts');
        $response->assertSee('Tarea Equipo A');
        $response->assertSee('Tarea Equipo B');
        $response->assertDontSee('Tarea Oculta');
        $response->assertViewHas('filters', fn (array $filters): bool => $filters['user_id'] === null);
    }

    public function test_tasks_index_renders_quick_empty_line_before_task_rows(): void
    {
        $user = User::factory()->create([
            'status_id' => 1,
        ]);

        $this->grantModulePermissions($user, '/tasks', ['list']);
        $this->actingAs($user->refresh());

        DB::table('task_statuses')->insert([
            'id' => 1,
            'name' => 'Pendiente',
            'pending' => true,
            'status_id' => 1,
            'weight' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Task::create([
            'name' => 'Tarea existente',
            'user_id' => $user->id,
            'status_id' => 1,
            'due_date' => now(),
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee('id="tasks-inline-quick-project-toggle"', false);
        $response->assertSee('id="tasks-inline-quick-user-toggle"', false);
        $response->assertSee('id="tasks-inline-quick-name"', false);
        $response->assertSee('@submit.prevent="submitQuickTask"', false);
        $response->assertSee('@task-created="handleTaskCreated($event.detail.task)"', false);
        $response->assertSee('x-for="recentTask in recentTasks"', false);
        $response->assertSee("x-text=\"recentTask.projectInitials ?? 'SP'\"", false);
        $response->assertSee('updateRecentTaskProject(recentTask', false);
        $response->assertSee('updateRecentTaskUser(recentTask', false);
        $response->assertSee('updateRecentTaskStatus(', false);
        $response->assertSee('loadPanel(recentTask.showUrl)', false);
        $response->assertSee('recentTask.statusBackgroundColor', false);
        $response->assertSee('x-if="recentTask.userAvatar"', false);
        $response->assertSee('x-text="selectedProjectInitials"', false);
        $response->assertSee(':style="`background:${selectedProjectColor}`"', false);
        $response->assertSee('selectedUserAvatar:', false);
        $response->assertSee("storageProjectKey: 'tasks.inline.quick.project_id'", false);
        $response->assertSee("storageUserKey: 'tasks.inline.quick.user_id'", false);
        $response->assertSee('data-task-row-status-select="', false);
        $response->assertSee('data-task-row-project-toggle="', false);
        $response->assertSee('data-task-row-user-toggle="', false);
        $response->assertSeeInOrder(['id="tasks-inline-quick-name"', 'Tarea existente'], false);
    }

    public function test_store_assigns_task_to_authenticated_user_when_user_id_is_missing(): void
    {
        $user = User::factory()->create([
            'status_id' => 1,
        ]);

        $this->grantModulePermissions($user, '/tasks', ['create']);
        $this->actingAs($user->refresh());

        DB::table('task_statuses')->insert([
            'id' => 1,
            'name' => 'Pendiente',
            'pending' => true,
            'status_id' => 1,
            'weight' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post(route('tasks.store'), [
            'name' => 'Tarea rápida',
            'status_id' => 1,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'name' => 'Tarea rápida',
            'user_id' => $user->id,
            'status_id' => 1,
            'creator_user_id' => $user->id,
            'updator_user_id' => $user->id,
        ]);
    }

    public function test_store_returns_json_for_async_requests(): void
    {
        $user = User::factory()->create([
            'status_id' => 1,
        ]);

        $this->grantModulePermissions($user, '/tasks', ['create']);
        $this->actingAs($user->refresh());

        DB::table('task_statuses')->insert([
            'id' => 1,
            'name' => 'Pendiente',
            'pending' => true,
            'status_id' => 1,
            'weight' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson(route('tasks.store'), [
            'name' => 'Tarea async',
            'status_id' => 1,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Tarea creada.');
        $response->assertJsonPath('task.name', 'Tarea async');
        $response->assertJsonPath('task.user_id', $user->id);

        $this->assertDatabaseHas('tasks', [
            'name' => 'Tarea async',
            'user_id' => $user->id,
            'status_id' => 1,
        ]);
    }

    public function test_quick_assign_updates_task_project_user_and_status(): void
    {
        $editor = User::factory()->create([
            'status_id' => 1,
        ]);
        $assignee = User::factory()->create([
            'status_id' => 1,
        ]);

        $this->grantModulePermissions($editor, '/tasks', ['update']);
        $this->actingAs($editor->refresh());

        DB::table('projects')->insert([
            [
                'id' => 11,
                'name' => 'Proyecto Inicial',
                'status_id' => 3,
                'weight' => 1,
                'color' => '#000000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'name' => 'Proyecto Nuevo',
                'status_id' => 3,
                'weight' => 2,
                'color' => '#111111',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('task_statuses')->insert([
            [
                'id' => 1,
                'name' => 'Pendiente',
                'pending' => true,
                'status_id' => 1,
                'weight' => 1,
                'color' => '#312e81',
                'background_color' => '#eef2ff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'En progreso',
                'pending' => true,
                'status_id' => 1,
                'weight' => 2,
                'color' => '#0f766e',
                'background_color' => '#ccfbf1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $task = Task::create([
            'name' => 'Tarea editable',
            'project_id' => 11,
            'value_generated' => true,
            'status_id' => 1,
            'due_date' => now(),
        ]);

        $response = $this->postJson(route('tasks.quick-assign', $task), [
            'project_id' => 12,
            'user_id' => $assignee->id,
            'status_id' => 2,
            'value_generated' => false,
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Asignación actualizada.');
        $response->assertJsonPath('task.id', $task->id);
        $response->assertJsonPath('task.project_id', 12);
        $response->assertJsonPath('task.user_id', $assignee->id);
        $response->assertJsonPath('task.status_id', 2);
        $response->assertJsonPath('task.status.name', 'En progreso');
        $response->assertJsonPath('task.value_generated', false);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'project_id' => 12,
            'user_id' => $assignee->id,
            'status_id' => 2,
            'value_generated' => false,
            'updator_user_id' => $editor->id,
        ]);
    }

    public function test_update_keeps_value_generated_when_field_is_missing(): void
    {
        $editor = User::factory()->create([
            'status_id' => 1,
        ]);

        $this->grantModulePermissions($editor, '/tasks', ['update']);
        $this->actingAs($editor->refresh());

        DB::table('task_statuses')->insert([
            'id' => 1,
            'name' => 'Pendiente',
            'pending' => true,
            'status_id' => 1,
            'weight' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $task = Task::create([
            'name' => 'Tarea editable',
            'status_id' => 1,
            'value_generated' => true,
            'due_date' => now(),
        ]);

        $response = $this->put(route('tasks.update', $task), [
            'name' => 'Tarea editable actualizada',
            'status_id' => 1,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Tarea editable actualizada',
            'value_generated' => true,
            'updator_user_id' => $editor->id,
        ]);
    }
}
