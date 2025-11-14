<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Facility;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BorrowingController extends Controller
{
    public function index()
    {
        $loans = \App\Models\Loan::with(['items.facility'])
        ->orderByDesc('borrowed_at')
        ->get()
        ->map(function ($loan) {
            return [
                'id' => $loan->id,
                'borrower_name' => $loan->borrower_name,
                'borrowed_at' => $loan->borrowed_at,
                'status' => $loan->status,
                'returned_at' => optional($loan->returned_at)->format('Y-m-d'),
                'items' => $loan->items->map(function ($item) {
                    return [
                        'facility_name' => $item->facility->name,
                        'quantity' => $item->quantity,
                    ];
                }),
            ];
        });
        $facilities = Facility::where('quantity_available', '>', 0)
            ->select('id', 'name', 'quantity_available')
            ->orderBy('name')
            ->get();

        return Inertia::render('Borrowings/Index', [
            'loans' => $loans,
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

    public function update( $id)
    {
        $loan = Loan::with('items.facility')->findOrFail($id);

        if ($loan->returned_at) {
            return back()->with('error', 'Barang sudah dikembalikan sebelumnya.');
        }

        foreach ($loan->items as $item) {
            $facility = $item->facility;
            if ($facility) {
                $facility->quantity_available += $item->quantity;
                $facility->save();
            }
        }

        $loan->update([
                'returned_at' => now(),
        ]);
        $loan->save();

        return redirect()->back()->with('success', 'Barang berhasil dikembalikan.');
    }

    public function returnLoan($id)
    {
        $loan = Loan::with('items.facility')->findOrFail($id);

        if ($loan->returned_at) {
            return back()->with('error', 'Barang sudah dikembalikan sebelumnya.');
        }

        DB::transaction(function () use ($loan) {
            foreach ($loan->items as $item) {
                $facility = $item->facility;
                $facility->increment('quantity_available', $item->quantity);
            }

            $loan->update([
                'returned_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Barang berhasil dikembalikan dan stok diperbarui.');
    }
}
