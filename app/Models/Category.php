<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Category extends Model
{
    protected $guarded = []; // Allow mass assignment

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }    
}