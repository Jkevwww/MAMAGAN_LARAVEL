<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class PaymongoCheckout
{
    public function configured(): bool
    {
        return filled(config('services.paymongo.secret_key'));
    }

    public function createForBooking(Booking $booking): array
    {
        if (! $this->configured()) {
            throw new \RuntimeException('PayMongo secret key is not configured.');
        }

        $booking->loadMissing(['facility', 'user']);
        $amount = (int) round(((float) $booking->total_amount) * 100);

        $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
            ->acceptJson()
            ->asJson()
            ->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'billing' => [
                            'name' => $booking->user->name,
                            'email' => $booking->user->email,
                        ],
                        'description' => 'Mamagan booking #'.$booking->id,
                        'line_items' => [
                            [
                                'name' => $booking->facility->name,
                                'amount' => $amount,
                                'currency' => 'PHP',
                                'quantity' => 1,
                            ],
                        ],
                        'payment_method_types' => ['gcash', 'paymaya', 'card'],
                        'reference_number' => 'MAM-BOOKING-'.$booking->id,
                        'send_email_receipt' => true,
                        'show_description' => true,
                        'show_line_items' => true,
                        'success_url' => route('bookings.show', ['booking' => $booking, 'payment' => 'success']),
                        'cancel_url' => route('bookings.show', ['booking' => $booking, 'payment' => 'cancelled']),
                        'metadata' => [
                            'booking_id' => (string) $booking->id,
                            'user_id' => (string) $booking->user_id,
                            'facility_id' => (string) $booking->facility_id,
                        ],
                    ],
                ],
            ]);

        $response->throw();

        $payload = $response->json();
        $attributes = data_get($payload, 'data.attributes', []);

        return [
            'id' => data_get($payload, 'data.id'),
            'checkout_url' => data_get($attributes, 'checkout_url'),
            'payload' => $payload,
        ];
    }

    public function syncPaidPayment(Booking $booking, TicketIssuer $ticketIssuer): bool
    {
        if (! $this->configured() || blank($booking->payment?->paymongo_checkout_id)) {
            return false;
        }

        $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
            ->acceptJson()
            ->get('https://api.paymongo.com/v1/checkout_sessions/'.$booking->payment->paymongo_checkout_id);

        if (! $response->ok()) {
            return false;
        }

        $payload = $response->json();
        $attributes = data_get($payload, 'data.attributes', []);
        $paidPayment = collect(data_get($attributes, 'payments', []))
            ->first(fn ($payment) => data_get($payment, 'attributes.status') === 'paid');

        if (! $paidPayment) {
            return false;
        }

        DB::transaction(function () use ($booking, $ticketIssuer, $payload, $attributes, $paidPayment) {
            $amount = data_get($paidPayment, 'attributes.amount') ?? data_get($attributes, 'amount');

            $booking->update([
                'booking_status' => 'approved',
                'payment_status' => 'paid',
            ]);

            $booking->payment()->updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'method' => data_get($attributes, 'payment_method_used', 'paymongo'),
                    'amount' => $amount ? ((float) $amount / 100) : $booking->total_amount,
                    'status' => 'paid',
                    'reference_number' => data_get($attributes, 'reference_number', $booking->payment?->reference_number),
                    'paymongo_checkout_id' => data_get($payload, 'data.id', $booking->payment?->paymongo_checkout_id),
                    'paymongo_payment_id' => data_get($paidPayment, 'id'),
                    'provider_payload' => $payload,
                    'verified_at' => now(),
                ]
            );

            $ticketIssuer->issue($booking->fresh(['user', 'facility']));
        });

        return true;
    }
}
