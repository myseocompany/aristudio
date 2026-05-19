<?php

use App\Mcp\Servers\AriStudioServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('aristudio', AriStudioServer::class);

Mcp::oauthRoutes();

Mcp::web('/mcp/aristudio', AriStudioServer::class)
    ->middleware('auth:api');
