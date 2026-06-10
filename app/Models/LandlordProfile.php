<?php

namespace App\Models;

use App\Casts\Aes256GcmEncrypted;
use App\Models\Concerns\MasksSensitiveAttributes;
use App\Support\SensitiveData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordProfile extends Model
{
    use MasksSensitiveAttributes;

    protected array $sensitiveMaskedAttributes = [
        'phone' => 'phone',
    ];

    protected $fillable = [
        'user_id',
        'tenant_id',
        'full_name',
        'phone',
        'property_name',
        'property_address',
        'status',
    ];

    protected $casts = [
        'phone' => Aes256GcmEncrypted::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (self $profile): void {
            if ($profile->isDirty('phone')) {
                $profile->phone_blind_index = SensitiveData::blindIndex($profile->phone);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
