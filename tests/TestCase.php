<?php

namespace Tests;

use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param  array<int, string>  $abilities
     */
    protected function grantModulePermissions(User $user, string $slug, array $abilities): void
    {
        $roleId = $user->role_id;
        if (! $roleId) {
            $roleId = Role::query()->create(['name' => 'Test Role'])->id;
            $user->forceFill(['role_id' => $roleId])->save();
        }

        $normalizedSlug = '/'.ltrim($slug, '/');
        $module = Module::query()->firstOrCreate(
            ['slug' => $normalizedSlug],
            ['name' => trim($normalizedSlug, '/'), 'weight' => 0]
        );

        $abilities = array_unique($abilities);
        $payload = [
            'created' => in_array('create', $abilities, true) ? 1 : null,
            'readed' => in_array('read', $abilities, true) ? 1 : null,
            'updated' => in_array('update', $abilities, true) ? 1 : null,
            'deleted' => in_array('delete', $abilities, true) ? 1 : null,
            'list' => in_array('list', $abilities, true) ? 1 : null,
            'view_scope' => in_array('read', $abilities, true) || in_array('list', $abilities, true) ? 1 : 0,
            'updated_at' => now(),
        ];

        DB::table('role_modules')->updateOrInsert(
            ['role_id' => $roleId, 'module_id' => $module->id],
            $payload + ['created_at' => now()]
        );
    }
}
