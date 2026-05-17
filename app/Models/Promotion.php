<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'code',
        'discount_type',
        'discount_value',
        'minimum_amount',
        'starts_at',
        'ends_at',
        'usage_limit',
        'used_count',
        'facility_id',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function isUsableFor(Facility $facility, float $amount): bool
    {
        $today = now()->toDateString();

        return $this->is_active
            && (! $this->starts_at || $this->starts_at->toDateString() <= $today)
            && (! $this->ends_at || $this->ends_at->toDateString() >= $today)
            && (! $this->usage_limit || $this->used_count < $this->usage_limit)
            && (! $this->facility_id || $this->facility_id === $facility->id)
            && $amount >= (float) $this->minimum_amount;
    }

    public function discountFor(float $amount): float
    {
        if ($this->discount_type === 'percent') {
            return min($amount, $amount * ((float) $this->discount_value / 100));
        }

        return min($amount, (float) $this->discount_value);
    }
}
