<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'daily_goal',
        'status_id',
        'document',
        'address',
        'birth_date',
        'hourly_rate',
        'color',
        'availability',
        'enterprise_id',
        'facebook_id',
        'phone',
        'image_url',
        'position',
        'entry_date',
        'termination_date',
        'contracted_hours',
        'contract_type',
        'blood_type',
        'last_login',
        'arl',
        'eps',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'entry_date' => 'date',
            'termination_date' => 'date',
            'last_login' => 'datetime',
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_users', 'user_id', 'project_id')->withTimestamps();
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasModulePermission(string $slug, string $ability): bool
    {
        if (! $this->role_id) {
            return false;
        }

        if ($this->isAdmin()) {
            return true;
        }

        $column = match ($ability) {
            'create' => 'created',
            'read' => 'readed',
            'update' => 'updated',
            'delete' => 'deleted',
            'list' => 'list',
            default => null,
        };

        if (! $column) {
            return false;
        }

        $moduleId = $this->resolveModuleId($slug);
        if (! $moduleId) {
            return false;
        }

        return DB::table('role_modules')
            ->where('role_id', $this->role_id)
            ->where('module_id', $moduleId)
            ->where($column, 1)
            ->exists();
    }

    public function isAdmin(): bool
    {
        if (! $this->role_id) {
            return false;
        }

        if ($this->role_id === 1) {
            return true;
        }

        $roleName = $this->relationLoaded('role') ? $this->role?->name : null;
        if (! $roleName) {
            $roleName = DB::table('roles')
                ->where('id', $this->role_id)
                ->value('name');
        }

        if (! $roleName) {
            return false;
        }

        $normalized = strtolower($roleName);

        return in_array($normalized, ['admin', 'administrador', 'super admin', 'superadmin', 'super-admin'], true);
    }

    private function resolveModuleId(string $slug): ?int
    {
        $normalized = '/'.ltrim($slug, '/');
        $candidates = [$normalized, ltrim($normalized, '/')];

        return DB::table('modules')
            ->whereIn('slug', $candidates)
            ->value('id');
    }
}
