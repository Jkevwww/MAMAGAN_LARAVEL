<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'facility_id',
        'promotion_id',
        'booking_date',
        'start_time',
        'end_time',
        'quantity',
        'guest_count',
        'booking_type',
        'base_amount',
        'discount_amount',
        'total_amount',
        'booking_status',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'base_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
}
