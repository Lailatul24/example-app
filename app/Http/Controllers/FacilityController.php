<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Facility;
use Illuminate\Http\Request;
use Inertia\Inertia;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Row;

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
            'exportUrl' => route('facilities.export'),
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

        $query = Facility::query()->with('categories'); // <- kamu pakai many-to-many

        if ($categoryId) {
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $facilities = $query->get();

        $filePath = storage_path('app/facilities_export.xlsx');

        $writer = new Writer();
        $writer->openToFile($filePath);

        // header style
        $headerStyle = (new Style())
            ->setFontBold();

        // header
        $writer->addRow(Row::fromValues([
            'Nama Fasilitas',
            'Kategori',
            'Qty Total',
            'Qty Tersedia'
        ], $headerStyle));

        // data
        foreach ($facilities as $f) {
            $writer->addRow(Row::fromValues([
                $f->name,
                $f->categories->pluck('name')->join(', '), // many to many
                $f->quantity_total,
                $f->quantity_available
            ]));
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend();
    }

}
