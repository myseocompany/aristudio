<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'name',
        'description',
        'weight',
        'budget',
        'start_date',
        'finish_date',
        'weekly_pieces',
        'ads_budget',
        'status_id',
        'lead_target',
        'monthly_points_goal',
        'sales',
        'color',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users', 'project_id', 'user_id')->withTimestamps();
    }

    public function logins(): HasMany
    {
        return $this->hasMany(ProjectLogin::class);
    }
}
