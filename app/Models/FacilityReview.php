<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityReview extends Model
{
    protected $fillable = ['user_id', 'facility_id', 'booking_id', 'rating', 'comment'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function media()
    {
        return $this->hasMany(FacilityReviewMedia::class);
    }
}
