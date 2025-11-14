<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = ['borrower_name', 'borrowed_at', 'return_due_at', 'status', 'returned_at' ];

    protected $casts = [
        'returned_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(LoanItem::class);
    }
}
