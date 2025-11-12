<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','name','quantity_total','quantity_available','condition','description'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($facility) {
            // Generate kode otomatis, contoh: INV-2025-001
            $latest = static::latest('id')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $facility->code = 'INV-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_facility');
    }

    public function borrowings()
    {
        return $this->belongsToMany(Borrowing::class, 'borrowing_facility')
            ->withPivot('quantity')
            ->withTimestamps();
    }

}
