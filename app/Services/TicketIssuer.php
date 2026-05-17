<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Support\Str;

class TicketIssuer
{
    public function issue(Booking $booking): Ticket
    {
        $booking->loadMissing(['user', 'facility']);

        return Ticket::firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'reference_number' => 'MAM-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'qr_payload' => json_encode([
                    'booking_id' => $booking->id,
                    'tourist' => $booking->user->email,
                    'facility' => $booking->facility->name,
                    'signature' => hash_hmac('sha256', $booking->id.'|'.$booking->user_id, config('app.key')),
                ]),
                'issued_at' => now(),
            ]
        );
    }
}
