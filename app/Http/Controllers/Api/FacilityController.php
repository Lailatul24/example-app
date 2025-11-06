<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facilities = Facility::with(['category', 'location'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $facilities,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:facilities,code',
            'name' => 'required|string|max:255',
            'quantity_total' => 'required|integer|min:0',
            'quantity_available' => 'integer|min:0',
            'condition' => 'in:baik,rusak_ringan,rusak_berat',
            'description' => 'nullable|string',
        ]);

        $facility = Facility::create($validated);

        $categories = Category::all();

        // Hubungkan fasilitas ke semua kategori
        $facility->categories()->attach($categories->pluck('id'));

        return response()->json([
            'status' => 'success',
            'message' => 'Barang inventaris berhasil ditambahkan ke semua kategori.',
            'data' => $facility->load('categories'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        return response()->json([
            'status' => 'success',
            'data' => $facility->load(['category', 'location']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function byCategory($categoryName)
    {
        // Cari kategori berdasarkan nama (case-insensitive)
        $category = \App\Models\Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak ditemukan.'
            ], 404);
        }

        // Ambil semua fasilitas yang termasuk ke kategori ini
        $facilities = $category->facilities()->with('categories')->get();

        return response()->json([
            'status' => 'success',
            'category' => $category->name,
            'data' => $facilities,
        ]);
    }

}
