<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $statusId = $request->input('status_id');
        $projectId = $request->input('project_id');
        $now = Carbon::now();
        $defaultFromDate = $now->copy()->startOfMonth();
        $defaultToDate = $now->copy()->endOfMonth();

        $userIdParam = $request->input('user_id');
        $userId = $request->has('user_id')
            ? ($userIdParam === '' ? null : $userIdParam)
            : Auth::id();
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        if ($request->filled('range')) {
            $parts = explode('|', $request->input('range'));
            if (count($parts) === 2) {
                try {
                    $start = Carbon::createFromFormat('Y-m-d', trim($parts[0]))->toDateString();
                    $end = Carbon::createFromFormat('Y-m-d', trim($parts[1]))->toDateString();
                    $fromDate = $start;
                    $toDate = $end;
                } catch (\Exception $e) {
                    // Ignore invalid range format.
                }
            }
        } else {
            $fromDate = $fromDate ? Carbon::parse($fromDate)->toDateString() : $defaultFromDate->toDateString();
            $toDate = $toDate ? Carbon::parse($toDate)->toDateString() : $defaultToDate->toDateString();
        }
        $onlyValueGenerated = $request->boolean('value_generated');
        $search = $request->input('q');

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

        $tasksQuery = Task::query()
            ->with([
                'project:id,name,color',
                'user:id,name,image_url,status_id',
                'status:id,name,alias,color,background_color,pending',
            ])
            ->when($statusId, fn ($q) => $q->where('status_id', $statusId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($onlyValueGenerated, fn ($q) => $q->where('value_generated', 1))
            ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                $start = Carbon::parse($fromDate)->startOfDay();
                $end = Carbon::parse($toDate)->endOfDay();

                $q->whereBetween('due_date', [$start, $end]);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('caption', 'like', "%{$search}%");
                });
            });

        $selectedPointsTotal = (clone $tasksQuery)->sum('points');

        $tasks = $tasksQuery
            ->orderByDesc('created_at')
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
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
                'status_id' => $statusId,
                'project_id' => $projectId,
                'user_id' => $userId,
                'value_generated' => $onlyValueGenerated,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'q' => $search,
            ],
        ]);
    }

    public function create()
    {
        $options = $this->formOptions();

        $task = new Task([
            'due_date' => now()->startOfDay(),
            'priority' => 1,
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
        $data['value_generated'] = $request->boolean('value_generated');
        $data['not_billing'] = $request->boolean('not_billing');
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
}
