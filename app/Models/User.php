<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'tenant_id',
        'role_id',
        'name',
        'username',
        'phone',
        'email',
        'password',
        'like',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function roleRecord(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function roleSlug(): ?string
    {
        $roleRecord = $this->relationLoaded('roleRecord')
            ? $this->getRelation('roleRecord')
            : ($this->role_id ? $this->roleRecord : null);

        if ($roleRecord) {
            return $roleRecord->slug;
        }

        return match ($this->role) {
            'admin' => 'landlord',
            'user' => 'resident',
            default => $this->role,
        };
    }

    public function roleName(): string
    {
        $roleRecord = $this->relationLoaded('roleRecord')
            ? $this->getRelation('roleRecord')
            : ($this->role_id ? $this->roleRecord : null);

        if ($roleRecord) {
            return $roleRecord->name;
        }

        return match ($this->role) {
            'admin' => 'Quản trị viên',
            'user' => 'Người thuê',
            default => 'Quản trị viên',
        };
    }

    public function resident(): HasOne
    {
        return $this->hasOne(Resident::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Role Helper Methods
    public function roleSlug(): ?string
    {
        if ($this->role_id) {
            $role = $this->relationLoaded('role') ? $this->getRelation('role') : $this->role()->first();

            if ($role) {
                return $role->slug;
            }
        }

        return match ($this->getAttribute('role')) {
            'admin' => 'landlord',
            'user' => 'resident',
            default => $this->getAttribute('role'),
        };
    }

    public function roleName(): string
    {
        if ($this->role_id) {
            $role = $this->relationLoaded('role') ? $this->getRelation('role') : $this->role()->first();

            if ($role) {
                return $role->name;
            }
        }

        return match ($this->getAttribute('role')) {
            'admin' => 'Quản trị viên',
            'user' => 'Người thuê',
            default => 'Quản trị viên',
        };
    }

    public function isLandlord(): bool
    {
        return $this->roleSlug() === 'landlord';
    }

    public function isResident(): bool
    {
        return $this->roleSlug() === 'resident';
    }

    public function isGuest(): bool
    {
        return $this->roleSlug() === 'guest';
    }
}
