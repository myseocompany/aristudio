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
        $defaultEnd = Carbon::now();
        $defaultStart = $defaultEnd->copy()->subDays(29)->startOfDay();

        [$rangeStart, $rangeEnd] = $this->resolveRange($request->query('range'), $defaultStart, $defaultEnd);

        $groupedTasks = Task::query()
            ->select(['status_id', 'created_at', 'points'])
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->whereIn('status_id', array_merge([1], self::BILLING_STATUS_IDS))
            ->get()
            ->groupBy(fn (Task $task) => $task->created_at->copy()->startOfWeek(Carbon::MONDAY)->toDateString());

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
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
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

        return view('dashboard', [
            'chartData' => $chartData,
            'taskSummary' => $summary,
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
