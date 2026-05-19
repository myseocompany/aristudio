<?php

use App\Mcp\Middleware\AuthenticateMcpRequest;
use App\Mcp\Servers\AriStudioServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('aristudio', AriStudioServer::class);

Mcp::web('/mcp/aristudio', AriStudioServer::class)
    ->middleware(AuthenticateMcpRequest::class);
