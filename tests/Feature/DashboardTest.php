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

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('project_users');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_statuses');
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('project_users', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
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
        Schema::create('task_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('pending')->default(true);
            $table->string('color')->nullable();
            $table->string('background_color')->nullable();
        });

        DB::table('task_statuses')->insert([
            ['id' => 1, 'name' => 'REQ', 'pending' => 1, 'color' => '#1d4ed8', 'background_color' => '#dbeafe'],
            ['id' => 6, 'name' => 'Ver', 'pending' => 0, 'color' => '#047857', 'background_color' => '#d1fae5'],
            ['id' => 56, 'name' => 'Billing', 'pending' => 0, 'color' => '#0f172a', 'background_color' => '#e2e8f0'],
        ]);
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
                'created_at' => $now->copy(),
                'updated_at' => $now->copy(),
                'due_date' => $now->copy(),
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
                'due_date' => $now->copy()->subDays(10),
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
                'due_date' => $now->copy()->subDays(15),
            ],
            [
                'name' => 'Overdue REQ',
                'user_id' => $user->id,
                'status_id' => 1,
                'creator_user_id' => $user->id,
                'updator_user_id' => $user->id,
                'points' => 1,
                'value_generated' => true,
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(4),
                'due_date' => $now->copy()->subDays(4),
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
                'due_date' => $now->copy()->subDays(45),
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
                'due_date' => $now->copy()->subDays(7),
            ],
        ]);

        $rangeParam = $now->copy()->subDays(20)->format('Y-m-d').'|'.$now->format('Y-m-d');

        $response = $this->get('/dashboard?range='.$rangeParam);
        $response->assertOk();
        $response->assertSee('Resumen mensual');

        $summary = $response->viewData('taskSummary');
        $chart = $response->viewData('chartData');
        $todayList = $response->viewData('todayTasks');
        $overdueList = $response->viewData('overdueTasks');

        $this->assertSame(2, $summary['req']);
        $this->assertSame(2, $summary['billing']);
        $this->assertEqualsWithDelta(5.0, $summary['points'], 0.01);
        $this->assertEqualsWithDelta(125.0, $summary['amount'], 0.01);
        $this->assertEqualsWithDelta(25.0, $summary['hourly_rate'], 0.01);

        $this->assertGreaterThan(0, count($chart['labels']));
        $this->assertSame($summary['req'], array_sum($chart['req']));
        $this->assertSame($summary['billing'], array_sum($chart['billing']));
        $this->assertSame($rangeParam, $summary['range_value']);
        $this->assertCount(1, $todayList);
        $this->assertSame('REQ task', $todayList->first()->name);
        $this->assertGreaterThan(0, $overdueList->count());
        $this->assertSame('Overdue REQ', $overdueList->first()->name);
    }

    public function test_client_dashboard_shows_project_activities_not_only_own_tasks(): void
    {
        $client = User::factory()->create([
            'role_id' => 4,
            'hourly_rate' => 50,
        ]);
        $teamMember = User::factory()->create();

        $this->actingAs($client);

        $now = Carbon::now();
        $visibleProjectId = 5001;
        $hiddenProjectId = 5002;

        DB::table('projects')->insert([
            [
                'id' => $visibleProjectId,
                'name' => 'Proyecto cliente visible',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $hiddenProjectId,
                'name' => 'Proyecto no vinculado',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('project_users')->insert([
            'project_id' => $visibleProjectId,
            'user_id' => $client->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Task::query()->insert([
            [
                'name' => 'Visible Today REQ',
                'project_id' => $visibleProjectId,
                'user_id' => $teamMember->id,
                'status_id' => 1,
                'creator_user_id' => $teamMember->id,
                'updator_user_id' => $teamMember->id,
                'points' => 1,
                'value_generated' => true,
                'created_at' => $now,
                'updated_at' => $now,
                'due_date' => $now->copy(),
            ],
            [
                'name' => 'Visible Billing',
                'project_id' => $visibleProjectId,
                'user_id' => $teamMember->id,
                'status_id' => 6,
                'creator_user_id' => $teamMember->id,
                'updator_user_id' => $teamMember->id,
                'points' => 2,
                'value_generated' => true,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
                'due_date' => $now->copy()->subDays(2),
            ],
            [
                'name' => 'Visible Overdue REQ',
                'project_id' => $visibleProjectId,
                'user_id' => $teamMember->id,
                'status_id' => 1,
                'creator_user_id' => $teamMember->id,
                'updator_user_id' => $teamMember->id,
                'points' => 1,
                'value_generated' => true,
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
                'due_date' => $now->copy()->subDays(3),
            ],
            [
                'name' => 'Hidden Project REQ',
                'project_id' => $hiddenProjectId,
                'user_id' => $teamMember->id,
                'status_id' => 1,
                'creator_user_id' => $teamMember->id,
                'updator_user_id' => $teamMember->id,
                'points' => 10,
                'value_generated' => true,
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
                'due_date' => $now->copy()->subDays(1),
            ],
        ]);

        $rangeParam = $now->copy()->subDays(10)->format('Y-m-d').'|'.$now->format('Y-m-d');

        $response = $this->get('/dashboard?range='.$rangeParam);
        $response->assertOk();

        $summary = $response->viewData('taskSummary');
        $todayList = $response->viewData('todayTasks');
        $overdueList = $response->viewData('overdueTasks');

        $this->assertSame(2, $summary['req']);
        $this->assertSame(1, $summary['billing']);
        $this->assertEqualsWithDelta(4.0, $summary['points'], 0.01);
        $this->assertCount(1, $todayList);
        $this->assertSame('Visible Today REQ', $todayList->first()->name);
        $this->assertGreaterThan(0, $overdueList->count());
        $this->assertSame('Visible Overdue REQ', $overdueList->first()->name);
    }
}
