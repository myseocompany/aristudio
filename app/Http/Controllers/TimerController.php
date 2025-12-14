<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tasks = Task::with(['project:id,name,color', 'status:id,name,pending'])
            ->where('user_id', Auth::id())
            ->where('status_id', 1)
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        $projects = Project::where('status_id', 3)
            ->orderBy('weight')
            ->orderBy('name')
            ->get(['id', 'name', 'color']);

        return view('timer.index', [
            'tasks' => $tasks,
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:240'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'seconds' => ['required', 'integer', 'min:1', 'max:7200'],
        ]);

        $points = round($data['seconds'] / 3600, 2);

        $task = Task::create([
            'name' => $data['name'],
            'project_id' => $data['project_id'] ?? null,
            'user_id' => Auth::id(),
            'status_id' => 1,
            'points' => $points,
            'creator_user_id' => Auth::id(),
            'updator_user_id' => Auth::id(),
            'due_date' => now(),
            'value_generated' => true,
        ]);

        return response()->json([
            'ok' => true,
            'task_id' => $task->id,
            'points' => $task->points,
        ]);
    }
}
