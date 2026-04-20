@php
    $children = $question->children ?? collect();
    $typeId = (int) ($question->type_id ?? 0);
    $effectiveTypeId = $typeId ?: (int) ($parentTypeId ?? 0);
    $isOptionGroup = in_array($typeId, [2, 3], true) && $children->isNotEmpty();
    $selectedOptions = old("selected_options.{$question->id}");
    $normalizedQuestion = str($question->value)->lower();
    $isAccessSection = $children->isNotEmpty()
        && ($normalizedQuestion->contains('accesos') || $normalizedQuestion->contains('dominio') || $normalizedQuestion->contains('hosting'));
@endphp

@if($isAccessSection)
    <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-4">
        <div>
            <h3 class="font-semibold text-gray-900 whitespace-pre-line">{{ $question->value }}</h3>
            <p class="text-sm text-gray-500">Agrega los accesos necesarios. Se guardarán como logins del proyecto.</p>
        </div>
        <div class="space-y-3">
            @for($index = 0; $index < 3; $index++)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="text" name="access_logins[{{ $index }}][name]" value="{{ old("access_logins.{$index}.name") }}" class="w-full border rounded px-3 py-2" placeholder="Nombre">
                    <input type="text" name="access_logins[{{ $index }}][user]" value="{{ old("access_logins.{$index}.user") }}" class="w-full border rounded px-3 py-2" placeholder="Usuario">
                    <input type="text" name="access_logins[{{ $index }}][password]" value="{{ old("access_logins.{$index}.password") }}" class="w-full border rounded px-3 py-2" placeholder="Contraseña">
                    <input type="url" name="access_logins[{{ $index }}][url]" value="{{ old("access_logins.{$index}.url") }}" class="w-full border rounded px-3 py-2" placeholder="URL">
                </div>
            @endfor
        </div>
    </div>
@elseif($isOptionGroup)
    <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-3">
        <div>
            <h3 class="font-semibold text-gray-900 whitespace-pre-line">{{ $question->value }}</h3>
            <p class="text-sm text-gray-500">{{ $typeId === 2 ? 'Selecciona una opción.' : 'Selecciona una o varias opciones.' }}</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($children as $option)
                @php
                    $isChecked = is_array($selectedOptions)
                        ? in_array((string) $option->id, array_map('strval', $selectedOptions), true)
                        : $answers->has($option->id);
                @endphp
                <label class="flex items-center gap-3 border rounded-lg px-3 py-2 hover:border-indigo-200">
                    <input
                        type="{{ $typeId === 2 ? 'radio' : 'checkbox' }}"
                        name="selected_options[{{ $question->id }}][]"
                        value="{{ $option->id }}"
                        class="rounded text-indigo-600"
                        @checked($isChecked)
                    >
                    <span class="text-sm text-gray-800">{{ $option->value }}</span>
                </label>
            @endforeach
        </div>
    </div>
@elseif($children->isNotEmpty())
    <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-5">
        <div>
            <h3 class="font-semibold text-gray-900 whitespace-pre-line">{{ $question->value }}</h3>
        </div>
        <div class="space-y-5">
            @foreach($children as $child)
                @include('project_briefs.partials.question', [
                    'question' => $child,
                    'answers' => $answers,
                    'level' => $level + 1,
                    'parentTypeId' => $typeId ?: ($parentTypeId ?? null),
                ])
            @endforeach
        </div>
    </div>
@else
    @php
        $answerValue = old("answers.{$question->id}", $answers->get($question->id, ''));
    @endphp
    <div class="{{ $level > 0 ? 'border-t border-gray-100 pt-4 first:border-t-0 first:pt-0' : 'bg-white shadow-sm rounded-lg border border-gray-100 p-6' }}">
        <label class="block text-sm font-medium text-gray-800 whitespace-pre-line">{{ $question->value }}</label>
        @if($effectiveTypeId === 1)
            <input type="text" name="answers[{{ $question->id }}]" value="{{ $answerValue }}" class="mt-2 w-full border rounded px-3 py-2">
        @elseif($effectiveTypeId === 5)
            @php
                $fileAnswer = is_string($answerValue) ? json_decode($answerValue, true) : null;
            @endphp
            <input type="file" name="files[{{ $question->id }}]" class="mt-2 w-full border rounded px-3 py-2 bg-white">
            @if(is_array($fileAnswer) && isset($fileAnswer['path']))
                <p class="mt-2 text-xs text-gray-600">
                    Actual:
                    <a href="{{ asset('storage/'.$fileAnswer['path']) }}" target="_blank" rel="noreferrer" class="text-indigo-600 hover:underline">
                        {{ $fileAnswer['name'] ?? 'Ver archivo' }}
                    </a>
                </p>
            @endif
        @else
            <textarea name="answers[{{ $question->id }}]" rows="3" class="mt-2 w-full border rounded px-3 py-2">{{ $answerValue }}</textarea>
        @endif
    </div>
@endif
