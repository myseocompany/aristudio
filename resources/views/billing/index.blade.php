<x-app-layout>
    @php
        $periodLabel = ucfirst($selectedMonth->locale('es')->translatedFormat('F Y'));
        $fromLabel = $range['from']->translatedFormat('d M');
        $toLabel = $range['to']->translatedFormat('d M');
        $amountDisplay = $summary['amount'] > 0
            ? '$'.number_format($summary['amount'], 2, '.', ',')
            : '—';
    @endphp
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500">Facturacion</p>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cuentas de cobro</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <form method="GET" class="grid gap-4 md:grid-cols-4 items-end">
                    <div class="md:col-span-2">
                        <label for="month" class="text-sm text-gray-600 font-medium block mb-1">Mes</label>
                        <input
                            type="month"
                            id="month"
                            name="month"
                            value="{{ $params['month'] }}"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm focus:border-brand-600 focus:ring-brand-600"
                        >
                    </div>
                    <div class="md:col-span-2">
                        <label for="user_id" class="text-sm text-gray-600 font-medium block mb-1">Usuario</label>
                        <select
                            id="user_id"
                            name="user_id"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm focus:border-brand-600 focus:ring-brand-600"
                        >
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected((int) $params['user_id'] === (int) $user->id)>
                                    {{ $user->name }} @if($user->hourly_rate) ({{ number_format((float) $user->hourly_rate, 2, '.', ',') }}/h) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-primary-button class="w-full justify-center">Generar</x-primary-button>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-3">
                    Solo se suman tareas con fecha de entrega en el mes seleccionado, estados 6/56 y no marcadas como "no facturar".
                </p>
            </div>

            <div class="grid gap-3 md:grid-cols-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Periodo</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $periodLabel }}</p>
                    <p class="text-sm text-gray-500">Del {{ $fromLabel }} al {{ $toLabel }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Puntos facturados</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($summary['points'], 2, '.', ',') }}</p>
                    <p class="text-sm text-gray-500">{{ $summary['tasks'] }} tareas</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Valor hora</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ $summary['hourly_rate'] ? '$'.number_format($summary['hourly_rate'], 2, '.', ',') : '—' }}
                    </p>
                    <p class="text-sm text-gray-500">Tarifa del usuario</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Total a cobrar</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $amountDisplay }}</p>
                    <p class="text-sm text-gray-500">Puntos x valor hora</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-500">Detalle</p>
                        <h3 class="text-lg font-semibold text-gray-900">Tareas facturables</h3>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-600">Estados 6 y 56</span>
                </div>
                @if($tasks->isEmpty())
                    <p class="text-sm text-gray-500">No hay tareas facturables en este periodo.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-gray-500 uppercase tracking-wide">
                                <tr>
                                    <th class="px-3 py-2">Fecha</th>
                                    <th class="px-3 py-2">Tarea</th>
                                    <th class="px-3 py-2">Proyecto</th>
                                    <th class="px-3 py-2 text-right">Puntos</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($tasks as $task)
                                    <tr>
                                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $task->due_date?->translatedFormat('d M') }}</td>
                                        <td class="px-3 py-2 text-gray-900 font-medium">{{ $task->name }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $task->project?->name ?? '—' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-900 font-semibold">{{ number_format((float) ($task->points ?? 0), 2, '.', ',') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if($selectedUser)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Vista previa</p>
                            <h3 class="text-lg font-semibold text-gray-900">Cuenta de cobro</h3>
                        </div>
                        <button type="button" onclick="window.print()" class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-900 text-white hover:bg-gray-700">
                            Imprimir
                        </button>
                    </div>
                    <div class="mt-6 bg-gray-50 border border-dashed border-gray-200 rounded-xl p-6 text-gray-700 space-y-4">
                        <div class="text-right text-sm text-gray-500">
                            {{ now()->locale('es')->translatedFormat('F j, Y') }}
                        </div>
                        <div class="text-center space-y-1">
                            <p class="font-semibold text-gray-900 uppercase tracking-wide">Cuenta de cobro</p>
                            <p class="text-gray-600">{{ $periodLabel }}</p>
                        </div>
                        <div class="text-center space-y-1">
                            <p class="text-sm text-gray-500">Debe a:</p>
                            <p class="font-semibold text-gray-900">{{ $selectedUser->name }}</p>
                            @if($selectedUser->document)
                                <p class="text-sm text-gray-600">Documento: {{ $selectedUser->document }}</p>
                            @endif
                        </div>
                        <div class="text-center py-4">
                            <p class="text-xs uppercase tracking-wide text-gray-500">La suma de</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $amountDisplay }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ number_format($summary['points'], 2, '.', ',') }} puntos x {{ number_format($summary['hourly_rate'], 2, '.', ',') }} / hora</p>
                        </div>
                        <div class="text-sm text-gray-600 leading-relaxed">
                            <p>Periodo: {{ $periodLabel }} ({{ $fromLabel }} - {{ $toLabel }})</p>
                            <p>Total tareas: {{ $summary['tasks'] }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
