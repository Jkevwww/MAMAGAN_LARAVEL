<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityReviewMedia extends Model
{
    protected $fillable = ['facility_review_id', 'path', 'media_type'];

    public function review()
    {
        return $this->belongsTo(FacilityReview::class, 'facility_review_id');
    }
}
