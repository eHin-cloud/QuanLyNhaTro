<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'resident_id',
        'contract_code',
        'start_date',
        'end_date',
        'deposit',
        'status',
        'terms',
        'signature',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }
}
