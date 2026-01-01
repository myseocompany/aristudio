<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param  array<string, string>  $abilitiesByAction
     */
    protected function authorizeModule(Request $request, string $slug, array $abilitiesByAction): void
    {
        $action = $request->route()?->getActionMethod();
        if (! $action) {
            return;
        }

        $ability = $abilitiesByAction[$action] ?? null;
        if (! $ability) {
            return;
        }

        $user = $request->user();
        abort_unless($user && $user->hasModulePermission($slug, $ability), 403, 'No autorizado.');
    }
}
