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
            ->where('status_id', 1)
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        return view('timer.index', [
            'tasks' => $tasks,
        ]);
    }
}
