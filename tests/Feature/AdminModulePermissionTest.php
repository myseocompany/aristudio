<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModulePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_role_bypasses_module_permissions(): void
    {
        $role = Role::query()->create(['name' => 'Admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        Module::query()->create([
            'name' => 'Users',
            'slug' => '/users',
        ]);

        $this->assertTrue($user->hasModulePermission('/users', 'list'));
    }

    public function test_non_admin_role_still_requires_permissions(): void
    {
        $role = Role::query()->create(['name' => 'Member']);
        $user = User::factory()->create(['role_id' => $role->id]);

        Module::query()->create([
            'name' => 'Users',
            'slug' => '/users',
        ]);

        $this->assertFalse($user->hasModulePermission('/users', 'list'));
    }
}
