<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NavigationModuleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_navigation_hides_child_modules(): void
    {
        DB::table('roles')->insert(['id' => 2, 'name' => 'Team']);
        $user = User::factory()->create(['role_id' => 2]);

        DB::table('modules')->insert([
            ['id' => 4, 'name' => 'Usuarios', 'slug' => 'users', 'weight' => 4, 'parent_id' => 10],
            ['id' => 10, 'name' => 'Configuración', 'slug' => '/config', 'weight' => 30, 'parent_id' => null],
        ]);

        DB::table('role_modules')->insert([
            ['role_id' => 2, 'module_id' => 4, 'list' => 1],
            ['role_id' => 2, 'module_id' => 10, 'list' => 1],
        ]);

        $this->actingAs($user);

        $html = view('layouts.navigation')->render();

        $this->assertStringContainsString('Configuración', $html);
        $this->assertStringNotContainsString('Usuarios', $html);
    }
}
