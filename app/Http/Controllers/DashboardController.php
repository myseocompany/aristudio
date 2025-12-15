<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private const BILLING_STATUS_IDS = [6, 56];

    public function __invoke(Request $request): View
    {
        $user = Auth::user();
        $defaultEnd = Carbon::now()->endOfDay();
        $defaultStart = $defaultEnd->copy()->subDays(29)->startOfDay();

        [$rangeStart, $rangeEnd] = $this->resolveRange($request->query('range'), $defaultStart, $defaultEnd);

        $groupedTasks = Task::query()
            ->select(['status_id', 'due_date', 'points'])
            ->where('user_id', $user->id)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$rangeStart, $rangeEnd])
            ->whereIn('status_id', array_merge([1], self::BILLING_STATUS_IDS))
            ->get()
            ->groupBy(fn (Task $task) => $task->due_date?->copy()->startOfWeek(Carbon::MONDAY)->toDateString() ?? 'unknown');

        $labels = [];
        $reqSeries = [];
        $billingSeries = [];

        $windowStart = $rangeStart->copy()->startOfWeek(Carbon::MONDAY);
        $windowEnd = $rangeEnd->copy()->endOfWeek(Carbon::SUNDAY);

        for ($cursor = $windowStart->copy(); $cursor->lessThanOrEqualTo($windowEnd); $cursor->addWeek()) {
            $weekKey = $cursor->toDateString();
            $weekTasks = $groupedTasks->get($weekKey, collect());
            $labels[] = $cursor->translatedFormat('d M');
            $reqSeries[] = $weekTasks
                ->filter(fn (Task $task) => (int) $task->status_id === 1)
                ->count();
            $billingSeries[] = $weekTasks
                ->filter(fn (Task $task) => in_array((int) $task->status_id, self::BILLING_STATUS_IDS, true))
                ->count();
        }

        $flatTasks = $groupedTasks->flatten();
        $summaryTasks = Task::query()
            ->select(['status_id', 'points'])
            ->where('user_id', $user->id)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$rangeStart, $rangeEnd])
            ->get();

        $chartData = [
            'labels' => $labels,
            'req' => $reqSeries,
            'billing' => $billingSeries,
        ];

        $pointsTotal = $summaryTasks->sum(fn (Task $task) => (float) ($task->points ?? 0));
        $hourlyRate = (float) ($user->hourly_rate ?? 0);

        $summary = [
            'req' => $summaryTasks->where('status_id', 1)->count(),
            'billing' => $summaryTasks->filter(fn ($task) => in_array((int) $task->status_id, self::BILLING_STATUS_IDS, true))->count(),
            'points' => $pointsTotal,
            'amount' => round($pointsTotal * $hourlyRate, 2),
            'hourly_rate' => $hourlyRate,
            'range' => [
                'from' => $rangeStart,
                'to' => $rangeEnd,
            ],
            'range_value' => $request->query('range', $this->formatRangeValue($rangeStart, $rangeEnd)),
        ];
        $today = Carbon::today();
        $todayTasks = Task::with(['status:id,name,color,background_color,pending'])
            ->where('user_id', $user->id)
            ->whereNotNull('due_date')
            ->whereDate('due_date', $today)
            ->orderBy('due_date')
            ->take(5)
            ->get(['id', 'name', 'due_date', 'status_id']);

        $overdueTasks = Task::with(['status:id,name,color,background_color,pending'])
            ->where('user_id', $user->id)
            ->whereNotNull('due_date')
            ->where('status_id', 1)
            ->whereDate('due_date', '<', $today)
            ->whereHas('status', fn ($query) => $query->where('pending', 1))
            ->orderByDesc('due_date')
            ->take(5)
            ->get(['id', 'name', 'due_date', 'status_id']);

        return view('dashboard', [
            'chartData' => $chartData,
            'taskSummary' => $summary,
            'todayTasks' => $todayTasks,
            'overdueTasks' => $overdueTasks,
        ]);
    }

    private function resolveRange(?string $rawRange, Carbon $defaultStart, Carbon $defaultEnd): array
    {
        if ($rawRange) {
            $parts = explode('|', $rawRange);
            if (count($parts) === 2) {
                try {
                    $start = Carbon::createFromFormat('Y-m-d', trim($parts[0]))->startOfDay();
                    $end = Carbon::createFromFormat('Y-m-d', trim($parts[1]))->endOfDay();
                    if ($start->lessThanOrEqualTo($end)) {
                        return [$start, $end];
                    }
                } catch (\Exception $e) {
                    // ignore and fallback to defaults
                }
            }
        }

        return [$defaultStart, $defaultEnd];
    }

    private function formatRangeValue(Carbon $start, Carbon $end): string
    {
        return $start->format('Y-m-d').'|'.$end->format('Y-m-d');
    }
}
