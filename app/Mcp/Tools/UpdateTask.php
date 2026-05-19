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
class UpdateTask extends Tool
{
    use SerializesTasks;

    protected string $description = 'Update an existing Ari Studio task. Requires an authenticated user with update permission for the tasks module.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->required()
                ->min(1)
                ->description('Task ID to update.'),
            'name' => $schema->string()
                ->description('Task name.'),
            'status_id' => $schema->integer()
                ->min(1)
                ->description('Task status ID.'),
            'description' => $schema->string()
                ->nullable()
                ->description('Task description. Send null to clear it.'),
            'copy' => $schema->string()
                ->nullable()
                ->description('Task copy. Send null to clear it.'),
            'caption' => $schema->string()
                ->nullable()
                ->description('Task caption. Send null to clear it.'),
            'project_id' => $schema->integer()
                ->nullable()
                ->min(1)
                ->description('Project ID. Send null to clear assignment.'),
            'user_id' => $schema->integer()
                ->nullable()
                ->min(1)
                ->description('Assigned user ID. Send null to unassign.'),
            'parent_id' => $schema->integer()
                ->nullable()
                ->min(1)
                ->description('Parent task ID. Send null to clear it.'),
            'type_id' => $schema->integer()
                ->nullable()
                ->min(1)
                ->description('Task type ID. Send null to clear it.'),
            'sub_type_id' => $schema->integer()
                ->nullable()
                ->min(1)
                ->description('Task subtype ID. Send null to clear it.'),
            'due_date' => $schema->string()
                ->nullable()
                ->description('Due date/time parseable by Laravel. Send null to clear it.'),
            'delivery_date' => $schema->string()
                ->nullable()
                ->description('Delivery date/time parseable by Laravel. Send null to clear it.'),
            'priority' => $schema->integer()
                ->nullable()
                ->description('Task priority. Send null to clear it.'),
            'points' => $schema->number()
                ->nullable()
                ->description('Task points. Send null to clear it.'),
            'estimated_points' => $schema->number()
                ->nullable()
                ->description('Estimated task points. Send null to clear it.'),
            'value_generated' => $schema->boolean()
                ->description('Whether the task generates value. Omitted values keep the current state.'),
            'not_billing' => $schema->boolean()
                ->description('Whether the task should be excluded from billing. Omitted values keep the current state.'),
            'url_finished' => $schema->string()
                ->nullable()
                ->description('URL for completed work. Send null to clear it.'),
            'referrer' => $schema->string()
                ->nullable()
                ->description('Task referrer. Send null to clear it.'),
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
                'updated_at' => $schema->string()->nullable(),
            ])->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->hasModulePermission('/tasks', 'update')) {
            return Response::error('No autorizado.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:tasks,id'],
            'name' => ['sometimes', 'required', 'string', 'max:240'],
            'description' => ['sometimes', 'nullable', 'string'],
            'copy' => ['sometimes', 'nullable', 'string'],
            'caption' => ['sometimes', 'nullable', 'string'],
            'project_id' => ['sometimes', 'nullable', 'exists:projects,id'],
            'user_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'status_id' => ['sometimes', 'required', 'exists:task_statuses,id'],
            'parent_id' => ['sometimes', 'nullable', 'exists:tasks,id'],
            'delivery_date' => ['sometimes', 'nullable', 'date'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'priority' => ['sometimes', 'nullable', 'integer'],
            'points' => ['sometimes', 'nullable', 'numeric'],
            'estimated_points' => ['sometimes', 'nullable', 'numeric'],
            'type_id' => ['sometimes', 'nullable', 'exists:task_types,id'],
            'sub_type_id' => ['sometimes', 'nullable', 'exists:task_types,id'],
            'value_generated' => ['sometimes', 'nullable', 'boolean'],
            'not_billing' => ['sometimes', 'nullable', 'boolean'],
            'url_finished' => ['sometimes', 'nullable', 'string', 'max:300'],
            'referrer' => ['sometimes', 'nullable', 'string', 'max:300'],
        ]);

        $task = Task::query()->findOrFail($validated['id']);
        unset($validated['id']);

        $validated['updator_user_id'] = $user->id;

        if ($request->has('value_generated')) {
            $validated['value_generated'] = $request->boolean('value_generated');
        }

        if ($request->has('not_billing')) {
            $validated['not_billing'] = $request->boolean('not_billing');
        }

        if ($request->has('due_date')) {
            $validated['due_date'] = $this->parseDateTime($request->get('due_date'));
        }

        if ($request->has('delivery_date')) {
            $validated['delivery_date'] = $this->parseDateTime($request->get('delivery_date'));
        }

        $task->update($validated);

        return Response::structured([
            'message' => 'Tarea actualizada.',
            'task' => $this->taskPayload($task->refresh()),
        ]);
    }

    private function parseDateTime(?string $value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }
}
