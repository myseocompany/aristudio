<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;

    protected $table = 'task_statuses';

    protected $fillable = [
        'name',
        'pending',
        'description',
        'alias',
        'color',
        'stage_id',
        'background_color',
        'weight',
        'status_id',
        'show_in_report',
    ];

    public function scopeActive($query)
    {
        return $query->where('status_id', 1);
    }
}
