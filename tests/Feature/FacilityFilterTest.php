<?php

namespace Tests\Feature;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_facilities_can_be_filtered_by_category_capacity_and_price_range(): void
    {
        Facility::create([
            'name' => 'Family Cottage',
            'category' => 'Cottage',
            'price_min' => 1200,
            'price_max' => 1800,
            'capacity' => 12,
            'inventory_count' => 2,
            'is_active' => true,
            'is_bookable' => true,
        ]);

        Facility::create([
            'name' => 'Small Cabana',
            'category' => 'Cabana / Room',
            'price_min' => 3500,
            'price_max' => 5000,
            'capacity' => 4,
            'inventory_count' => 1,
            'is_active' => true,
            'is_bookable' => true,
        ]);

        Facility::create([
            'name' => 'Volleyball Set',
            'category' => 'Beach Equipment',
            'price_min' => 250,
            'price_max' => 500,
            'capacity' => 10,
            'inventory_count' => 3,
            'is_active' => true,
            'is_bookable' => true,
        ]);

        $response = $this->get('/facilities?category=Cottage&guest_count=8&min_price=1000&max_price=2000');

        $response
            ->assertOk()
            ->assertSee('Family Cottage')
            ->assertDontSee('Small Cabana')
            ->assertDontSee('Volleyball Set');
    }
}
