@php
    $publicBriefUrl = route('public.briefs.edit', $brief->public_token);
@endphp

<div class="flex flex-wrap items-center gap-2 text-xs" x-data="{ copied: false, link: @js($publicBriefUrl) }">
    <a href="{{ $publicBriefUrl }}" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-500 font-semibold">Abrir enlace</a>
    <button
        type="button"
        class="px-2 py-1 border rounded text-gray-700 hover:bg-gray-50"
        @click="navigator.clipboard.writeText(link); copied = true; setTimeout(() => copied = false, 1800)"
    >
        <span x-show="!copied">Copiar enlace</span>
        <span x-show="copied">Copiado</span>
    </button>
</div>
