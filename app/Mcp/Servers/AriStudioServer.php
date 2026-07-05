<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CreateTask;
use App\Mcp\Tools\ListProjects;
use App\Mcp\Tools\ListTasks;
use App\Mcp\Tools\UpdateProject;
use App\Mcp\Tools\UpdateTask;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Contracts\Transport;

class AriStudioServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Ari Studio Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '0.0.1';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        Use this server to read, create, and update operational Ari Studio tasks. Prefer narrow filters and small limits before requesting larger result sets.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        CreateTask::class,
        ListProjects::class,
        ListTasks::class,
        UpdateProject::class,
        UpdateTask::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);

        $version = trim((string) file_get_contents(base_path('VERSION')));

        if ($version !== '') {
            $this->version = $version;
        }
    }
}
