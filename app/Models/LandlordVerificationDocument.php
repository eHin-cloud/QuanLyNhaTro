<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordVerificationDocument extends Model
{
    protected $fillable = [
        'verification_request_id',
        'document_type',
        'disk',
        'file_path',
        'original_filename',
        'mime_type',
        'size_bytes',
        'sha256_checksum',
        'status',
        'notes',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(LandlordVerificationRequest::class, 'verification_request_id');
    }
}
