<?php

namespace App\Models;

use App\Casts\Aes256GcmEncrypted;
use App\Models\Concerns\MasksSensitiveAttributes;
use App\Support\SensitiveData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use MasksSensitiveAttributes;

    protected array $sensitiveMaskedAttributes = [
        'phone' => 'phone',
        'bank_account_no' => 'bank',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'bank_name',
        'bank_account_no',
        'bank_account_name',
        'verification_status',
        'onboarding_step',
        'listing_badge',
        'boost_score',
        'verified_at',
        'kyc_verified_at',
        'premium_verified_at',
    ];

    protected $casts = [
        'phone' => Aes256GcmEncrypted::class,
        'bank_account_no' => Aes256GcmEncrypted::class,
        'verified_at' => 'datetime',
        'kyc_verified_at' => 'datetime',
        'premium_verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $tenant): void {
            if ($tenant->isDirty('phone')) {
                $tenant->phone_blind_index = SensitiveData::blindIndex($tenant->phone);
            }

            if ($tenant->isDirty('bank_account_no')) {
                $tenant->bank_account_no_blind_index = SensitiveData::blindIndex($tenant->bank_account_no);
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function electricWaterLogs(): HasMany
    {
        return $this->hasMany(ElectricWaterLog::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
