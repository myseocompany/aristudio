<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->authorizeModule($request, '/tasks', [
                'index' => 'list',
                'export' => 'list',
                'create' => 'create',
                'store' => 'create',
                'show' => 'read',
                'edit' => 'update',
                'update' => 'update',
                'quickAssign' => 'update',
                'destroy' => 'delete',
            ]);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        [$isClientRole, $clientProjectIds] = $this->taskVisibilityScope();
        $now = Carbon::now();
        $defaultFromDate = $now->copy()->startOfMonth();
        $defaultToDate = $now->copy()->endOfMonth();
        $filters = $this->resolveTaskFilters(
            $request,
            $isClientRole,
            $defaultFromDate,
            $defaultToDate,
        );

        $statuses = TaskStatus::active()->orderBy('weight')->get();
        $projects = Project::where('status_id', 3)
            ->when($isClientRole, fn ($query) => $query->whereIn('id', $clientProjectIds))
            ->orderBy('weight')
            ->orderBy('name')
            ->get(['id', 'name', 'color']);
        $users = User::where('status_id', 1)
            ->when($isClientRole, function ($query) use ($clientProjectIds) {
                $teamUserIds = Task::query()
                    ->whereIn('project_id', $clientProjectIds)
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
                    ->all();

                $query->whereIn('id', $teamUserIds);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'image_url', 'status_id']);
        $types = TaskType::orderBy('name')->get();
        $parentTypes = $types->whereNull('parent_id')->values();
        $subTypes = $types->whereNotNull('parent_id')
            ->map(function ($type) use ($types) {
                $parent = $types->firstWhere('id', $type->parent_id);
                $type->label = $parent ? "{$parent->name} → {$type->name}" : $type->name;

                return $type;
            })
            ->values();
        $defaultStatusId = $statuses->first()?->id;
        $today = $now->copy()->startOfDay();
        $timePresets = [
            'manana' => [
                'label' => 'Mañana',
                'from' => $today->copy()->addDay()->toDateString(),
                'to' => $today->copy()->addDay()->toDateString(),
            ],
            'hoy' => [
                'label' => 'Hoy',
                'from' => $today->toDateString(),
                'to' => $today->toDateString(),
            ],
            'ayer' => [
                'label' => 'Ayer',
                'from' => $today->copy()->subDay()->toDateString(),
                'to' => $today->copy()->subDay()->toDateString(),
            ],
            'esta_semana' => [
                'label' => 'Esta semana',
                'from' => $now->copy()->startOfWeek(Carbon::MONDAY)->toDateString(),
                'to' => $now->copy()->endOfWeek(Carbon::SUNDAY)->toDateString(),
            ],
            'semana_pasada' => [
                'label' => 'Semana pasada',
                'from' => $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY)->toDateString(),
                'to' => $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY)->toDateString(),
            ],
            'este_mes' => [
                'label' => 'Este mes',
                'from' => $now->copy()->startOfMonth()->toDateString(),
                'to' => $now->copy()->endOfMonth()->toDateString(),
            ],
            'mes_pasado' => [
                'label' => 'Mes pasado',
                'from' => $now->copy()->subMonth()->startOfMonth()->toDateString(),
                'to' => $now->copy()->subMonth()->endOfMonth()->toDateString(),
            ],
            'este_anio' => [
                'label' => 'Este año',
                'from' => $now->copy()->startOfYear()->toDateString(),
                'to' => $now->copy()->endOfYear()->toDateString(),
            ],
            'anio_pasado' => [
                'label' => 'Año pasado',
                'from' => $now->copy()->subYear()->startOfYear()->toDateString(),
                'to' => $now->copy()->subYear()->endOfYear()->toDateString(),
            ],
        ];

        $tasksQuery = $this->buildFilteredTasksQuery(
            $filters,
            $isClientRole,
            $clientProjectIds,
        );

        $selectedPointsTotal = (clone $tasksQuery)->sum('tasks.points');

        $tasks = $tasksQuery
            ->orderByDesc('tasks.created_at')
            ->orderByRaw('CASE WHEN tasks.due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('tasks.due_date')
            ->paginate(15)
            ->withQueryString();

        $defaultDueDateTime = now()->format('Y-m-d\TH:i');

        return view('tasks.index', [
            'tasks' => $tasks,
            'statuses' => $statuses,
            'projects' => $projects,
            'users' => $users,
            'parentTypes' => $parentTypes,
            'subTypes' => $subTypes,
            'defaultStatusId' => $defaultStatusId,
            'timePresets' => $timePresets,
            'selectedPointsTotal' => $selectedPointsTotal,
            'defaultFromDate' => $defaultFromDate->toDateString(),
            'defaultToDate' => $defaultToDate->toDateString(),
            'defaultDueDateTime' => $defaultDueDateTime,
            'filters' => [
                'status_id' => $filters['status_id'],
                'project_id' => $filters['project_id'],
                'user_id' => $filters['user_id'],
                'value_generated' => $filters['value_generated'],
                'from_date' => $filters['from_date'],
                'to_date' => $filters['to_date'],
                'q' => $filters['q'],
            ],
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        [$isClientRole, $clientProjectIds] = $this->taskVisibilityScope();
        $now = Carbon::now();
        $defaultFromDate = $now->copy()->startOfMonth();
        $defaultToDate = $now->copy()->endOfMonth();
        $filters = $this->resolveTaskFilters(
            $request,
            $isClientRole,
            $defaultFromDate,
            $defaultToDate,
        );

        $tasksQuery = $this->buildFilteredTasksQuery(
            $filters,
            $isClientRole,
            $clientProjectIds,
            false
        )
            ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->leftJoin('users', 'users.id', '=', 'tasks.user_id')
            ->leftJoin('task_statuses', 'task_statuses.id', '=', 'tasks.status_id')
            ->orderByDesc('tasks.created_at')
            ->orderByRaw('CASE WHEN tasks.due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('tasks.due_date')
            ->select([
                'tasks.id',
                'tasks.name',
                'projects.name as project_name',
                'task_statuses.name as status_name',
                'users.name as user_name',
                'tasks.value_generated',
                'tasks.points',
                'tasks.due_date',
                'tasks.delivery_date',
                'tasks.created_at',
            ]);

        $filename = 'tareas-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($tasksQuery): void {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'ID',
                'Tarea',
                'Proyecto',
                'Estado',
                'Responsable',
                'Cobrable',
                'Puntos',
                'Vence',
                'Entrega',
                'Creada',
            ]);

            foreach ($tasksQuery->cursor() as $task) {
                fputcsv($output, [
                    $task->id,
                    $task->name,
                    $task->project_name ?? '',
                    $task->status_name ?? '',
                    $task->user_name ?? '',
                    $task->value_generated ? 'Si' : 'No',
                    $task->points !== null ? number_format((float) $task->points, 2, '.', '') : '',
                    $task->due_date?->format('Y-m-d H:i') ?? '',
                    $task->delivery_date?->format('Y-m-d') ?? '',
                    $task->created_at?->format('Y-m-d H:i') ?? '',
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function create()
    {
        $options = $this->formOptions();

        $task = new Task([
            'due_date' => now()->startOfDay(),
            'priority' => 1,
            'user_id' => Auth::id(),
        ]);

        return view('tasks.create', array_merge($options, [
            'task' => $task,
            'submit' => 'Crear',
        ]));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if (! isset($data['priority'])) {
            $data['priority'] = 1;
        }

        if (! isset($data['user_id']) || ! $data['user_id']) {
            $data['user_id'] = Auth::id();
        }

        $data['creator_user_id'] = Auth::id();
        $data['updator_user_id'] = Auth::id();
        $data['value_generated'] = $request->boolean('value_generated', true);
        $data['not_billing'] = $request->boolean('not_billing');
        $data['due_date'] = $this->parseDateTime($request->input('due_date')) ?? now()->startOfDay();
        $data['delivery_date'] = $this->parseDateTime($request->input('delivery_date'));

        if ($request->hasFile('file')) {
            $data['file_url'] = $request->file('file')->store('files/tasks', 'public');
        }

        $task = Task::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tarea creada.',
                'task' => [
                    'id' => $task->id,
                    'name' => $task->name,
                    'project_id' => $task->project_id,
                    'user_id' => $task->user_id,
                    'status_id' => $task->status_id,
                    'value_generated' => (bool) $task->value_generated,
                ],
            ], 201);
        }

        return redirect()->route('tasks.index')->with('status', 'Tarea creada.');
    }

    public function show(Request $request, Task $task)
    {
        $task->load(['project', 'user', 'status', 'type', 'subType']);

        if ($request->boolean('sidebar')) {
            return view('tasks.partials.show_panel', ['task' => $task]);
        }

        return view('tasks.show', ['task' => $task]);
    }

    public function quickAssign(Request $request, Task $task)
    {
        $data = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'value_generated' => ['nullable', 'boolean'],
        ]);

        $task->fill($data);
        $task->updator_user_id = Auth::id();
        $task->save();
        $task->load(['project:id,name,color', 'user:id,name,image_url']);

        return response()->json([
            'message' => 'Asignación actualizada.',
            'task' => [
                'id' => $task->id,
                'project_id' => $task->project_id,
                'user_id' => $task->user_id,
                'value_generated' => (bool) $task->value_generated,
                'project' => $task->project ? [
                    'id' => $task->project->id,
                    'name' => $task->project->name,
                    'color' => $task->project->color,
                ] : null,
                'user' => $task->user ? [
                    'id' => $task->user->id,
                    'name' => $task->user->name,
                    'image_url' => $task->user->image_url,
                ] : null,
            ],
        ]);
    }

    public function edit(Request $request, Task $task)
    {
        $options = $this->formOptions();

        if ($request->boolean('sidebar')) {
            return view('tasks.partials.edit_panel', array_merge($options, [
                'task' => $task,
                'submit' => 'Actualizar',
            ]));
        }

        return view('tasks.edit', array_merge($options, [
            'task' => $task,
            'submit' => 'Actualizar',
        ]));
    }

    public function update(Request $request, Task $task)
    {
        $data = $this->validateData($request);
        $data['updator_user_id'] = Auth::id();
        $data['value_generated'] = $request->has('value_generated')
            ? $request->boolean('value_generated')
            : $task->value_generated;
        $data['not_billing'] = $request->has('not_billing')
            ? $request->boolean('not_billing')
            : $task->not_billing;
        $data['due_date'] = $this->parseDateTime($request->input('due_date'));
        $data['delivery_date'] = $this->parseDateTime($request->input('delivery_date'));

        if ($request->boolean('remove_file') && $task->file_url) {
            Storage::disk('public')->delete($task->file_url);
            $data['file_url'] = null;
        }

        if ($request->hasFile('file')) {
            if ($task->file_url) {
                Storage::disk('public')->delete($task->file_url);
            }
            $data['file_url'] = $request->file('file')->store('files/tasks', 'public');
        }

        $task->update($data);

        return redirect()->route('tasks.index')->with('status', 'Tarea actualizada.');
    }

    public function destroy(Task $task)
    {
        if ($task->file_url) {
            Storage::disk('public')->delete($task->file_url);
        }

        $task->delete();

        return redirect()->route('tasks.index')->with('status', 'Tarea eliminada.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
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
            'file' => ['nullable', 'file', 'max:10240'],
            'remove_file' => ['nullable', 'boolean'],
        ]);
    }

    protected function formOptions(): array
    {
        $statuses = TaskStatus::active()->orderBy('weight')->get();
        $projects = Project::where('status_id', 3)
            ->orderBy('weight')
            ->orderBy('name')
            ->get(['id', 'name', 'color']);
        $users = User::where('status_id', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'image_url', 'status_id']);
        $types = TaskType::orderBy('name')->get();

        $parentTypes = $types->whereNull('parent_id')->values();
        $subTypes = $types->whereNotNull('parent_id')
            ->map(function ($type) use ($types) {
                $parent = $types->firstWhere('id', $type->parent_id);
                $type->label = $parent ? "{$parent->name} → {$type->name}" : $type->name;

                return $type;
            })
            ->values();

        $defaultStatusId = $statuses->first()?->id;

        return compact('statuses', 'projects', 'users', 'parentTypes', 'subTypes', 'defaultStatusId');
    }

    protected function parseDateTime(?string $value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }

    /**
     * @return array{0: bool, 1: array<int, int>}
     */
    protected function taskVisibilityScope(): array
    {
        $authUser = Auth::user();
        $isClientRole = (int) ($authUser?->role_id ?? 0) === 4;
        $clientProjectIds = $isClientRole
            ? $authUser->projects()->pluck('projects.id')->all()
            : [];

        return [$isClientRole, $clientProjectIds];
    }

    /**
     * @return array{
     *     status_id: mixed,
     *     project_id: mixed,
     *     user_id: mixed,
     *     from_date: ?string,
     *     to_date: ?string,
     *     value_generated: bool,
     *     q: ?string
     * }
     */
    protected function resolveTaskFilters(
        Request $request,
        bool $isClientRole,
        Carbon $defaultFromDate,
        Carbon $defaultToDate
    ): array {
        $statusId = $request->input('status_id');
        $projectId = $request->input('project_id');
        $userIdParam = $request->input('user_id');
        $userId = $request->has('user_id')
            ? ($userIdParam === '' ? null : $userIdParam)
            : ($isClientRole ? null : Auth::id());
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($request->filled('range')) {
            $parts = explode('|', $request->input('range'));
            if (count($parts) === 2) {
                try {
                    $fromDate = Carbon::createFromFormat('Y-m-d', trim($parts[0]))->toDateString();
                    $toDate = Carbon::createFromFormat('Y-m-d', trim($parts[1]))->toDateString();
                } catch (\Exception $e) {
                    // Ignore invalid range format.
                }
            }
        } else {
            $fromDate = $fromDate ? Carbon::parse($fromDate)->toDateString() : $defaultFromDate->toDateString();
            $toDate = $toDate ? Carbon::parse($toDate)->toDateString() : $defaultToDate->toDateString();
        }

        return [
            'status_id' => $statusId,
            'project_id' => $projectId,
            'user_id' => $userId,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'value_generated' => $request->boolean('value_generated'),
            'q' => $request->input('q'),
        ];
    }

    /**
     * @param  array{
     *     status_id: mixed,
     *     project_id: mixed,
     *     user_id: mixed,
     *     from_date: ?string,
     *     to_date: ?string,
     *     value_generated: bool,
     *     q: ?string
     * }  $filters
     * @param  array<int, int>  $clientProjectIds
     */
    protected function buildFilteredTasksQuery(
        array $filters,
        bool $isClientRole,
        array $clientProjectIds,
        bool $withRelations = true
    ): Builder {
        $tasksQuery = Task::query();

        if ($withRelations) {
            $tasksQuery->with([
                'project:id,name,color',
                'user:id,name,image_url,status_id',
                'status:id,name,alias,color,background_color,pending',
            ]);
        }

        return $tasksQuery
            ->when($isClientRole, fn ($query) => $query->whereIn('tasks.project_id', $clientProjectIds))
            ->when($filters['status_id'], fn ($query, $statusId) => $query->where('tasks.status_id', $statusId))
            ->when($filters['project_id'], fn ($query, $projectId) => $query->where('tasks.project_id', $projectId))
            ->when($filters['user_id'], fn ($query, $userId) => $query->where('tasks.user_id', $userId))
            ->when($filters['value_generated'], fn ($query) => $query->where('tasks.value_generated', 1))
            ->when($filters['from_date'] && $filters['to_date'], function ($query) use ($filters) {
                $start = Carbon::parse($filters['from_date'])->startOfDay();
                $end = Carbon::parse($filters['to_date'])->endOfDay();

                $query->whereBetween('tasks.due_date', [$start, $end]);
            })
            ->when($filters['q'], function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('tasks.name', 'like', "%{$search}%")
                        ->orWhere('tasks.description', 'like', "%{$search}%")
                        ->orWhere('tasks.caption', 'like', "%{$search}%");
                });
            });
    }
}
