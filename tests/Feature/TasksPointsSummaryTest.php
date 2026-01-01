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
            $table->text('caption')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->decimal('points', 8, 2)->nullable();
            $table->timestamp('due_date')->nullable();
            $table->boolean('value_generated')->default(false);
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
}
