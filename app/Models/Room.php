<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_number', 'floor', 'status', 'price'];

    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    public function utilityRecords()
    {
        return $this->hasMany(UtilityRecord::class);
    }
}
