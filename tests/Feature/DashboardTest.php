<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardTest extends TestCase
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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('status_id')->default(1);
            $table->decimal('points', 8, 2)->nullable();
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->unsignedBigInteger('updator_user_id')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->boolean('value_generated')->default(false);
            $table->timestamps();
        });
    }

    public function test_dashboard_displays_monthly_task_stats(): void
    {
        $user = User::factory()->create([
            'hourly_rate' => 25,
        ]);
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $now = Carbon::now();

        Task::query()->insert([
            [
                'name' => 'REQ task',
                'user_id' => $user->id,
                'status_id' => 1,
                'creator_user_id' => $user->id,
                'updator_user_id' => $user->id,
                'points' => 1,
                'value_generated' => true,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'name' => 'Billing task',
                'user_id' => $user->id,
                'status_id' => 6,
                'creator_user_id' => $user->id,
                'updator_user_id' => $user->id,
                'points' => 2,
                'value_generated' => true,
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(10),
            ],
            [
                'name' => 'Billing task 56',
                'user_id' => $user->id,
                'status_id' => 56,
                'creator_user_id' => $user->id,
                'updator_user_id' => $user->id,
                'points' => 1,
                'value_generated' => true,
                'created_at' => $now->copy()->subDays(15),
                'updated_at' => $now->copy()->subDays(15),
            ],
            [
                'name' => 'Outside range',
                'user_id' => $user->id,
                'status_id' => 1,
                'creator_user_id' => $user->id,
                'updator_user_id' => $user->id,
                'points' => 3,
                'value_generated' => true,
                'created_at' => $now->copy()->subDays(45),
                'updated_at' => $now->copy()->subDays(45),
            ],
            [
                'name' => 'Created by user task',
                'user_id' => $otherUser->id,
                'status_id' => 6,
                'creator_user_id' => $user->id,
                'updator_user_id' => $user->id,
                'points' => 3,
                'value_generated' => true,
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(7),
            ],
        ]);

        $rangeParam = $now->copy()->subDays(20)->format('Y-m-d').'|'.$now->format('Y-m-d');

        $response = $this->get('/dashboard?range='.$rangeParam);
        $response->assertOk();
        $response->assertSee('Resumen mensual');

        $summary = $response->viewData('taskSummary');
        $chart = $response->viewData('chartData');

        $this->assertSame(1, $summary['req']);
        $this->assertSame(2, $summary['billing']);
        $this->assertEqualsWithDelta(4.0, $summary['points'], 0.01);
        $this->assertEqualsWithDelta(100.0, $summary['amount'], 0.01);
        $this->assertEqualsWithDelta(25.0, $summary['hourly_rate'], 0.01);

        $this->assertGreaterThan(0, count($chart['labels']));
        $this->assertSame($summary['req'], array_sum($chart['req']));
        $this->assertSame($summary['billing'], array_sum($chart['billing']));
        $this->assertSame($rangeParam, $summary['range_value']);
    }
}
