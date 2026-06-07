<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomEquipment extends Model
{
    protected $table = 'room_equipment';

    protected $fillable = [
        'tenant_id',
        'room_id',
        'equipment_id',
        'quantity',
        'last_allocated_at',
        'last_recovered_at',
    ];

    protected $casts = [
        'last_allocated_at' => 'datetime',
        'last_recovered_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}
