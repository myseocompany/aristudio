<li data-id="{{ $taskType->id }}" class="task-item">
    @if ($taskType->children->isNotEmpty())
        <span class="expand-toggle"></span>
    @endif
    
    <!-- Contenedor de fila para todos los campos -->
    <div class="task-fields">
        <!-- ID -->
        <span class="task-id">ID: {{ $taskType->id }}</span>

        <!-- Campo de edición del nombre -->
        <input type="text" class="editable-name" value="{{ $taskType->name }}" data-id="{{ $taskType->id }}" placeholder="Nombre" />

        <!-- Campo de edición del Parent ID -->
        <input type="number" class="editable-parent-id" value="{{ $taskType->parent_id }}" data-id="{{ $taskType->id }}" placeholder="Parent ID" />

        <!-- Campo de edición del Orden -->
        <input type="number" class="editable-order" value="{{ $taskType->order ?? '' }}" data-id="{{ $taskType->id }}" placeholder="Order" />
    </div>

    <!-- Mostrar los hijos si existen -->
    @if ($taskType->children->isNotEmpty())
        <ul class="sub-tasks level-{{ $level }}">
            @foreach ($taskType->children as $child)
                @include('partials.task_item', ['taskType' => $child, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
