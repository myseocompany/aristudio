@if ($errors->any())
    <div class="p-3 rounded bg-red-100 text-red-700">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Título</label>
            <input type="text" name="title" value="{{ old('title', $brief->title ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Proyecto</label>
            <input type="text" value="{{ $project->name }}" class="mt-1 w-full border rounded px-3 py-2 bg-gray-50 text-gray-600" disabled>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Notas internas</label>
            <textarea name="notes" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('notes', $brief->notes ?? '') }}</textarea>
        </div>
    </div>
</div>

@forelse($questions as $question)
    @include('project_briefs.partials.question', [
        'question' => $question,
        'answers' => $answers,
        'level' => 0,
    ])
@empty
    <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6">
        <p class="text-sm text-gray-500">No hay preguntas configuradas para el brief.</p>
    </div>
@endforelse

<div class="pt-2 flex gap-3">
    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded shadow hover:bg-indigo-500">{{ $submit }}</button>
    <a href="{{ route('projects.briefs.index', $project) }}" class="px-5 py-2.5 border rounded text-gray-700 hover:bg-gray-50">Cancelar</a>
</div>
