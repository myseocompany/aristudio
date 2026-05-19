<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensCan([
            'mcp:use' => 'Use MCP server',
        ]);

        Passport::authorizationView(function (array $parameters): View {
            return view('mcp.authorize', $parameters);
        });
    }
}
