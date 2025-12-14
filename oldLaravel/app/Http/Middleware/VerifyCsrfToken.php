<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
    	"/api/tasks",
    	"/api/tasks/update",
    	"api/tasks/next_status",
    	"/api/tasks/nextDay",
        "/timer",
        "/timer/stop",
        "/planner",
        "/laravel/app/Http/Middleware"

    	
        //
    ];
}
