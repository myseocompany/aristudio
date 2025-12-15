<?php

namespace App\Http\Controllers;

use App\Http\Requests\Timer\TimerStartRequest;
use App\Http\Requests\Timer\TimerStoreRequest;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TimerController extends Controller
{
    public const MAX_SECONDS = 7200;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $tasks = Task::with([
            'project:id,name,color',
            'status:id,name,pending,color,background_color',
        ])
            ->where('user_id', Auth::id())
            ->where('status_id', 1)
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        $projects = Project::where('status_id', 3)
            ->orderBy('weight')
            ->orderBy('name')
            ->get(['id', 'name', 'color']);

        $prefillTask = null;
        if ($request->filled('task')) {
            $candidate = Task::with('project:id,name')
                ->where('id', $request->integer('task'))
                ->where('user_id', Auth::id())
                ->first();
            if ($candidate) {
                $prefillTask = [
                    'id' => (string) $candidate->id,
                    'name' => $candidate->name,
                    'project_id' => $candidate->project_id ? (string) $candidate->project_id : '',
                    'project_name' => $candidate->project->name ?? '',
                    'status_id' => (string) ($candidate->status_id ?? ''),
                ];
            }
        }

        return view('timer.index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'maxSeconds' => self::MAX_SECONDS,
            'prefillTask' => $prefillTask,
        ]);
    }

    public function status(): JsonResponse
    {
        $session = $this->readSession();

        return response()->json($this->formatSession($session));
    }

    public function start(TimerStartRequest $request): JsonResponse
    {
        $session = $this->readSession();
        $baseElapsed = $this->elapsedSeconds($session);
        $validated = $request->validated();

        $session = [
            'running' => true,
            'task_id' => $validated['task_id'] ?? null,
            'task_label' => $validated['task_label'],
            'project_id' => $validated['project_id'] ?? null,
            'project_name' => $validated['project_name'] ?? '',
            'started_at' => Carbon::now(),
            'elapsed_seconds' => $baseElapsed,
        ];

        $this->storeSession($session);

        return response()->json($this->formatSession($session));
    }

    public function pause(): JsonResponse
    {
        $session = $this->readSession();
        $session['elapsed_seconds'] = $this->elapsedSeconds($session);
        $session['started_at'] = null;
        $session['running'] = false;

        $this->storeSession($session);

        return response()->json($this->formatSession($session));
    }

    public function reset(): JsonResponse
    {
        $this->clearSession();

        return response()->json($this->formatSession($this->defaultSession()));
    }

    public function store(TimerStoreRequest $request): JsonResponse
    {
        $session = $this->readSession();
        $validated = $request->validated();

        $seconds = $this->elapsedSeconds($session);
        if ($seconds === 0) {
            $seconds = $validated['seconds'];
        }

        $points = round($seconds / 3600, 2);

        $task = Task::create([
            'name' => $validated['name'],
            'project_id' => $validated['project_id'] ?? null,
            'user_id' => Auth::id(),
            'status_id' => 6,
            'points' => $points,
            'creator_user_id' => Auth::id(),
            'updator_user_id' => Auth::id(),
            'due_date' => now()->startOfDay(),
            'value_generated' => true,
        ]);

        $this->clearSession();

        return response()->json([
            'ok' => true,
            'task_id' => $task->id,
            'points' => $task->points,
            'seconds' => $seconds,
        ]);
    }

    private function timerCacheKey(): string
    {
        return 'timer:session:user:'.Auth::id();
    }

    private function defaultSession(): array
    {
        return [
            'running' => false,
            'task_id' => null,
            'task_label' => '',
            'project_id' => null,
            'project_name' => '',
            'started_at' => null,
            'elapsed_seconds' => 0,
        ];
    }

    private function readSession(): array
    {
        return Cache::get($this->timerCacheKey(), $this->defaultSession());
    }

    private function storeSession(array $session): void
    {
        Cache::put($this->timerCacheKey(), $session, now()->addDay());
    }

    private function clearSession(): void
    {
        Cache::forget($this->timerCacheKey());
    }

    private function elapsedSeconds(array $session): int
    {
        $elapsed = (int) ($session['elapsed_seconds'] ?? 0);

        if (($session['running'] ?? false) && ! empty($session['started_at'])) {
            $elapsed += Carbon::parse($session['started_at'])->diffInSeconds(now());
        }

        return min($elapsed, self::MAX_SECONDS);
    }

    private function formatSession(array $session): array
    {
        $elapsed = $this->elapsedSeconds($session);

        return [
            'running' => (bool) ($session['running'] ?? false),
            'elapsed' => $elapsed,
            'task_id' => $session['task_id'] ?? null,
            'task_label' => $session['task_label'] ?? '',
            'project_id' => $session['project_id'] ?? null,
            'project_name' => $session['project_name'] ?? '',
            'started_at' => ! empty($session['started_at']) ? Carbon::parse($session['started_at'])->toIso8601String() : null,
            'max_seconds' => self::MAX_SECONDS,
        ];
    }
}
