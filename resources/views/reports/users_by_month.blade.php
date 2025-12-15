<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500">Reportes</p>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usuarios por mes</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="flex flex-wrap items-end gap-3 text-sm bg-white border border-gray-100 rounded-xl shadow-sm p-4">
                <div>
                    <label for="year" class="text-gray-600 font-medium block mb-1">Seleccione año</label>
                    <select id="year" name="year" class="rounded-lg border-gray-300 px-3 py-2 focus:border-brand-600 focus:ring-brand-600">
                        @foreach($yearOptions as $option)
                            <option value="{{ $option }}" @selected($year == $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-5">
                    <x-primary-button>Aplicar</x-primary-button>
                </div>
            </form>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-fixed">
                        <thead>
                            <tr class="text-sm text-gray-500">
                                <th class="w-48 px-4 py-3 text-left font-semibold uppercase tracking-wide">Usuario</th>
                                @foreach($months as $month)
                                    <th class="px-3 py-3 text-center font-semibold uppercase tracking-wide">{{ $month->translatedFormat('M') }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr class="border-t border-gray-100">
                                    <td class="px-4 py-4 align-top">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                            @if($user->email)
                                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    @foreach($months as $month)
                                        @php
                                            $points = $matrix[$user->id][$month->month] ?? 0;
                                            $amount = $user->hourly_rate ? $points * (float) $user->hourly_rate : null;
                                        @endphp
                                        <td class="px-3 py-4 text-center">
                                            @if($points > 0)
                                                <div class="text-sm font-semibold text-gray-900">{{ number_format($points, 2, '.', ',') }} pts</div>
                                                @if(! is_null($amount))
                                                    <div class="text-xs text-emerald-600 font-medium">${{ number_format($amount, 2, '.', ',') }}</div>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="px-4 py-6 text-center text-gray-500">No hay usuarios activos para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
