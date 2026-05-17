<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'cottage_id',
        'check_in',
        'check_out',
        'total_price',
        'payment_method',
        'payment_proof',
        'paymongo_link_id',
        'paymongo_payment_id',
        'payment_status',
        'status',
        'admin_notes',
    ];

    /**
     * Get the user that owns the reservation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cottage associated with the reservation.
     */
    public function cottage()
    {
        return $this->belongsTo(Cottage::class);
    }
}
