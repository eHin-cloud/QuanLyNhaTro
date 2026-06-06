<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'billing_month',
        'old_electricity',
        'new_electricity',
        'old_water',
        'new_water',
        'electricity_price',
        'water_price',
        'status'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
