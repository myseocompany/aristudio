<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReportsUsersByMonthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('tasks');

        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->decimal('points', 8, 2)->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function test_report_displays_points_and_amount_by_month(): void
    {
        $viewer = User::factory()->create();
        $member = User::factory()->create([
            'name' => 'Ana Lopez',
            'hourly_rate' => 50,
        ]);
        $this->grantModulePermissions($viewer, '/reports', ['read', 'list']);

        $year = now()->year;

        Task::create([
            'name' => 'Tarea enero',
            'user_id' => $member->id,
            'status_id' => 6,
            'points' => 10,
            'due_date' => Carbon::create($year, 1, 15),
        ]);

        Task::create([
            'name' => 'Otra tarea enero',
            'user_id' => $member->id,
            'status_id' => 56,
            'points' => 5,
            'due_date' => Carbon::create($year, 1, 20),
        ]);

        // Task with different status should not count.
        Task::create([
            'name' => 'Revisión',
            'user_id' => $member->id,
            'status_id' => 1,
            'points' => 8,
            'due_date' => Carbon::create($year, 1, 10),
        ]);

        $this->actingAs($viewer->refresh());

        $response = $this->get(route('reports.users_by_month', ['year' => $year]));

        $response->assertOk();
        $response->assertSee('15.00 pts');
        $response->assertSee('$750.00');
    }
}
