<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\SeasonalRate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SeasonalRateController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $filteredQuery = $this->filteredRateQuery($request);
        $analyticsRates = (clone $filteredQuery)->get();
        $rates = (clone $filteredQuery)
            ->orderBy('starts_at')
            ->paginate(5)
            ->withQueryString();

        $summary = [
            'total' => $analyticsRates->count(),
            'active' => $analyticsRates->filter(fn ($rate) => $rate->starts_at->lte($today) && $rate->ends_at->gte($today))->count(),
            'upcoming' => $analyticsRates->filter(fn ($rate) => $rate->starts_at->gt($today))->count(),
            'expired' => $analyticsRates->filter(fn ($rate) => $rate->ends_at->lt($today))->count(),
            'average_price' => $analyticsRates->avg('price') ?? 0,
        ];

        return view('admin.rates.index', [
            'rates' => $rates,
            'facilities' => Facility::orderBy('name')->get(),
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        SeasonalRate::create($this->validated($request));

        return back()->with('success', 'Seasonal rate saved.');
    }

    public function update(Request $request, SeasonalRate $seasonalRate)
    {
        $seasonalRate->update($this->validated($request));

        return back()->with('success', 'Seasonal rate updated.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'name' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    public function destroy(SeasonalRate $seasonalRate)
    {
        $seasonalRate->delete();

        return back()->with('success', 'Seasonal rate deleted.');
    }

    private function filteredRateQuery(Request $request)
    {
        return SeasonalRate::with('facility')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhereHas('facility', fn ($facility) => $facility
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('facility_id'), fn ($query) => $query->where('facility_id', $request->facility_id))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('ends_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('starts_at', '<=', $request->date_to));
    }
}
