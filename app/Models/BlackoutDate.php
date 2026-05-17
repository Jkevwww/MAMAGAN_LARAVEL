<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlackoutDate extends Model
{
    protected $fillable = ['facility_id', 'title', 'starts_at', 'ends_at', 'reason'];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
