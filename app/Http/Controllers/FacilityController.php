<?php

namespace App\Http\Controllers;

use App\Exports\FacilitiesExport;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Excel;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $facilities = Facility::all();

        return Inertia::render('Facilities/Index', [
           'facilities' => Facility::with('categories')->latest()->get(),

            // ambil semua kategori untuk dropdown
            'categories' => Category::orderBy('name')->get(),
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
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'quantity_total' => 'required|integer|min:0',
            'quantity_available' => 'integer|min:0',
            'condition' => 'in:baik,rusak_ringan,rusak_berat',
            'description' => 'nullable|string',
        ]);

        $facility = Facility::create($data);

        // Simpan relasi ke pivot table
        $facility->categories()->sync($data['category_ids']);

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
    public function update(Request $request, Facility $facility)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'quantity_total' => 'required|integer|min:0',
            'quantity_available' => 'integer|min:0',
            'condition' => 'in:baik,rusak_ringan,rusak_berat',
            'description' => 'nullable|string',
        ]);

        $facility->update($data);
        $facility->categories()->sync($data['category_ids']);

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

    public function export(Request $request)
    {
        $categoryId = $request->get('category_id');
        $categoryName = 'Semua_Kategori';

        if ($categoryId) {
            $category = \App\Models\Category::find($categoryId);
            if ($category) {
                $categoryName = $category->name;
            }
        }

        return Excel::download(new FacilitiesExport($categoryId), "fasilitas_{$categoryName}.xlsx");
    }
}
