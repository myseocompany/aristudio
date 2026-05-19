<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\AriStudioServer;
use App\Mcp\Tools\ListTasks;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            $table->integer('priority')->nullable();
            $table->decimal('points', 8, 2)->nullable();
            $table->timestamp('due_date')->nullable();
            $table->boolean('value_generated')->default(false);
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
}
