<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php
        $rangeLabel = $taskSummary['range']['from']->translatedFormat('d M').' - '.$taskSummary['range']['to']->translatedFormat('d M');
        $totalTasks = $taskSummary['req'] + $taskSummary['billing'];
        $pointsDisplay = number_format((float) $taskSummary['points'], 2, '.', ',');
        $amountDisplay = number_format((float) $taskSummary['amount'], 0, '.', ',');
        $rateDisplay = number_format((float) $taskSummary['hourly_rate'], 0, '.', ',');
        $rangeValue = $taskSummary['range_value'];
        $currentUser = auth()->user();
        $avatarPath = $currentUser?->image_url
            ? (str_contains($currentUser->image_url, '/') ? $currentUser->image_url : 'files/users/'.$currentUser->image_url)
            : null;
        $userInitials = collect(explode(' ', trim($currentUser?->name ?? '')))
            ->filter()
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');
    @endphp
    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="space-y-10">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="flex items-center gap-4 max-w-2xl">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm text-gray-600 uppercase tracking-wide">Planifica tu día</p>
                            <p class="text-3xl font-semibold text-gray-900 leading-tight">Hola, {{ auth()->user()?->name ?? 'Ari' }}.!</p>
                            <p class="text-xl font-semibold text-gray-900 leading-tight">¿Qué planes tienes para hoy?</p>
                            <p class="text-sm text-gray-600">Esta plataforma está diseñada para ayudarte a lograr tus metas.</p>
                        </div>

                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
                        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-5 flex flex-col">
                            <p class="text-3xl font-semibold text-gray-900">${{ $amountDisplay }}</p>
                            <p class="text-sm text-gray-500">Valor estimado</p>
                            <p class="text-xs text-emerald-600 font-medium mt-1">Tarifa actual: ${{ $rateDisplay }}/h</p>
                        </div>
                        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-5 flex flex-col">
                            <p class="text-3xl font-semibold text-gray-900">{{ $pointsDisplay }}</p>
                            <p class="text-sm text-gray-500">Puntos generados</p>
                            <p class="text-xs text-indigo-600 font-medium mt-1">Último rango</p>
                        </div>
                        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-5 flex flex-col">
                            <p class="text-3xl font-semibold text-gray-900">{{ $taskSummary['req'] }}</p>
                            <p class="text-sm text-gray-500">Tareas REQ</p>
                            <p class="text-xs text-amber-600 font-medium mt-1">Estado 1</p>
                        </div>
                        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-5 flex flex-col">
                            <p class="text-3xl font-semibold text-gray-900">{{ $taskSummary['billing'] }}</p>
                            <p class="text-sm text-gray-500">Ver/Billing</p>
                            <p class="text-xs text-sky-600 font-medium mt-1">Estados 6 y 56</p>
                        </div>
                    </div>
                </div>
                <div class="grid lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-8 bg-white shadow-sm rounded-2xl border border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Resumen mensual</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalTasks }} tareas</p>
                            </div>
                        <div class="flex items-center gap-3">
                            <form method="GET" id="rangeForm" class="flex items-center">
                                <input type="text" id="rangePicker" class="w-64 px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white shadow-sm cursor-pointer" readonly>
                                <input type="hidden" name="range" id="rangeValue" value="{{ $rangeValue }}">
                            </form>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-700">Rango seleccionado</p>
                                <p class="text-xs text-gray-500">{{ $rangeLabel }}</p>
                            </div>
                        </div>
                        </div>
                        <div class="mt-6">
                            <canvas id="tasksTrendChart" height="220"></canvas>
                        </div>
                    </div>
                    <div class="lg:col-span-4 space-y-4">
                        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-5">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-900">Tareas para hoy</p>
                                <span class="text-xs text-gray-500">{{ $todayTasks->count() }}</span>
                            </div>
                            <div class="mt-3 space-y-3">
                                @forelse($todayTasks as $task)
                                    <div class="flex items-center justify-between gap-3 border border-gray-100 rounded-xl px-3 py-2">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $task->name }}</p>
                                            <p class="text-xs text-gray-500">Hora: {{ optional($task->due_date)->format('H:i') ?? 'Sin hora' }}</p>
                                        </div>
                                        <a href="{{ route('timer.index', ['task' => $task->id]) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100" title="Ir al timer">
                                            ▶
                                        </a>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No tienes tareas para hoy.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-5">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-900">Tareas vencidas</p>
                                <span class="text-xs text-gray-500">{{ $overdueTasks->count() }}</span>
                            </div>
                            <div class="mt-3 space-y-3">
                                @forelse($overdueTasks as $task)
                                    <div class="flex items-center justify-between gap-3 border border-gray-100 rounded-xl px-3 py-2">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $task->name }}</p>
                                            <p class="text-xs text-rose-600">Venció: {{ optional($task->due_date)->format('d M') }}</p>
                                        </div>
                                        @if($task->status)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]" style="background: {{ $task->status->background_color ?? '#fee2e2' }}; color: {{ $task->status->color ?? '#b91c1c' }}">
                                                {{ $task->status->name }}
                                            </span>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Sin tareas vencidas.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment-with-locales.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            moment.locale('es');
            const rangeInput = $('#rangePicker');
            const hiddenRange = $('#rangeValue');
            const rangeForm = document.getElementById('rangeForm');
            const startDate = moment('{{ $taskSummary['range']['from']->format('Y-m-d') }}', 'YYYY-MM-DD');
            const endDate = moment('{{ $taskSummary['range']['to']->format('Y-m-d') }}', 'YYYY-MM-DD');

            function updateDisplay(start, end) {
                rangeInput.val(`${start.format('DD MMM YYYY')} - ${end.format('DD MMM YYYY')}`);
                hiddenRange.val(`${start.format('YYYY-MM-DD')}|${end.format('YYYY-MM-DD')}`);
            }

            const presetRanges = {
                'Hoy': [moment().startOf('today'), moment().endOf('today')],
                'Este mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Trimestre pasado': [moment().subtract(3, 'month').startOf('quarter'), moment().subtract(3, 'month').endOf('quarter')],
                'Semestre pasado': (function () {
                    const start = moment().startOf('month').subtract(6, 'month').startOf('month');
                    const end = start.clone().add(5, 'month').endOf('month');
                    return [start, end];
                })(),
                'Este año': [moment().startOf('year'), moment().endOf('year')],
                'Año pasado': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            };

            if (rangeInput.length) {
                rangeInput.daterangepicker({
                    startDate,
                    endDate,
                    ranges: presetRanges,
                    locale: {
                        format: 'DD MMM YYYY',
                        applyLabel: 'Aplicar',
                        cancelLabel: 'Cancelar',
                    },
                    opens: 'left',
                }, function(start, end) {
                    updateDisplay(start, end);
                    if (rangeForm) {
                        rangeForm.submit();
                    }
                });
                updateDisplay(startDate, endDate);
            }

            const ctx = document.getElementById('tasksTrendChart');
            if (!ctx) {
                return;
            }
            const chartPayload = @json($chartData);
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartPayload.labels,
                    datasets: [
                        {
                            label: 'REQ',
                            data: chartPayload.req,
                            backgroundColor: '#0f172a',
                            borderRadius: 8,
                            barThickness: 28,
                        },
                        {
                            label: 'Ver o Billing',
                            data: chartPayload.billing,
                            backgroundColor: '#cbd5f5',
                            borderRadius: 8,
                            barThickness: 28,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                color: '#475569',
                            },
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                            },
                        },
                    },
                },
            });
        });
    </script>
</x-app-layout>
