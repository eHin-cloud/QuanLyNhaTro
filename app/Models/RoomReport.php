<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'reporter_name',
        'reporter_phone',
        'reason',
        'description',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
