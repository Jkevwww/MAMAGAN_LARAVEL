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
        ]);

        $facilities = Facility::withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('is_active', true)
            ->where('is_bookable', true)
            ->when($request->filled('category'), fn ($query) => $query->where('category', $filters['category']))
            ->when($request->filled('guest_count'), fn ($query) => $query->where('capacity', '>=', $filters['guest_count']))
            ->when($request->filled('min_price'), fn ($query) => $query->where('price_max', '>=', $filters['min_price']))
            ->when($request->filled('max_price'), fn ($query) => $query->where('price_min', '<=', $filters['max_price']))
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', "%{$filters['search']}%"))
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = Facility::where('is_active', true)->distinct()->orderBy('category')->pluck('category');
        $capacityOptions = Facility::where('is_active', true)
            ->where('is_bookable', true)
            ->distinct()
            ->orderBy('capacity')
            ->pluck('capacity');

        return view('facilities.index', compact('facilities', 'categories', 'capacityOptions'));
    }

    public function show(Facility $facility)
    {
        $facility->load(['reviews.user', 'reviews.media']);

        return view('facilities.show', compact('facility'));
    }
}
