<?php

namespace App\Models;

use App\Casts\Aes256GcmEncrypted;
use App\Models\Concerns\MasksSensitiveAttributes;
use App\Support\SensitiveData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandlordVerificationRequest extends Model
{
    use MasksSensitiveAttributes;

    protected array $sensitiveMaskedAttributes = [
        'cccd_number' => 'national_id',
    ];

    protected $fillable = [
        'tenant_id',
        'user_id',
        'type',
        'cccd_number',
        'admin_review_consent_given',
        'admin_review_consent_at',
        'admin_review_consent_ip',
        'default_document_access_revoked_at',
        'status',
        'reviewed_by',
        'reviewed_at',
        'reject_reason',
    ];

    protected $casts = [
        'cccd_number' => Aes256GcmEncrypted::class,
        'admin_review_consent_given' => 'boolean',
        'admin_review_consent_at' => 'datetime',
        'default_document_access_revoked_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $verification): void {
            if ($verification->isDirty('cccd_number')) {
                $verification->cccd_number_blind_index = SensitiveData::blindIndex($verification->cccd_number);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LandlordVerificationDocument::class, 'verification_request_id');
    }

    public function allowsDefaultAdminDocumentReview(): bool
    {
        return $this->admin_review_consent_given
            && $this->default_document_access_revoked_at === null
            && in_array($this->status, ['pending', 'pending_landlord'], true);
    }

    public function revokeDefaultAdminDocumentReview(): void
    {
        $this->forceFill([
            'default_document_access_revoked_at' => now(),
        ])->save();
    }
}
