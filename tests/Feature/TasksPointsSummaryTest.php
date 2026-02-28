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
        $response->assertSee('2.00 pts');
        $response->assertDontSee('5.00 pts');
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
        $response->assertSee('x-text="\'Proyecto: \' + selectedProjectName"', false);
        $response->assertSee('x-text="selectedProjectInitials"', false);
        $response->assertSee('x-text="\'Usuario: \' + selectedUserName"', false);
        $response->assertSee("storageProjectKey: 'tasks.inline.quick.project_id'", false);
        $response->assertSee("storageUserKey: 'tasks.inline.quick.user_id'", false);
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
}
