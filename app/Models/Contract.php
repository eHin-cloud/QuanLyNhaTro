<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    protected $fillable = [
        'tenant_id',
        'room_id',
        'resident_id',
        'contract_code',
        'start_date',
        'end_date',
        'deposit',
        'status',
        'terms',
        'signature',
        'lessor_signature'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }
}
