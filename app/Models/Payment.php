<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'method',
        'amount',
        'status',
        'reference_number',
        'paymongo_checkout_id',
        'paymongo_payment_id',
        'checkout_url',
        'provider_payload',
        'proof_path',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'provider_payload' => 'array',
        'verified_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
