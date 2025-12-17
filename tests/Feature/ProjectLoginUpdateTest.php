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

class ProjectLoginUpdateTest extends TestCase
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

    public function test_user_can_reassign_login_to_accessible_project(): void
    {
        $moduleId = DB::table('modules')->insertGetId([
            'name' => 'Logins',
            'slug' => 'logins',
            'weight' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roleId = DB::table('roles')->insertGetId(['name' => 'Equipo', 'created_at' => now(), 'updated_at' => now()]);
        $user = User::factory()->create(['role_id' => $roleId]);

        DB::table('role_modules')->insert([
            'role_id' => $roleId,
            'module_id' => $moduleId,
            'view_scope' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $projectA = Project::create(['name' => 'Uno', 'color' => '#10b981', 'status_id' => 3]);
        $projectB = Project::create(['name' => 'Dos', 'color' => '#6366f1', 'status_id' => 3]);

        DB::table('project_users')->insert([
            ['project_id' => $projectA->id, 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
            ['project_id' => $projectB->id, 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $login = ProjectLogin::create([
            'project_id' => $projectA->id,
            'name' => 'Panel Hosting',
            'user' => 'user_example',
            'password' => 'secret',
            'url' => 'https://hosting.test',
        ]);

        $response = $this->actingAs($user)->put(route('projects.logins.update', [$projectA, $login]), [
            'project_id' => $projectB->id,
            'name' => 'Panel Hosting',
            'user' => 'user_example',
            'password' => 'nueva',
            'url' => 'https://hosting.test',
        ]);

        $response->assertRedirect(route('projects.show', $projectB));

        $this->assertDatabaseHas('project_logins', [
            'id' => $login->id,
            'project_id' => $projectB->id,
            'password' => 'nueva',
        ]);
    }

    public function test_user_cannot_reassign_login_to_unassigned_project(): void
    {
        $moduleId = DB::table('modules')->insertGetId([
            'name' => 'Logins',
            'slug' => 'logins',
            'weight' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roleId = DB::table('roles')->insertGetId(['name' => 'Equipo', 'created_at' => now(), 'updated_at' => now()]);
        $user = User::factory()->create(['role_id' => $roleId]);

        DB::table('role_modules')->insert([
            'role_id' => $roleId,
            'module_id' => $moduleId,
            'view_scope' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $projectA = Project::create(['name' => 'Tres', 'color' => '#0ea5e9', 'status_id' => 3]);
        $projectB = Project::create(['name' => 'Cuatro', 'color' => '#f59e0b', 'status_id' => 3]);

        DB::table('project_users')->insert([
            'project_id' => $projectA->id,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $login = ProjectLogin::create([
            'project_id' => $projectA->id,
            'name' => 'Panel DNS',
            'user' => 'dns_user',
            'password' => 'dns_pass',
            'url' => null,
        ]);

        $response = $this->actingAs($user)->put(route('projects.logins.update', [$projectA, $login]), [
            'project_id' => $projectB->id,
            'name' => 'Panel DNS',
            'user' => 'dns_user',
            'password' => 'dns_pass',
            'url' => null,
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('project_logins', [
            'id' => $login->id,
            'project_id' => $projectA->id,
        ]);
    }
}
