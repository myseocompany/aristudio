<?php

namespace App\Http\Controllers;

use App\Models\Task;
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
            ->whereHas('status', fn ($q) => $q->where('pending', 1))
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        return view('timer.index', [
            'tasks' => $tasks,
        ]);
    }
}
