@php
    $appVersion = trim((string) file_get_contents(base_path('VERSION')));
@endphp

<footer class="border-t border-gray-200 bg-white/80">
    <div class="mx-auto max-w-7xl px-4 py-3 text-center text-xs text-gray-500 sm:px-6 lg:px-8">
        Versión: {{ $appVersion !== '' ? $appVersion : '0.0.0' }}
    </div>
</footer>
