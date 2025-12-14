<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_users', 'user_id', 'project_id')->withTimestamps();
    }
}
