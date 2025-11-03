<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['user', 'facility'])->get();
        return response()->json($loans);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'facility_id' => 'required|exists:facilities,id',
            'quantity' => 'required|integer|min:1',
            'borrowed_at' => 'required|date',
            'returned_at' => 'nullable|date',
            'status' => 'in:dipinjam,dikembalikan,hilang',
            'notes' => 'nullable|string',
        ]);

        $loan = Loan::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $loan,
        ], 201);
    }
}
