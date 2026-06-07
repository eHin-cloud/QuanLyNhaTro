<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityRecord extends Model
{
    protected $fillable = [
        'tenant_id',
        'room_id',
        'billing_month',
        'old_electricity',
        'new_electricity',
        'old_water',
        'new_water',
        'electricity_price',
        'water_price',
        'status',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
