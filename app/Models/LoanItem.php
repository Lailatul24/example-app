<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanItem extends Model
{
    protected $fillable = ['loan_id', 'facility_id', 'quantity'];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
