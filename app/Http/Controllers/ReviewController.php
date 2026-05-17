<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilityReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Facility $facility)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'media.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,mp4,mov,webm', 'max:10240'],
        ]);

        $booking = Booking::where('user_id', Auth::id())
            ->where('facility_id', $facility->id)
            ->whereIn('booking_status', ['approved', 'checked_in'])
            ->where('payment_status', 'paid')
            ->latest()
            ->first();

        abort_unless($booking, 403, 'Only paid customers may review this facility.');

        $review = FacilityReview::create([
            'user_id' => Auth::id(),
            'facility_id' => $facility->id,
            'booking_id' => $booking->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        foreach ($request->file('media', []) as $file) {
            $review->media()->create([
                'path' => $file->store('review-media', 'public'),
                'media_type' => str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image',
            ]);
        }

        return back()->with('success', 'Review submitted.');
    }
}
