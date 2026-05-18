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

        if (! in_array($eventType, ['checkout_session.payment.paid', 'payment.paid', 'payment_intent.succeeded', 'source.chargeable', 'payment.failed'], true)) {
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
            $isFailed = $eventType === 'payment.failed';

            $booking->update([
                'booking_status' => $isFailed ? $booking->booking_status : 'approved',
                'payment_status' => $isFailed ? 'failed' : 'paid',
            ]);

            $booking->payment()->updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'method' => Arr::get($paymentAttributes, 'source.type', Arr::get($paymentAttributes, 'payment_method.type', 'paymongo')),
                    'amount' => $amount ? ((float) $amount / 100) : $booking->total_amount,
                    'status' => $isFailed ? 'failed' : 'paid',
                    'reference_number' => Arr::get($paymentAttributes, 'reference_number', Arr::get($paymentData, 'id')),
                    'paymongo_checkout_id' => $this->checkoutSessionId($paymentData, $paymentAttributes),
                    'paymongo_payment_id' => $this->paymentId($paymentData, $paymentAttributes),
                    'provider_payload' => $paymentData,
                    'verified_at' => $isFailed ? null : now(),
                ]
            );

            if (! $isFailed) {
                $ticketIssuer->issue($booking->fresh(['user', 'facility']));
            }

            $this->log($isFailed ? 'paymongo.payment_failed' : 'paymongo.payment_verified', $booking, [
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

        $checkoutSessionId = $this->checkoutSessionId($paymentData, $paymentAttributes);

        if ($checkoutSessionId) {
            $payment = Payment::where('paymongo_checkout_id', $checkoutSessionId)->first();

            if ($payment?->booking) {
                return $payment->booking;
            }
        }

        $paymentId = $this->paymentId($paymentData, $paymentAttributes);

        if ($paymentId) {
            $payment = Payment::where('paymongo_payment_id', $paymentId)->orWhere('reference_number', $paymentId)->first();

            if ($payment?->booking) {
                return $payment->booking;
            }
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

    private function checkoutSessionId(array $paymentData, array $paymentAttributes): ?string
    {
        if (Arr::get($paymentData, 'type') === 'checkout_session') {
            return Arr::get($paymentData, 'id');
        }

        return Arr::get($paymentAttributes, 'checkout_session_id')
            ?? Arr::get($paymentAttributes, 'checkout_session.id')
            ?? Arr::get($paymentAttributes, 'metadata.checkout_session_id');
    }

    private function paymentId(array $paymentData, array $paymentAttributes): ?string
    {
        if (Arr::get($paymentData, 'type') === 'payment') {
            return Arr::get($paymentData, 'id');
        }

        return Arr::get($paymentAttributes, 'payment_id')
            ?? Arr::get($paymentAttributes, 'payments.0.id')
            ?? Arr::get($paymentAttributes, 'latest_payment.id')
            ?? Arr::get($paymentAttributes, 'metadata.payment_id');
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
