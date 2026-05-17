<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\SystemLog;
use App\Services\TicketIssuer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PaymongoWebhookController extends Controller
{
    public function __invoke(Request $request, TicketIssuer $ticketIssuer)
    {
        if (! $this->hasValidSignature($request)) {
            return response()->json(['message' => 'Invalid webhook signature.'], Response::HTTP_UNAUTHORIZED);
        }

        $payload = $request->json()->all();
        $eventType = Arr::get($payload, 'data.attributes.type');
        $paymentData = Arr::get($payload, 'data.attributes.data', []);
        $paymentAttributes = Arr::get($paymentData, 'attributes', []);

        if (! in_array($eventType, ['payment.paid', 'payment_intent.succeeded', 'source.chargeable'], true)) {
            $this->log('paymongo.webhook_ignored', null, ['event_type' => $eventType]);

            return response()->json(['message' => 'Event ignored.']);
        }

        $booking = $this->resolveBooking($paymentData, $paymentAttributes);

        if (! $booking) {
            $this->log('paymongo.webhook_unmatched', null, [
                'event_type' => $eventType,
                'payment_id' => Arr::get($paymentData, 'id'),
                'reference_number' => Arr::get($paymentAttributes, 'reference_number'),
            ]);

            return response()->json(['message' => 'No matching booking found.'], Response::HTTP_ACCEPTED);
        }

        DB::transaction(function () use ($booking, $paymentData, $paymentAttributes, $ticketIssuer, $eventType) {
            $amount = Arr::get($paymentAttributes, 'amount');

            $booking->update([
                'booking_status' => 'approved',
                'payment_status' => 'paid',
            ]);

            $booking->payment()->updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'method' => Arr::get($paymentAttributes, 'source.type', Arr::get($paymentAttributes, 'payment_method.type', 'paymongo')),
                    'amount' => $amount ? ((float) $amount / 100) : $booking->total_amount,
                    'status' => 'paid',
                    'reference_number' => Arr::get($paymentAttributes, 'reference_number', Arr::get($paymentData, 'id')),
                    'verified_at' => now(),
                ]
            );

            $ticketIssuer->issue($booking->fresh(['user', 'facility']));

            $this->log('paymongo.payment_verified', $booking, [
                'event_type' => $eventType,
                'payment_id' => Arr::get($paymentData, 'id'),
            ]);
        });

        return response()->json(['message' => 'Webhook processed.']);
    }

    private function resolveBooking(array $paymentData, array $paymentAttributes): ?Booking
    {
        $bookingId = Arr::get($paymentAttributes, 'metadata.booking_id')
            ?? Arr::get($paymentAttributes, 'metadata.booking')
            ?? Arr::get($paymentData, 'metadata.booking_id');

        if ($bookingId) {
            return Booking::find($bookingId);
        }

        $reference = Arr::get($paymentAttributes, 'reference_number')
            ?? Arr::get($paymentAttributes, 'external_reference_number')
            ?? Arr::get($paymentData, 'id');

        if (! $reference) {
            return null;
        }

        $payment = Payment::where('reference_number', $reference)->first();

        return $payment?->booking;
    }

    private function hasValidSignature(Request $request): bool
    {
        $secret = config('services.paymongo.webhook_secret');

        if (! $secret) {
            return true;
        }

        $signatureHeader = $request->header('Paymongo-Signature');

        if (! $signatureHeader) {
            return false;
        }

        $parts = collect(explode(',', $signatureHeader))
            ->mapWithKeys(function (string $part) {
                [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);

                return [$key => $value];
            });

        $timestamp = $parts->get('t');
        $testSignature = $parts->get('te');
        $liveSignature = $parts->get('li');
        $expected = hash_hmac('sha256', $timestamp.'.'.$request->getContent(), $secret);

        return $timestamp && hash_equals($expected, (string) ($liveSignature ?: $testSignature));
    }

    private function log(string $action, ?Booking $booking, array $properties = []): void
    {
        SystemLog::create([
            'user_id' => null,
            'action' => $action,
            'target_type' => $booking ? Booking::class : null,
            'target_id' => $booking?->id,
            'properties' => $properties,
            'ip_address' => request()->ip(),
        ]);
    }
}
