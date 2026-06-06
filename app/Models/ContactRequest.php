<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        'phone',
        'message',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
