<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAccessLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'admin_user_id',
        'target_landlord_id',
        'tenant_id',
        'verification_request_id',
        'document_id',
        'document_type',
        'access_type',
        'reason',
        'ip_address',
        'user_agent',
        'presigned_url_expires_at',
        'prev_hash',
        'row_hash',
    ];

    protected $casts = [
        'presigned_url_expires_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function targetLandlord()
    {
        return $this->belongsTo(User::class, 'target_landlord_id');
    }

    public function document()
    {
        return $this->belongsTo(LandlordVerificationDocument::class, 'document_id');
    }
}
