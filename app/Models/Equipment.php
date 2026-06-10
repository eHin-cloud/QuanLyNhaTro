<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'unit',
        'total_quantity',
        'allocated_quantity',
        'description',
        'version',
    ];

    protected $appends = [
        'stock_quantity',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function roomAllocations(): HasMany
    {
        return $this->hasMany(RoomEquipment::class);
    }

    public function getStockQuantityAttribute(): int
    {
        return max(0, (int) $this->total_quantity - (int) $this->allocated_quantity);
    }
}
