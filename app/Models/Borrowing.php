<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower_name',
        'borrow_date',
        'return_date',
        'status',
    ];

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'borrowing_facility')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
