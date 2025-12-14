<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Tareas</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear tarea</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @include('tasks.form', [
                    'task' => $task,
                    'statuses' => $statuses,
                    'projects' => $projects,
                    'users' => $users,
                    'parentTypes' => $parentTypes,
                    'subTypes' => $subTypes,
                    'defaultStatusId' => $defaultStatusId,
                    'submit' => $submit
                ])
            </form>
        </div>
    </div>
</x-app-layout>
