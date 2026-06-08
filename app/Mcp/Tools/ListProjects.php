<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Project;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class ListProjects extends Tool
{
    private const DEFAULT_ACTIVE_STATUS_ID = 3;

    protected string $description = 'List active Ari Studio projects by default, with optional filters. Returns id, name, description, status, type, dates, budget fields and assigned user count.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'status_id' => $schema->integer()
                ->min(1)
                ->description('Filter by project status ID. Defaults to 3 for active/running projects.'),
            'type_id' => $schema->integer()
                ->min(1)
                ->description('Filter by project type ID.'),
            'q' => $schema->string()
                ->description('Search by project name or description.'),
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
                ->description('Number of projects returned.'),
            'projects' => $schema->array()
                ->items($schema->object([
                    'id' => $schema->integer()->required(),
                    'name' => $schema->string()->required(),
                    'description' => $schema->string()->nullable(),
                    'type' => $schema->string()->nullable(),
                    'type_id' => $schema->integer()->nullable(),
                    'status' => $schema->string()->nullable(),
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
                    'users_count' => $schema->integer()->nullable(),
                    'created_at' => $schema->string()->nullable(),
                    'updated_at' => $schema->string()->nullable(),
                ]))
                ->description('Projects matching the requested filters.'),
        ];
    }

    public function handle(Request $request): ResponseFactory
    {
        $validated = $request->validate([
            'status_id' => ['nullable', 'integer', 'min:1'],
            'type_id' => ['nullable', 'integer', 'min:1'],
            'q' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Project::query()
            ->leftJoin('project_statuses', 'project_statuses.id', '=', 'projects.status_id')
            ->leftJoin('project_types', 'project_types.id', '=', 'projects.type_id')
            ->select([
                'projects.id',
                'projects.name',
                'projects.description',
                'projects.type_id',
                'projects.status_id',
                'projects.color',
                'projects.weight',
                'projects.budget',
                'projects.start_date',
                'projects.finish_date',
                'projects.weekly_pieces',
                'projects.ads_budget',
                'projects.lead_target',
                'projects.monthly_points_goal',
                'projects.sales',
                'projects.created_at',
                'projects.updated_at',
                'project_statuses.name as status_name',
                'project_types.name as type_name',
            ])
            ->withCount('users');

        $query->where('projects.status_id', $validated['status_id'] ?? self::DEFAULT_ACTIVE_STATUS_ID);

        if ($typeId = $validated['type_id'] ?? null) {
            $query->where('projects.type_id', $typeId);
        }

        if ($search = $validated['q'] ?? null) {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('projects.name', 'like', "%{$search}%")
                    ->orWhere('projects.description', 'like', "%{$search}%");
            });
        }

        $limit = $validated['limit'] ?? 30;

        $projects = $query
            ->orderBy('projects.weight')
            ->orderBy('projects.name')
            ->limit($limit)
            ->get();

        $payload = [
            'count' => $projects->count(),
            'projects' => $projects->map(fn (Project $project): array => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'type' => $project->type_name,
                'type_id' => $project->type_id,
                'status' => $project->status_name,
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
                'users_count' => $project->users_count,
                'created_at' => $project->created_at?->toDateTimeString(),
                'updated_at' => $project->updated_at?->toDateTimeString(),
            ])->values()->all(),
        ];

        return Response::structured($payload);
    }
}
