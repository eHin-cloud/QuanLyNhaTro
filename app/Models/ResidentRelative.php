<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResidentRelative extends Model
{
    protected $table = 'resident_relatives';

    protected $fillable = [
        'resident_id',
        'name',
        'dob',
        'cccd',
        'phone',
        'hometown',
        'relationship',
        'temporary_residence_status',
        'start_date',
        'end_date',
        'version'
    ];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }
}
