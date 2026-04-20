<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $brief->title }} - {{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="py-8">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                    <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-2">
                        <p class="text-sm text-gray-500">{{ $project?->name ?? 'Proyecto' }}</p>
                        <h1 class="text-2xl font-semibold text-gray-900">{{ $brief->title }}</h1>
                        <p class="text-sm text-gray-600">Diligencia la información solicitada y guarda tus respuestas.</p>
                    </div>

                    @if (session('status'))
                        <div class="text-green-700 bg-green-100 border border-green-200 px-4 py-3 rounded">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="p-3 rounded bg-red-100 text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('public.briefs.update', $brief->public_token) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="title" value="{{ $brief->title }}">
                        <input type="hidden" name="notes" value="{{ $brief->notes }}">

                        @forelse($questions as $question)
                            @include('project_briefs.partials.question', [
                                'question' => $question,
                                'answers' => $answers,
                                'level' => 0,
                            ])
                        @empty
                            <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6">
                                <p class="text-sm text-gray-500">No hay preguntas configuradas para este brief.</p>
                            </div>
                        @endforelse

                        <div class="pt-2">
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded shadow hover:bg-indigo-500">Guardar respuestas</button>
                        </div>
                    </form>
                </div>
            </div>

            <x-app-version-footer />
        </div>
    </body>
</html>
