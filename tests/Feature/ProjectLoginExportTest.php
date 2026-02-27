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

class ProjectLoginExportTest extends TestCase
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

    public function test_user_id_1_with_role_id_1_can_export_project_logins(): void
    {
        DB::table('roles')->insert([
            'id' => 1,
            'name' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::factory()->create([
            'id' => 1,
            'role_id' => 1,
        ]);

        $project = Project::create(['name' => 'Alpha', 'color' => '#10b981', 'status_id' => 3]);

        ProjectLogin::create([
            'project_id' => $project->id,
            'name' => 'Login Alpha',
            'user' => 'alpha_user',
            'password' => 'alpha_pass',
            'url' => 'https://alpha.test',
        ]);

        $response = $this->actingAs($user)->get(route('projects.logins.export', $project));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment; filename=', (string) $response->headers->get('content-disposition'));

        $csvContent = $response->streamedContent();
        $this->assertStringContainsString('Proyecto,Login,Usuario,Contrasena,URL', $csvContent);
        $this->assertStringContainsString('Login Alpha', $csvContent);
    }

    public function test_user_with_role_id_1_but_different_id_cannot_export_project_logins(): void
    {
        DB::table('roles')->insert([
            'id' => 1,
            'name' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::factory()->create([
            'id' => 2,
            'role_id' => 1,
        ]);

        $project = Project::create(['name' => 'Alpha', 'color' => '#10b981', 'status_id' => 3]);
        ProjectLogin::create([
            'project_id' => $project->id,
            'name' => 'Login Alpha',
            'user' => 'alpha_user',
            'password' => 'alpha_pass',
            'url' => 'https://alpha.test',
        ]);

        $response = $this->actingAs($user)->get(route('projects.logins.export', $project));
        $response->assertForbidden();
    }

    public function test_user_with_id_1_but_different_role_cannot_export_project_logins(): void
    {
        $moduleId = DB::table('modules')->insertGetId([
            'name' => 'Logins',
            'slug' => 'logins',
            'weight' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('roles')->insert([
            'id' => 2,
            'name' => 'Equipo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::factory()->create([
            'id' => 1,
            'role_id' => 2,
        ]);

        DB::table('role_modules')->insert([
            'role_id' => 2,
            'module_id' => $moduleId,
            'list' => true,
            'view_scope' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $project = Project::create(['name' => 'Alpha', 'color' => '#10b981', 'status_id' => 3]);
        ProjectLogin::create([
            'project_id' => $project->id,
            'name' => 'Login Alpha',
            'user' => 'alpha_user',
            'password' => 'alpha_pass',
            'url' => 'https://alpha.test',
        ]);

        $response = $this->actingAs($user)->get(route('projects.logins.export', $project));
        $response->assertForbidden();
    }
}
