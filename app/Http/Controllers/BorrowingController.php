<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Facility;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BorrowingController extends Controller
{
    public function index()
    {
        $borrowings = Borrowing::with('facilities')->latest()->get();
        $facilities = Facility::all();

        return Inertia::render('Borrowings/Index', [
            'borrowings' => $borrowings,
            'facilities' => $facilities,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'borrower_name' => 'required|string|max:255',
            'borrow_date' => 'required|date',
            'facility_ids' => 'required|array',
            'facility_ids.*' => 'exists:facilities,id',
        ]);

        $borrowing = Borrowing::create([
            'borrower_name' => $validated['borrower_name'],
            'borrow_date' => $validated['borrow_date'],
        ]);

        foreach ($validated['facility_ids'] as $id) {
            // Ambil stok
            $facility = Facility::find($id);

            // Kurangi stok 1
            if ($facility->quantity_available > 0) {
                $facility->decrement('quantity_available', 1);
            }

            // Simpan relasi pivot
            $borrowing->facilities()->attach($id, ['quantity' => 1]);
        }

        return redirect()->back()->with('success', 'Peminjaman berhasil ditambahkan!');
    }

    public function update(Borrowing $borrowing)
    {
        if ($borrowing->status === 'dikembalikan') {
            return redirect()->back()->with('info', 'Barang sudah dikembalikan sebelumnya.');
        }

        // Ubah status ke dikembalikan
        $borrowing->update([
            'status' => 'dikembalikan',
            'return_date' => now(),
        ]);

        // Tambah kembali stok barang
        foreach ($borrowing->facilities as $facility) {
            $facility->increment('quantity_available', $facility->pivot->quantity);
        }

        return redirect()->back()->with('success', 'Barang berhasil dikembalikan.');
    }
}
