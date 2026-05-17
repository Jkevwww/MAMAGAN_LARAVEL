<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = [
        'name',
        'category',
        'description',
        'image',
        'price_min',
        'price_max',
        'capacity',
        'inventory_count',
        'rental_type',
        'is_active',
        'is_bookable',
    ];

    protected $casts = [
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
        'is_active' => 'boolean',
        'is_bookable' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(FacilityReview::class);
    }

    public function seasonalRates()
    {
        return $this->hasMany(SeasonalRate::class);
    }

    public function averageRating(): float
    {
        return round((float) $this->reviews()->avg('rating'), 1);
    }
}
