<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class ListTasks extends Tool
{
    protected string $description = 'List tasks with optional filters. Returns id, name, status, project, assigned user, due date and priority.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'status_id' => $schema->integer()
                ->min(1)
                ->description('Filter by status ID.'),
            'project_id' => $schema->integer()
                ->min(1)
                ->description('Filter by project ID.'),
            'user_id' => $schema->integer()
                ->min(1)
                ->description('Filter by assigned user ID.'),
            'q' => $schema->string()
                ->description('Search by name, description or caption.'),
            'limit' => $schema->integer()
                ->min(1)
                ->max(100)
                ->default(30)
                ->description('Max results to return (default 30, max 100).'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'count' => $schema->integer()
                ->description('Number of tasks returned.'),
            'tasks' => $schema->array()
                ->items($schema->object([
                    'id' => $schema->integer()->required(),
                    'name' => $schema->string()->required(),
                    'status' => $schema->string()->nullable(),
                    'status_id' => $schema->integer()->nullable(),
                    'project' => $schema->string()->nullable(),
                    'project_id' => $schema->integer()->nullable(),
                    'user' => $schema->string()->nullable(),
                    'user_id' => $schema->integer()->nullable(),
                    'due_date' => $schema->string()->nullable(),
                    'priority' => $schema->integer()->nullable(),
                    'points' => $schema->string()->nullable(),
                    'value_generated' => $schema->boolean()->nullable(),
                    'created_at' => $schema->string()->nullable(),
                ]))
                ->description('Tasks matching the requested filters.'),
        ];
    }

    public function handle(Request $request): ResponseFactory
    {
        $validated = $request->validate([
            'status_id' => ['nullable', 'integer', 'min:1'],
            'project_id' => ['nullable', 'integer', 'min:1'],
            'user_id' => ['nullable', 'integer', 'min:1'],
            'q' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Task::with(['status:id,name,alias', 'project:id,name', 'user:id,name'])
            ->select('id', 'name', 'status_id', 'project_id', 'user_id', 'due_date', 'priority', 'points', 'value_generated', 'created_at');

        if ($statusId = $validated['status_id'] ?? null) {
            $query->where('status_id', $statusId);
        }

        if ($projectId = $validated['project_id'] ?? null) {
            $query->where('project_id', $projectId);
        }

        if ($userId = $validated['user_id'] ?? null) {
            $query->where('user_id', $userId);
        }

        if ($search = $validated['q'] ?? null) {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        $limit = $validated['limit'] ?? 30;

        $tasks = $query->orderByDesc('created_at')->limit($limit)->get();

        $payload = [
            'count' => $tasks->count(),
            'tasks' => $tasks->map(fn (Task $task): array => [
                'id' => $task->id,
                'name' => $task->name,
                'status' => $task->status?->name,
                'status_id' => $task->status_id,
                'project' => $task->project?->name,
                'project_id' => $task->project_id,
                'user' => $task->user?->name,
                'user_id' => $task->user_id,
                'due_date' => $task->due_date?->toDateTimeString(),
                'priority' => $task->priority,
                'points' => $task->points,
                'value_generated' => $task->value_generated,
                'created_at' => $task->created_at?->toDateTimeString(),
            ])->values()->all(),
        ];

        return Response::structured($payload);
    }
}
