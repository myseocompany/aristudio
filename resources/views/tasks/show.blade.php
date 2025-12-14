<x-app-layout>
    <div class="fixed inset-0 bg-gray-900/50"></div>
    <div class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-xl border-l border-gray-200 flex flex-col z-30">
        @include('tasks.partials.show_panel', ['task' => $task])
    </div>
</x-app-layout>
