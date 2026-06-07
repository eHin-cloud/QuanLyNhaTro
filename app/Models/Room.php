<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'building_id',
        'tenant_id',
        'room_number',
        'floor',
        'status',
        'room_type',
        'price',
        'area',
        'amenities',
        'description',
        'image',
        'images',
        'video',
        'version'
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function electricWaterLogs(): HasMany
    {
        return $this->hasMany(ElectricWaterLog::class);
    }

    public function utilityRecords(): HasMany
    {
        return $this->hasMany(UtilityRecord::class)->orderBy('billing_month', 'desc');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function contactRequests()
    {
        return $this->hasMany(ContactRequest::class);
    }

    public function equipmentAllocations(): HasMany
    {
        return $this->hasMany(RoomEquipment::class);
    }

    public function activeResidents(): HasMany
    {
        return $this->hasMany(Resident::class)->where('status', 'active');
    }

    public function syncOccupancyStatus(): void
    {
        if ($this->status === 'maintenance') {
            return;
        }

        $hasActiveResidents = $this->activeResidents()->exists();

        if (!$hasActiveResidents) {
            $this->update(['status' => 'empty']);
            return;
        }

        if ($this->status === 'empty') {
            $this->update(['status' => 'occupied']);
        }
    }

    public static function syncOccupancyStatusById($roomId): void
    {
        if (!$roomId) {
            return;
        }

        $room = self::find($roomId);
        if ($room) {
            $room->syncOccupancyStatus();
        }
    }
}
