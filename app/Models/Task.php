<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'copy',
        'caption',
        'project_id',
        'user_id',
        'status_id',
        'parent_id',
        'delivery_date',
        'due_date',
        'priority',
        'points',
        'estimated_points',
        'type_id',
        'sub_type_id',
        'creator_user_id',
        'updator_user_id',
        'value_generated',
        'not_billing',
        'url_finished',
        'file_url',
        'referrer',
    ];

    protected $casts = [
        'value_generated' => 'boolean',
        'not_billing' => 'boolean',
        'due_date' => 'datetime',
        'delivery_date' => 'date',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TaskType::class, 'type_id');
    }

    public function subType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class, 'sub_type_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }
}
