<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BillingAccountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('tasks');
        Schema::dropIfExists('projects');

        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->decimal('points', 8, 2)->nullable();
            $table->timestamp('due_date')->nullable();
            $table->boolean('not_billing')->default(false);
            $table->timestamps();
        });
    }

    public function test_it_calculates_monthly_account_total_from_points_and_hourly_rate(): void
    {
        $viewer = User::factory()->create([
            'hourly_rate' => 80,
        ]);

        $otherUser = User::factory()->create();

        $projectId = DB::table('projects')->insertGetId([
            'name' => 'Proyecto Demo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $month = Carbon::create(2025, 1, 1);

        Task::create([
            'name' => 'Tarea facturable A',
            'project_id' => $projectId,
            'user_id' => $viewer->id,
            'status_id' => 6,
            'points' => 10,
            'due_date' => $month->copy()->day(5),
        ]);

        Task::create([
            'name' => 'Tarea facturable B',
            'project_id' => $projectId,
            'user_id' => $viewer->id,
            'status_id' => 56,
            'points' => 5,
            'due_date' => $month->copy()->day(15),
        ]);

        Task::create([
            'name' => 'No facturar',
            'project_id' => $projectId,
            'user_id' => $viewer->id,
            'status_id' => 6,
            'points' => 3,
            'due_date' => $month->copy()->day(20),
            'not_billing' => true,
        ]);

        Task::create([
            'name' => 'Otra fecha',
            'project_id' => $projectId,
            'user_id' => $viewer->id,
            'status_id' => 6,
            'points' => 8,
            'due_date' => $month->copy()->addMonth()->day(2),
        ]);

        Task::create([
            'name' => 'De otro usuario',
            'project_id' => $projectId,
            'user_id' => $otherUser->id,
            'status_id' => 6,
            'points' => 7,
            'due_date' => $month->copy()->day(10),
        ]);

        $this->actingAs($viewer);

        $response = $this->get(route('billing.index', [
            'month' => '2025-01',
            'user_id' => $viewer->id,
        ]));

        $response->assertOk();
        $response->assertSee('15.00');
        $response->assertSee('$1,200.00');
        $response->assertSee('Tarea facturable A');
        $response->assertSee('Tarea facturable B');
        $response->assertDontSee('No facturar');
        $response->assertDontSee('Otra fecha');
        $response->assertDontSee('De otro usuario');
    }
}
