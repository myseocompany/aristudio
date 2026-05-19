<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Concerns;

use App\Models\Task;

trait SerializesTasks
{
    /**
     * @return array<string, mixed>
     */
    private function taskPayload(Task $task): array
    {
        return [
            'id' => $task->id,
            'name' => $task->name,
            'status_id' => $task->status_id,
            'project_id' => $task->project_id,
            'user_id' => $task->user_id,
            'creator_user_id' => $task->creator_user_id,
            'updator_user_id' => $task->updator_user_id,
            'due_date' => $task->due_date?->toDateTimeString(),
            'delivery_date' => $task->delivery_date?->toDateString(),
            'priority' => $task->priority,
            'points' => $task->points,
            'value_generated' => $task->value_generated,
            'not_billing' => $task->not_billing,
            'created_at' => $task->created_at?->toDateTimeString(),
            'updated_at' => $task->updated_at?->toDateTimeString(),
        ];
    }
}
