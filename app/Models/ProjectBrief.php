<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProjectBrief extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'created_by',
        'public_token',
        'title',
        'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProjectBrief $brief): void {
            if (! $brief->public_token) {
                $brief->public_token = Str::random(48);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ProjectBriefAnswer::class);
    }
}
