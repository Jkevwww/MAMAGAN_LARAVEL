<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlackoutDate;
use App\Models\Facility;
use Illuminate\Http\Request;

class BlackoutDateController extends Controller
{
    public function index()
    {
        return view('admin.blackouts.index', [
            'blackouts' => BlackoutDate::with('facility')->latest()->paginate(15),
            'facilities' => Facility::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        BlackoutDate::create($request->validate([
            'facility_id' => ['nullable', 'exists:facilities,id'],
            'title' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'reason' => ['nullable', 'string'],
        ]));

        return back()->with('success', 'Blackout window saved.');
    }

    public function destroy(BlackoutDate $blackoutDate)
    {
        $blackoutDate->delete();

        return back()->with('success', 'Blackout window deleted.');
    }
}
