<?php

namespace App\Models;

use App\Casts\Aes256GcmEncrypted;
use App\Models\Concerns\MasksSensitiveAttributes;
use App\Support\SensitiveData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, MasksSensitiveAttributes, Notifiable;

    protected array $sensitiveMaskedAttributes = [
        'phone' => 'phone',
    ];

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
            'phone' => Aes256GcmEncrypted::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if ($user->isDirty('phone')) {
                $user->phone_blind_index = SensitiveData::blindIndex($user->phone);
            }
        });
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
            'admin' => 'admin',
            'manager', 'staff' => 'manager',
            'user' => 'resident',
            'guest' => 'guest',
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

        return match ($this->roleSlug()) {
            'admin' => 'Admin he thong',
            'unverified_landlord' => 'Chu tro chua xac minh',
            'landlord' => 'Chu tro',
            'manager' => 'Nhan vien quan ly',
            'resident' => 'Cu dan',
            'guest' => 'Khach xem phong',
            default => 'Nguoi dung',
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

    public function isLandlord(): bool
    {
        return $this->roleSlug() === 'landlord';
    }

    public function isUnverifiedLandlord(): bool
    {
        return $this->roleSlug() === 'unverified_landlord';
    }

    public function canAccessLandlordDashboard(): bool
    {
        return in_array($this->roleSlug(), ['landlord', 'unverified_landlord', 'manager'], true);
    }

    public function isAdmin(): bool
    {
        return $this->roleSlug() === 'admin';
    }

    public function isManager(): bool
    {
        return $this->roleSlug() === 'manager';
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
