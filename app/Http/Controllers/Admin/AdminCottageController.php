<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Cottage;
use Illuminate\Support\Facades\Storage;

class AdminCottageController extends Controller
{
    /**
     * Display a listing of cottages.
     */
    public function index()
    {
        $cottages = Cottage::latest()->get();
        return view('admin.cottages.index', compact('cottages'));
    }

    /**
     * Show the form for creating a new cottage.
     */
    public function create()
    {
        return view('admin.cottages.create');
    }

    /**
     * Store a newly created cottage in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('cottages', 'public');
        }

        Cottage::create($data);

        return redirect()->route('admin.cottages.index')->with('success', 'Cottage created successfully.');
    }

    /**
     * Show the form for editing the specified cottage.
     */
    public function edit(Cottage $cottage)
    {
        return view('admin.cottages.edit', compact('cottage'));
    }

    /**
     * Update the specified cottage in storage.
     */
    public function update(Request $request, Cottage $cottage)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            if ($cottage->image) {
                Storage::disk('public')->delete($cottage->image);
            }
            $data['image'] = $request->file('image')->store('cottages', 'public');
        }

        $cottage->update($data);

        return redirect()->route('admin.cottages.index')->with('success', 'Cottage updated successfully.');
    }

    /**
     * Remove the specified cottage from storage.
     */
    public function destroy(Cottage $cottage)
    {
        if ($cottage->image) {
            Storage::disk('public')->delete($cottage->image);
        }
        $cottage->delete();

        return redirect()->route('admin.cottages.index')->with('success', 'Cottage deleted successfully.');
    }
}
