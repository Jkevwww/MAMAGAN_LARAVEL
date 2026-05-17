<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'booking_id',
        'reference_number',
        'qr_payload',
        'issued_at',
        'checked_in_at',
        'checked_in_by',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }
}
