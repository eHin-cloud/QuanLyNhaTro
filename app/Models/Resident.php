<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'name', 'phone', 'email', 'start_date', 'status'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
