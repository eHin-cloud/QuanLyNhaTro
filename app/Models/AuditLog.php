<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'actor_user_id',
        'action',
        'resource_type',
        'resource_id',
        'sensitive_fields',
        'reason',
        'ip_address',
        'user_agent',
        'request_id',
        'prev_hash',
        'row_hash',
        'metadata',
    ];

    protected $casts = [
        'sensitive_fields' => 'array',
        'metadata' => 'array',
    ];
}
