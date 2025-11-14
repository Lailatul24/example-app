<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LoanController extends Controller
{

    public function create()
    {
        // Ambil fasilitas yang stoknya masih tersedia
        $facilities = Facility::where('quantity_available', '>', 0)
            ->select('id', 'name', 'quantity_available')
            ->orderBy('name')
            ->get();

        return Inertia::render('Loans/Create', [
            'facilities' => $facilities,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'borrower_name' => 'required|string|max:255',
            'borrowed_at' => 'required|date',
            'return_due_at' => 'nullable|date|after_or_equal:borrowed_at',
            'items' => 'required|array|min:1',
            'items.*.facility_id' => 'required|exists:facilities,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($data) {
            $loan = Loan::create([
                'borrower_name' => $data['borrower_name'],
                'borrowed_at' => $data['borrowed_at'],
                'return_due_at' => $data['return_due_at'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $facility = Facility::find($item['facility_id']);

                if ($facility->quantity_available < $item['quantity']) {
                    throw new \Exception("Stok tidak mencukupi untuk {$facility->name}");
                }

                // Kurangi stok
                $facility->decrement('quantity_available', $item['quantity']);

                // Simpan ke loan_items
                $loan->items()->create($item);
            }
        });

        return back()->with('success', 'Peminjaman berhasil disimpan');
    }

    public function returnLoan($id)
    {
        $loan = Loan::with(['items.facility' => function ($q) {
            $q->lockForUpdate(); // Hindari race condition
        }])->findOrFail($id);

        if ($loan->returned_at) {
            return back()->with('error', 'Barang sudah dikembalikan sebelumnya.');
        }

        DB::transaction(function () use ($loan) {

            foreach ($loan->items as $item) {
                $facility = $item->facility;

                if ($facility) {
                    // Tambah stok kembali dengan pengamanan
                    $facility->quantity_available += $item->quantity;
                    $facility->save();
                }
            }

            $loan->update([
                'returned_at' => now(),
            ]);

        });

        return back()->with('success', 'Barang berhasil dikembalikan.');
    }


}

