<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacilityAdminController extends Controller
{
    public function index()
    {
        $facilities = Facility::latest()->paginate(15);

        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('admin.facilities.form', ['facility' => new Facility()]);
    }

    public function store(Request $request)
    {
        $facility = Facility::create($this->validated($request));
        $this->storeImage($request, $facility);
        $this->log('facility.created', $facility);

        return redirect()->route('admin.facilities.index')->with('success', 'Facility created.');
    }

    public function edit(Facility $facility)
    {
        return view('admin.facilities.form', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $facility->update($this->validated($request));
        $this->storeImage($request, $facility);
        $this->log('facility.updated', $facility);

        return redirect()->route('admin.facilities.index')->with('success', 'Facility updated.');
    }

    public function destroy(Facility $facility)
    {
        if ($facility->image) {
            Storage::disk('public')->delete($facility->image);
        }
        $facility->delete();
        $this->log('facility.deleted', $facility);

        return back()->with('success', 'Facility deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:Cottage,Cabana / Room,Beach Equipment'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'price_min' => ['required', 'numeric', 'min:0'],
            'price_max' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'inventory_count' => ['required', 'integer', 'min:1'],
            'rental_type' => ['required', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
            'is_bookable' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
            'is_bookable' => $request->boolean('is_bookable'),
        ];
    }

    private function storeImage(Request $request, Facility $facility): void
    {
        if (! $request->hasFile('image')) {
            return;
        }

        if ($facility->image) {
            Storage::disk('public')->delete($facility->image);
        }

        $facility->update(['image' => $request->file('image')->store('facilities', 'public')]);
    }

    private function log(string $action, Facility $facility): void
    {
        \App\Models\SystemLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'target_type' => Facility::class,
            'target_id' => $facility->id,
            'ip_address' => request()->ip(),
        ]);
    }
}
