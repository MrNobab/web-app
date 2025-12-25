<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Product extends Model
{
    // This allows us to mass-assign data
    protected $guarded = [];

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

}
