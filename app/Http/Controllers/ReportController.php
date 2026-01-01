<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->authorizeModule($request, '/reports', [
                'usersByMonth' => 'read',
            ]);

            return $next($request);
        });
    }

    public function usersByMonth(Request $request): View
    {
        $currentYear = (int) now()->year;
        $year = (int) $request->input('year', $currentYear);

        $months = collect(range(1, 12))->map(fn ($month) => Carbon::create($year, $month, 1));

        $tasks = Task::query()
            ->select(['user_id', 'points', 'due_date'])
            ->whereYear('due_date', $year)
            ->whereIn('status_id', [6, 56])
            ->whereNotNull('due_date')
            ->whereNotNull('user_id')
            ->get();

        $matrix = [];
        foreach ($tasks as $task) {
            $userId = (int) $task->user_id;
            $monthNumber = Carbon::parse($task->due_date)->month;
            $current = $matrix[$userId][$monthNumber] ?? 0;
            $matrix[$userId][$monthNumber] = $current + (float) ($task->points ?? 0);
        }

        $userIds = array_keys($matrix);

        $users = collect();
        if (! empty($userIds)) {
            $users = User::query()
                ->whereIn('id', $userIds)
                ->orderBy('name')
                ->get(['id', 'name', 'hourly_rate', 'email']);
        }

        $yearOptions = collect(range($currentYear - 2, $currentYear + 1));
        if (! $yearOptions->contains($year)) {
            $yearOptions->push($year);
            $yearOptions = $yearOptions->sort()->values();
        }

        return view('reports.users_by_month', [
            'year' => $year,
            'months' => $months,
            'users' => $users,
            'matrix' => $matrix,
            'yearOptions' => $yearOptions,
        ]);
    }
}
