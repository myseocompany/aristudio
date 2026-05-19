<?php

use App\Mcp\Servers\AriStudioServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('aristudio', AriStudioServer::class);
