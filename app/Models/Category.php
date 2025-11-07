<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'category_facility');
    }
}
