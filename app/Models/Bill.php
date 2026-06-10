<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bill extends Model
{
    protected $fillable = [
        'tenant_id',
        'room_id',
        'electric_water_log_id',
        'billing_month',
        'room_price',
        'electricity_usage',
        'electricity_cost',
        'water_usage',
        'water_cost',
        'service_cost',
        'total_amount',
        'status',
        'payment_date',
        'vietqr_url'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function electricWaterLog(): BelongsTo
    {
        return $this->belongsTo(ElectricWaterLog::class);
    }
}
