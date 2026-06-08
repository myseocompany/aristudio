<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsDestructive(false)]
#[IsIdempotent(false)]
class UpdateProject extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Update an existing Ari Studio project. Requires an authenticated user with update permission for the projects module.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->required()
                ->min(1)
                ->description('Project ID to update.'),
            'description' => $schema->string()
                ->nullable()
                ->description('Project description. Send null to clear it.'),
            'name' => $schema->string()
                ->nullable()
                ->description('Project name. Send null to keep the current name.'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'message' => $schema->string()->required(),
            'project' => $schema->object([
                'id' => $schema->integer()->required(),
                'name' => $schema->string()->required(),
                'description' => $schema->string()->nullable(),
                'type_id' => $schema->integer()->nullable(),
                'status_id' => $schema->integer()->nullable(),
                'color' => $schema->string()->nullable(),
                'weight' => $schema->string()->nullable(),
                'budget' => $schema->string()->nullable(),
                'start_date' => $schema->string()->nullable(),
                'finish_date' => $schema->string()->nullable(),
                'weekly_pieces' => $schema->integer()->nullable(),
                'ads_budget' => $schema->string()->nullable(),
                'lead_target' => $schema->integer()->nullable(),
                'monthly_points_goal' => $schema->integer()->nullable(),
                'sales' => $schema->string()->nullable(),
                'created_at' => $schema->string()->nullable(),
                'updated_at' => $schema->string()->nullable(),
            ])->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->hasModulePermission('/projects', 'update')) {
            return Response::error('No autorizado.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:projects,id'],
            'description' => ['sometimes', 'nullable', 'string'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $project = Project::query()->findOrFail($validated['id']);
        unset($validated['id']);

        if (array_key_exists('name', $validated) && $validated['name'] === null) {
            unset($validated['name']);
        }

        $project->update($validated);

        return Response::structured([
            'message' => 'Proyecto actualizado.',
            'project' => $this->projectPayload($project->refresh()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function projectPayload(Project $project): array
    {
        return [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'type_id' => $project->type_id,
            'status_id' => $project->status_id,
            'color' => $project->color,
            'weight' => $project->weight,
            'budget' => $project->budget,
            'start_date' => $project->start_date,
            'finish_date' => $project->finish_date,
            'weekly_pieces' => $project->weekly_pieces,
            'ads_budget' => $project->ads_budget,
            'lead_target' => $project->lead_target,
            'monthly_points_goal' => $project->monthly_points_goal,
            'sales' => $project->sales,
            'created_at' => $project->created_at?->toDateTimeString(),
            'updated_at' => $project->updated_at?->toDateTimeString(),
        ];
    }
}
