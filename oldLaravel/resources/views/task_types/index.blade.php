@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Organizar Task Types</h2>
    <ul id="taskTypeList" class="sortable-list">
        @foreach ($taskTypes as $taskType)
            @include('partials.task_item', ['taskType' => $taskType, 'level' => 1])
        @endforeach
    </ul>
    <button id="saveOrder" class="btn btn-primary">Guardar Orden</button>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
    $(document).ready(function () {
    console.log("ready");

    // Evento de cambio de nombre (blur)
    $(document).on('blur', '.editable-name', function () {
        const taskId = $(this).data('id');
        const newName = $(this).val();

        $.ajax({
            url: '{{ route("taskTypes.updateName") }}',
            type: 'POST',
            data: {
                id: taskId,
                name: newName,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                console.log(response.status);
            },
            error: function (error) {
                console.log(error);
                alert('Error al actualizar el nombre');
            }
        });
    });

    // Evento de cambio de Parent ID (blur)
    $(document).on('blur', '.editable-parent-id', function () {
        const taskId = $(this).data('id');
        const newParentId = $(this).val() || null;

        $.ajax({
            url: '{{ route("taskTypes.updateParent") }}',
            type: 'POST',
            data: {
                id: taskId,
                parent_id: newParentId,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                console.log(response.status);
            },
            error: function (error) {
                console.log(error);
                alert('Error al actualizar el Parent ID');
            }
        });
    });

    // Evento de cambio de Orden (blur)
    $(document).on('blur', '.editable-order', function () {
        const taskId = $(this).data('id');
        const newOrder = $(this).val();

        $.ajax({
            url: '{{ route("taskTypes.updateOrder") }}',
            type: 'POST',
            data: {
                id: taskId,
                order: newOrder,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                console.log(response.status);
            },
            error: function (error) {
                console.log(error);
                alert('Error al actualizar el orden');
            }
        });
    });
});

</script>

<style>

#taskTypeList, .sub-tasks {
    list-style: none;
    padding: 0;
}

.task-item {
    padding: 5px;
    border: 1px solid #ccc;
    margin-bottom: 5px;
    cursor: move;
    background-color: #f9f9f9;
    position: relative;
}

.task-fields {
    display: flex;
    align-items: center;
    gap: 10px;
}

.task-id {
    background-color: #e1f5fe;
    padding: 5px;
    border-radius: 4px;
    font-weight: bold;
}

.editable-name {
    background-color: #e8f5e9;
    padding: 5px;
    border-radius: 4px;
    border: 1px solid #ccc;
    width: 200px;
    box-sizing: border-box;
}

.editable-parent-id {
    background-color: #fff3e0;
    padding: 5px;
    border-radius: 4px;
    border: 1px solid #ccc;
    width: 100px;
    box-sizing: border-box;
}

.editable-order {
    background-color: #fce4ec;
    padding: 5px;
    border-radius: 4px;
    border: 1px solid #ccc;
    width: 80px;
    box-sizing: border-box;
}

.expand-toggle {
    cursor: pointer;
    font-weight: bold;
    margin-right: 5px;
}

.expand-toggle::before {
    content: '+ ';
}

.expand-toggle.expanded::before {
    content: '- ';
}

.ui-state-highlight {
    background-color: #f1f1f1;
    height: 40px;
}

.sub-tasks {
    margin-left: 20px;
}

.sub-tasks.level-3, .sub-tasks.level-4, .sub-tasks.level-5 {
    display: none; /* Oculta niveles profundos */
}

</style>

@endsection
