<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectBriefAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_meta_data_id',
        'value',
    ];

    public function brief(): BelongsTo
    {
        return $this->belongsTo(ProjectBrief::class, 'project_brief_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ProjectMetaData::class, 'project_meta_data_id');
    }
}
