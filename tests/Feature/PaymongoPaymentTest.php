<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymongoPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_creation_redirects_to_paymongo_checkout(): void
    {
        config(['services.paymongo.secret_key' => 'sk_test_123']);

        Http::fake([
            'api.paymongo.com/v1/checkout_sessions' => Http::response([
                'data' => [
                    'id' => 'cs_test_123',
                    'attributes' => [
                        'checkout_url' => 'https://checkout.paymongo.com/cs_test_123',
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $facility = Facility::create([
            'name' => 'Ocean Cottage',
            'category' => 'Cottage',
            'price_min' => 1200,
            'price_max' => 1200,
            'capacity' => 10,
            'inventory_count' => 2,
            'is_active' => true,
            'is_bookable' => true,
        ]);

        $response = $this->actingAs($user)->post('/bookings', [
            'facility_id' => $facility->id,
            'booking_date' => now()->addDay()->toDateString(),
            'quantity' => 1,
            'guest_count' => 2,
            'booking_type' => 'day_use',
        ]);

        $response->assertRedirect('https://checkout.paymongo.com/cs_test_123');

        $this->assertDatabaseHas('payments', [
            'method' => 'paymongo',
            'status' => 'pending',
            'reference_number' => 'MAM-BOOKING-1',
            'paymongo_checkout_id' => 'cs_test_123',
            'checkout_url' => 'https://checkout.paymongo.com/cs_test_123',
        ]);
    }

    public function test_paymongo_webhook_marks_booking_paid_and_issues_ticket(): void
    {
        config(['services.paymongo.webhook_secret' => null]);

        $user = User::factory()->create();
        $facility = Facility::create([
            'name' => 'Ocean Cottage',
            'category' => 'Cottage',
            'price_min' => 1200,
            'price_max' => 1200,
            'capacity' => 10,
            'inventory_count' => 2,
            'is_active' => true,
            'is_bookable' => true,
        ]);
        $booking = Booking::create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'booking_date' => now()->addDay()->toDateString(),
            'quantity' => 1,
            'guest_count' => 2,
            'booking_type' => 'day_use',
            'base_amount' => 1200,
            'discount_amount' => 0,
            'total_amount' => 1200,
            'booking_status' => 'pending',
            'payment_status' => 'pending',
        ]);
        Payment::create([
            'booking_id' => $booking->id,
            'method' => 'paymongo',
            'amount' => 1200,
            'status' => 'pending',
            'reference_number' => 'MAM-BOOKING-'.$booking->id,
            'paymongo_checkout_id' => 'cs_test_123',
        ]);

        $response = $this->postJson('/webhooks/paymongo', [
            'data' => [
                'attributes' => [
                    'type' => 'checkout_session.payment.paid',
                    'data' => [
                        'id' => 'cs_test_123',
                        'type' => 'checkout_session',
                        'attributes' => [
                            'amount' => 120000,
                            'reference_number' => 'MAM-BOOKING-'.$booking->id,
                            'payments' => [
                                ['id' => 'pay_test_123'],
                            ],
                            'metadata' => [
                                'booking_id' => (string) $booking->id,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertOk();

        $booking->refresh();

        $this->assertSame('approved', $booking->booking_status);
        $this->assertSame('paid', $booking->payment_status);
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'status' => 'paid',
            'paymongo_checkout_id' => 'cs_test_123',
            'paymongo_payment_id' => 'pay_test_123',
        ]);
        $this->assertNotNull($booking->ticket()->first());
        $this->assertStringStartsWith('MAM-', $booking->ticket()->first()->reference_number);
    }

    public function test_booking_page_syncs_paid_paymongo_checkout_when_webhook_is_missing(): void
    {
        config(['services.paymongo.secret_key' => 'sk_test_123']);

        Http::fake([
            'api.paymongo.com/v1/checkout_sessions/cs_test_123' => Http::response([
                'data' => [
                    'id' => 'cs_test_123',
                    'attributes' => [
                        'reference_number' => 'MAM-BOOKING-1',
                        'payment_method_used' => 'gcash',
                        'payments' => [
                            [
                                'id' => 'pay_test_123',
                                'attributes' => [
                                    'status' => 'paid',
                                    'amount' => 120000,
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $facility = Facility::create([
            'name' => 'Ocean Cottage',
            'category' => 'Cottage',
            'price_min' => 1200,
            'price_max' => 1200,
            'capacity' => 10,
            'inventory_count' => 2,
            'is_active' => true,
            'is_bookable' => true,
        ]);
        $booking = Booking::create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'booking_date' => now()->addDay()->toDateString(),
            'quantity' => 1,
            'guest_count' => 2,
            'booking_type' => 'day_use',
            'base_amount' => 1200,
            'discount_amount' => 0,
            'total_amount' => 1200,
            'booking_status' => 'pending',
            'payment_status' => 'pending',
        ]);
        Payment::create([
            'booking_id' => $booking->id,
            'method' => 'paymongo',
            'amount' => 1200,
            'status' => 'pending',
            'reference_number' => 'MAM-BOOKING-'.$booking->id,
            'paymongo_checkout_id' => 'cs_test_123',
        ]);

        $response = $this->actingAs($user)->get(route('bookings.show', $booking));

        $response
            ->assertOk()
            ->assertSee('Ticket Reference');

        $booking->refresh();

        $this->assertSame('approved', $booking->booking_status);
        $this->assertSame('paid', $booking->payment_status);
        $this->assertNotNull($booking->ticket()->first());
    }
}
