<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Facility;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facilities = Facility::all();

        return Inertia::render('Facilities/Index', [
            'facilities' => $facilities,
            'flash' => session('success'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return Inertia::render('Facilities/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'quantity_total' => 'required|integer|min:0',
            'quantity_available' => 'integer|min:0',
            'condition' => 'in:baik,rusak_ringan,rusak_berat',
            'description' => 'nullable|string',
        ]);

        Facility::create($data);

         return redirect()->back()->with('success', 'Fasilitas berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Inertia::render('Facilities/Edit', [
            'facilities' => $id
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'quantity_total' => 'required|integer|min:0',
            'quantity_available' => 'integer|min:0',
            'condition' => 'in:baik,rusak_ringan,rusak_berat',
            'description' => 'nullable|string',
        ]);

        Facility::create($data);

        return redirect()->back()->with('success', 'Fasilitas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facility $facility)
    {
        $facility->delete();

        return redirect()->back()->with('success', 'Fasilitas berhasil dihapus!');
    }
}
