<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cottage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'capacity',
        'image',
        'is_available',
    ];

    /**
     * Get the reservations for the cottage.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
