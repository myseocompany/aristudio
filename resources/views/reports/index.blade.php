<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Reports</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Hoja en blanco</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-dashed border-gray-200 min-h-[320px] flex flex-col items-center justify-center text-center text-gray-500 text-lg gap-4 px-8">
                <p>Este espacio está listo para tus reportes.</p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('reports.users_by_month') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-brand-600 text-white font-semibold shadow hover:bg-brand-500 transition">
                        <span>Ver reporte de usuarios por mes</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="{{ route('billing.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-emerald-600 text-white font-semibold shadow hover:bg-emerald-500 transition">
                        <span>Generar cuenta de cobro</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
