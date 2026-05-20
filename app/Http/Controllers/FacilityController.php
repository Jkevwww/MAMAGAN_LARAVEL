<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'guest_count' => ['nullable', 'integer', 'min:1'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:featured,price_low,price_high,capacity,rating'],
        ]);

        $activeBookableFacilities = Facility::where('is_active', true)
            ->where('is_bookable', true);

        $sort = $filters['sort'] ?? 'featured';

        $facilities = (clone $activeBookableFacilities)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->when($request->filled('category'), fn ($query) => $query->where('category', $filters['category']))
            ->when($request->filled('guest_count'), fn ($query) => $query->where('capacity', '>=', $filters['guest_count']))
            ->when($request->filled('min_price'), fn ($query) => $query->where('price_max', '>=', $filters['min_price']))
            ->when($request->filled('max_price'), fn ($query) => $query->where('price_min', '<=', $filters['max_price']))
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', "%{$filters['search']}%"))
            ->when($sort === 'price_low', fn ($query) => $query->orderBy('price_min')->orderBy('name'))
            ->when($sort === 'price_high', fn ($query) => $query->orderByDesc('price_min')->orderBy('name'))
            ->when($sort === 'capacity', fn ($query) => $query->orderByDesc('capacity')->orderBy('name'))
            ->when($sort === 'rating', fn ($query) => $query->orderByDesc('reviews_avg_rating')->orderByDesc('reviews_count')->orderBy('name'))
            ->when($sort === 'featured', fn ($query) => $query->orderBy('category')->orderBy('name'))
            ->paginate(12)
            ->withQueryString();

        $categories = (clone $activeBookableFacilities)
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $categoryStats = (clone $activeBookableFacilities)
            ->select('category')
            ->selectRaw('COUNT(*) as facility_count')
            ->selectRaw('MIN(price_min) as starting_rate')
            ->selectRaw('SUM(inventory_count) as available_units')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        $capacityOptions = (clone $activeBookableFacilities)
            ->distinct()
            ->orderBy('capacity')
            ->pluck('capacity');

        $stats = [
            'facility_count' => (clone $activeBookableFacilities)->count(),
            'category_count' => (clone $activeBookableFacilities)->distinct()->count('category'),
            'available_units' => (clone $activeBookableFacilities)->sum('inventory_count'),
            'guest_capacity' => (clone $activeBookableFacilities)->sum('capacity'),
        ];

        return view('facilities.index', compact('facilities', 'categories', 'categoryStats', 'capacityOptions', 'stats', 'sort'));
    }

    public function show(Facility $facility)
    {
        abort_unless($facility->is_active, 404);

        $facility->load(['reviews.user', 'reviews.media']);

        return view('facilities.show', compact('facility'));
    }
}
