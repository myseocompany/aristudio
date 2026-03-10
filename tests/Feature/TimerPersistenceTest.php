<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TimerPersistenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_statuses');
        Schema::dropIfExists('projects');

        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->integer('weight')->default(0);
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('status_id')->default(1);
            $table->unsignedTinyInteger('priority')->nullable();
            $table->decimal('points', 8, 2)->nullable();
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->unsignedBigInteger('updator_user_id')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->boolean('value_generated')->default(false);
            $table->timestamps();
        });
        Schema::create('task_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('pending')->default(true);
            $table->string('color')->nullable();
            $table->string('background_color')->nullable();
        });
    }

    public function test_timer_state_survives_reload_and_resume(): void
    {
        $user = User::factory()->create();
        $this->grantModulePermissions($user, '/timer', ['read', 'update']);
        $this->actingAs($user->refresh());

        $this->postJson(route('timer.start'), [
            'task_label' => 'Llamada con cliente',
        ])->assertOk()->assertJson([
            'running' => true,
            'elapsed' => 0,
        ]);

        $this->travel(90)->seconds();

        $status = $this->getJson(route('timer.status'))->assertOk()->json();
        $this->assertTrue($status['running']);
        $this->assertEqualsWithDelta(90, $status['elapsed'], 2);
        $this->assertSame('Llamada con cliente', $status['task_label']);

        $paused = $this->postJson(route('timer.pause'))->assertOk()->json();
        $this->assertFalse($paused['running']);
        $this->assertEqualsWithDelta($status['elapsed'], $paused['elapsed'], 1);

        $this->travel(60)->seconds();

        $this->postJson(route('timer.start'), [
            'task_label' => 'Llamada con cliente',
        ])->assertOk()->assertJson([
            'running' => true,
        ]);

        $this->travel(30)->seconds();

        $resumed = $this->getJson(route('timer.status'))->assertOk()->json();
        $this->assertTrue($resumed['running']);
        $this->assertEqualsWithDelta($paused['elapsed'] + 30, $resumed['elapsed'], 2);
    }

    public function test_timer_index_encodes_task_payload_for_alpine(): void
    {
        $user = User::factory()->create();
        $this->grantModulePermissions($user, '/timer', ['list']);

        $project = Project::query()->create([
            'name' => 'MQE',
            'status_id' => 3,
        ]);

        $task = Task::query()->create([
            'name' => 'Analizar "mejor" contenido\'s',
            'project_id' => $project->id,
            'user_id' => $user->id,
            'status_id' => 1,
        ]);

        $expectedName = 'Analizar \\u0022mejor\\u0022 contenido\\u0027s';
        $expected = sprintf(
            "setTask(%d, '%s', %d, 'MQE')",
            $task->id,
            $expectedName,
            $project->id
        );

        $this->actingAs($user)
            ->get(route('timer.index'))
            ->assertOk()
            ->assertSee($expected, false)
            ->assertSee('async setTask(id, label, projectId = \'\', projectName = \'\', autoStart = true) {', false)
            ->assertSee('this.manualTaskName = label ? String(label) : \'\';', false)
            ->assertSee('this.manualTaskName = data?.task_label || \'\';', false)
            ->assertSee('if (autoStart && !this.running && this.elapsed < this.maxSeconds) {', false)
            ->assertSee('await this.start();', false)
            ->assertSee('taskData.project_name ?? \'\',', false)
            ->assertSee('                        false', false)
            ->assertSee('window.scrollTo({ top: 0, behavior: \'smooth\' });', false);
    }

    public function test_store_uses_server_elapsed_and_clears_session(): void
    {
        $user = User::factory()->create();
        $this->grantModulePermissions($user, '/timer', ['create', 'read', 'update']);
        $this->actingAs($user->refresh());

        $this->postJson(route('timer.start'), [
            'task_label' => 'Demo timer',
        ])->assertOk();

        $this->travel(125)->seconds();

        $response = $this->postJson(route('timer.store'), [
            'name' => 'Demo timer',
            'seconds' => 1,
        ])->assertOk()->json();

        $this->assertTrue($response['ok']);
        $this->assertEqualsWithDelta(125, $response['seconds'], 2);

        $task = Task::first();
        $this->assertNotNull($task);
        $this->assertSame('Demo timer', $task->name);
        $this->assertEqualsWithDelta(round(125 / 3600, 2), (float) $task->points, 0.01);

        $this->getJson(route('timer.status'))
            ->assertOk()
            ->assertJson([
                'running' => false,
                'elapsed' => 0,
            ]);
    }

    public function test_store_updates_selected_task_instead_of_creating_a_new_one(): void
    {
        $user = User::factory()->create();
        $this->grantModulePermissions($user, '/timer', ['create', 'read', 'update']);
        $this->actingAs($user->refresh());

        $project = Project::query()->create([
            'name' => 'Proyecto Timer',
            'status_id' => 3,
        ]);

        $task = Task::query()->create([
            'name' => 'Tarea programada',
            'project_id' => $project->id,
            'user_id' => $user->id,
            'status_id' => 1,
            'priority' => 1,
        ]);

        $this->postJson(route('timer.start'), [
            'task_id' => $task->id,
            'task_label' => $task->name,
            'project_id' => $project->id,
            'project_name' => $project->name,
        ])->assertOk()->assertJson([
            'task_id' => $task->id,
            'task_label' => $task->name,
        ]);

        $this->travel(600)->seconds();

        $response = $this->postJson(route('timer.store'), [
            'name' => $task->name,
            'project_id' => $project->id,
            'seconds' => 1,
        ])->assertOk()->json();

        $this->assertTrue($response['ok']);
        $this->assertSame($task->id, $response['task_id']);
        $this->assertSame(1, Task::query()->count());

        $task->refresh();

        $this->assertSame(6, (int) $task->status_id);
        $this->assertSame($project->id, (int) $task->project_id);
        $this->assertEqualsWithDelta(round(600 / 3600, 2), (float) $task->points, 0.01);
        $this->assertTrue((bool) $task->value_generated);

        $this->getJson(route('timer.status'))
            ->assertOk()
            ->assertJson([
                'running' => false,
                'elapsed' => 0,
            ]);
    }
}
