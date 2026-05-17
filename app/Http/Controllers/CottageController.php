<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cottage;

class CottageController extends Controller
{
    /**
     * Display a listing of available cottages.
     */
    public function index()
    {
        $cottages = Cottage::where('is_available', true)->get();
        return view('cottages.index', compact('cottages'));
    }

    /**
     * Display the specified cottage.
     */
    public function show(Cottage $cottage)
    {
        return view('cottages.show', compact('cottage'));
    }
}
