<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectLogin;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProjectLoginIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('project_users');
        Schema::dropIfExists('project_logins');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('role_modules');

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('modules', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->string('slug', 100)->nullable();
            $table->integer('weight')->default(0);
            $table->string('url', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('role_modules', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('module_id');
            $table->boolean('created')->default(false);
            $table->boolean('readed')->default(false);
            $table->boolean('updated')->default(false);
            $table->boolean('deleted')->default(false);
            $table->boolean('list')->default(false);
            $table->unsignedTinyInteger('view_scope')->default(0);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('password');
            }
        });

        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->unsignedBigInteger('status_id')->default(3);
            $table->timestamps();
        });

        Schema::create('project_users', function (Blueprint $table): void {
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('project_logins', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('name');
            $table->string('user');
            $table->string('password');
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }

    public function test_user_sees_only_assigned_project_logins_when_role_is_not_all(): void
    {
        $moduleId = DB::table('modules')->insertGetId([
            'name' => 'Logins',
            'slug' => 'logins',
            'weight' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roleTeamId = DB::table('roles')->insertGetId(['name' => 'Equipo', 'created_at' => now(), 'updated_at' => now()]);
        $user = User::factory()->create(['role_id' => $roleTeamId]);

        DB::table('role_modules')->insert([
            'role_id' => $roleTeamId,
            'module_id' => $moduleId,
            'list' => true,
            'created_at' => now(),
            'updated_at' => now(),
            'view_scope' => 0,
        ]);

        $projectA = Project::create(['name' => 'Alpha', 'color' => '#10b981', 'status_id' => 3]);
        $projectB = Project::create(['name' => 'Beta', 'color' => '#6366f1', 'status_id' => 1]);

        DB::table('project_users')->insert([
            'project_id' => $projectA->id,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ProjectLogin::create([
            'project_id' => $projectA->id,
            'name' => 'Login Alpha',
            'user' => 'alpha_user',
            'password' => 'alpha_pass',
            'url' => 'https://alpha.test',
        ]);

        ProjectLogin::create([
            'project_id' => $projectB->id,
            'name' => 'Login Beta',
            'user' => 'beta_user',
            'password' => 'beta_pass',
            'url' => 'https://beta.test',
        ]);

        $response = $this->actingAs($user)->get(route('logins.index'));

        $response->assertOk();
        $response->assertSee('Login Alpha');
        $response->assertDontSee('Login Beta');
    }

    public function test_role_todos_can_see_all_project_logins_and_filter(): void
    {
        $moduleId = DB::table('modules')->insertGetId([
            'name' => 'Logins',
            'slug' => 'logins',
            'weight' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roleAllId = DB::table('roles')->insertGetId(['name' => 'Todos', 'created_at' => now(), 'updated_at' => now()]);
        $user = User::factory()->create(['role_id' => $roleAllId]);

        DB::table('role_modules')->insert([
            'role_id' => $roleAllId,
            'module_id' => $moduleId,
            'list' => true,
            'created_at' => now(),
            'updated_at' => now(),
            'view_scope' => 1,
        ]);

        $projectA = Project::create(['name' => 'Gamma', 'color' => '#0ea5e9', 'status_id' => 3]);
        $projectB = Project::create(['name' => 'Delta', 'color' => '#f59e0b', 'status_id' => 1]);

        $loginGamma = ProjectLogin::create([
            'project_id' => $projectA->id,
            'name' => 'Gamma Login',
            'user' => 'gamma_user',
            'password' => 'gamma_pass',
            'url' => null,
        ]);

        $loginDelta = ProjectLogin::create([
            'project_id' => $projectB->id,
            'name' => 'Delta Login',
            'user' => 'delta_user',
            'password' => 'delta_pass',
            'url' => null,
        ]);

        $response = $this->actingAs($user)->get(route('logins.index'));
        $response->assertOk();
        $response->assertSee($loginGamma->name);
        $response->assertSee($loginDelta->name);

        $filtered = $this->actingAs($user)->get(route('logins.index', [
            'project_id' => $projectA->id,
            'q' => 'Gamma',
        ]));

        $filtered->assertOk();
        $filtered->assertSee($loginGamma->name);
        $filtered->assertDontSee($loginDelta->name);
    }
}
