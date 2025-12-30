<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillingReportRequest;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class BillingController extends Controller
{
    private const BILLABLE_STATUS_IDS = [6, 56];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(BillingReportRequest $request): View
    {
        $validated = $request->validated();
        $selectedMonth = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $rangeStart = $selectedMonth->copy()->startOfMonth()->startOfDay();
        $rangeEnd = $selectedMonth->copy()->endOfMonth()->endOfDay();

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'hourly_rate', 'email', 'status_id', 'document']);

        $selectedUser = $users->firstWhere('id', (int) $validated['user_id'])
            ?? User::query()->find($validated['user_id']);

        $tasks = Task::query()
            ->with(['project:id,name'])
            ->where('user_id', $selectedUser?->id)
            ->whereBetween('due_date', [$rangeStart, $rangeEnd])
            ->whereNotNull('due_date')
            ->whereIn('status_id', self::BILLABLE_STATUS_IDS)
            ->where(function ($query): void {
                $query->whereNull('not_billing')
                    ->orWhere('not_billing', false);
            })
            ->orderBy('due_date')
            ->orderBy('id')
            ->get([
                'id',
                'name',
                'points',
                'project_id',
                'due_date',
                'not_billing',
                'status_id',
            ]);

        $pointsTotal = $tasks->sum(fn (Task $task): float => (float) ($task->points ?? 0));
        $hourlyRate = (float) ($selectedUser?->hourly_rate ?? 0);
        $amount = round($pointsTotal * $hourlyRate, 2);

        return view('billing.index', [
            'users' => $users,
            'selectedUser' => $selectedUser,
            'selectedMonth' => $selectedMonth,
            'range' => [
                'from' => $rangeStart,
                'to' => $rangeEnd,
            ],
            'tasks' => $tasks,
            'summary' => [
                'points' => $pointsTotal,
                'hourly_rate' => $hourlyRate,
                'amount' => $amount,
                'tasks' => $tasks->count(),
            ],
            'params' => [
                'month' => $validated['month'],
                'user_id' => (int) $validated['user_id'],
            ],
        ]);
    }
}
