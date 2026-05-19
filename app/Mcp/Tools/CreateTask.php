<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\SerializesTasks;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsDestructive(false)]
#[IsIdempotent(false)]
class CreateTask extends Tool
{
    use SerializesTasks;

    protected string $description = 'Create a task in Ari Studio. Requires an authenticated user with create permission for the tasks module.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->required()
                ->description('Task name.'),
            'status_id' => $schema->integer()
                ->required()
                ->min(1)
                ->description('Task status ID.'),
            'description' => $schema->string()
                ->description('Task description.'),
            'copy' => $schema->string()
                ->description('Task copy.'),
            'caption' => $schema->string()
                ->description('Task caption.'),
            'project_id' => $schema->integer()
                ->min(1)
                ->description('Project ID.'),
            'user_id' => $schema->integer()
                ->min(1)
                ->description('Assigned user ID. Defaults to the authenticated user.'),
            'parent_id' => $schema->integer()
                ->min(1)
                ->description('Parent task ID.'),
            'type_id' => $schema->integer()
                ->min(1)
                ->description('Task type ID.'),
            'sub_type_id' => $schema->integer()
                ->min(1)
                ->description('Task subtype ID.'),
            'due_date' => $schema->string()
                ->description('Due date/time parseable by Laravel, for example 2026-06-01 10:30:00. Defaults to today at 00:00.'),
            'delivery_date' => $schema->string()
                ->description('Delivery date/time parseable by Laravel.'),
            'priority' => $schema->integer()
                ->description('Task priority. Defaults to 1.'),
            'points' => $schema->number()
                ->description('Task points.'),
            'estimated_points' => $schema->number()
                ->description('Estimated task points.'),
            'value_generated' => $schema->boolean()
                ->description('Whether the task generates value. Defaults to true.'),
            'not_billing' => $schema->boolean()
                ->description('Whether the task should be excluded from billing. Defaults to false.'),
            'url_finished' => $schema->string()
                ->description('URL for completed work.'),
            'referrer' => $schema->string()
                ->description('Task referrer.'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'message' => $schema->string()->required(),
            'task' => $schema->object([
                'id' => $schema->integer()->required(),
                'name' => $schema->string()->required(),
                'status_id' => $schema->integer()->nullable(),
                'project_id' => $schema->integer()->nullable(),
                'user_id' => $schema->integer()->nullable(),
                'creator_user_id' => $schema->integer()->nullable(),
                'updator_user_id' => $schema->integer()->nullable(),
                'due_date' => $schema->string()->nullable(),
                'delivery_date' => $schema->string()->nullable(),
                'priority' => $schema->integer()->nullable(),
                'points' => $schema->string()->nullable(),
                'value_generated' => $schema->boolean()->nullable(),
                'not_billing' => $schema->boolean()->nullable(),
                'created_at' => $schema->string()->nullable(),
            ])->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->hasModulePermission('/tasks', 'create')) {
            return Response::error('No autorizado.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:240'],
            'description' => ['nullable', 'string'],
            'copy' => ['nullable', 'string'],
            'caption' => ['nullable', 'string'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'status_id' => ['required', 'exists:task_statuses,id'],
            'parent_id' => ['nullable', 'exists:tasks,id'],
            'delivery_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['nullable', 'integer'],
            'points' => ['nullable', 'numeric'],
            'estimated_points' => ['nullable', 'numeric'],
            'type_id' => ['nullable', 'exists:task_types,id'],
            'sub_type_id' => ['nullable', 'exists:task_types,id'],
            'value_generated' => ['nullable', 'boolean'],
            'not_billing' => ['nullable', 'boolean'],
            'url_finished' => ['nullable', 'string', 'max:300'],
            'referrer' => ['nullable', 'string', 'max:300'],
        ]);

        $validated['priority'] ??= 1;
        $validated['user_id'] ??= $user->id;
        $validated['creator_user_id'] = $user->id;
        $validated['updator_user_id'] = $user->id;
        $validated['value_generated'] = $request->boolean('value_generated', true);
        $validated['not_billing'] = $request->boolean('not_billing');
        $validated['due_date'] = $this->parseDateTime($validated['due_date'] ?? null) ?? now()->startOfDay();
        $validated['delivery_date'] = $this->parseDateTime($validated['delivery_date'] ?? null);

        $task = Task::query()->create($validated);

        return Response::structured([
            'message' => 'Tarea creada.',
            'task' => $this->taskPayload($task),
        ]);
    }

    private function parseDateTime(?string $value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }
}
