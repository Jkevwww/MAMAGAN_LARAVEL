<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\BlackoutDate;
use App\Models\Facility;
use App\Models\Promotion;
use App\Models\SeasonalRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);

        $facilities = [
            [
                'name' => 'Oceanfront Family Cottage',
                'category' => 'Cottage',
                'description' => 'Open-air cottage near the shoreline for day-use family gatherings.',
                'price_min' => 1200,
                'price_max' => 1800,
                'capacity' => 12,
                'inventory_count' => 5,
                'rental_type' => 'daily',
            ],
            [
                'name' => 'Sunset Cabana Room',
                'category' => 'Cabana / Room',
                'description' => 'Air-conditioned cabana room with private seating and sunset-facing patio.',
                'price_min' => 3500,
                'price_max' => 5000,
                'capacity' => 4,
                'inventory_count' => 3,
                'rental_type' => 'overnight',
            ],
            [
                'name' => 'Kayak Rental',
                'category' => 'Beach Equipment',
                'description' => 'Single kayak rental with paddle and life vest.',
                'price_min' => 300,
                'price_max' => 600,
                'capacity' => 1,
                'inventory_count' => 10,
                'rental_type' => 'hourly',
            ],
            [
                'name' => 'Beach Volleyball Set',
                'category' => 'Beach Equipment',
                'description' => 'Net and ball set for beach games.',
                'price_min' => 250,
                'price_max' => 500,
                'capacity' => 10,
                'inventory_count' => 4,
                'rental_type' => 'daily',
            ],
        ];

        foreach ($facilities as $facilityData) {
            $facility = Facility::updateOrCreate(['name' => $facilityData['name']], $facilityData + [
                'is_active' => true,
                'is_bookable' => true,
            ]);

            SeasonalRate::updateOrCreate(
                ['facility_id' => $facility->id, 'name' => 'Summer Peak'],
                ['starts_at' => now()->startOfYear()->addMonths(2), 'ends_at' => now()->startOfYear()->addMonths(4), 'price' => $facility->price_max]
            );
        }

        Promotion::updateOrCreate(['code' => 'MAMAGAN10'], [
            'name' => 'Opening Discount',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'minimum_amount' => 1000,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonths(6),
            'usage_limit' => 100,
            'is_active' => true,
        ]);

        BlackoutDate::updateOrCreate(['title' => 'Quarterly Maintenance'], [
            'starts_at' => now()->addMonth()->toDateString(),
            'ends_at' => now()->addMonth()->addDay()->toDateString(),
            'reason' => 'Resort-wide maintenance window.',
        ]);

        foreach ([
            'resort_name' => 'Mamagan Fun & Adventure Beach Resort',
            'email' => 'info@mamagan.test',
            'phone' => '0917-000-0000',
            'address' => 'Mamagan Beach, Philippines',
            'business_hours' => '8:00 AM - 6:00 PM',
            'booking_rules' => 'Bookings require payment verification before QR ticket issuance.',
            'notification_settings' => 'Email verification codes are sent through configured SMTP.',
        ] as $key => $value) {
            AppSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
