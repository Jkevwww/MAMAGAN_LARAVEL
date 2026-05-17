<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\SeasonalRate;
use Illuminate\Http\Request;

class SeasonalRateController extends Controller
{
    public function index()
    {
        return view('admin.rates.index', [
            'rates' => SeasonalRate::with('facility')->latest()->paginate(15),
            'facilities' => Facility::orderBy('name')->get(),
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
}
