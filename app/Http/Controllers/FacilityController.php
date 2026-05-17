<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $facilities = Facility::withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('is_active', true)
            ->where('is_bookable', true)
            ->when($request->category, fn ($query, $category) => $query->where('category', $category))
            ->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = Facility::where('is_active', true)->distinct()->pluck('category');

        return view('facilities.index', compact('facilities', 'categories'));
    }

    public function show(Facility $facility)
    {
        $facility->load(['reviews.user', 'reviews.media']);

        return view('facilities.show', compact('facility'));
    }
}
