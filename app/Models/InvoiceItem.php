<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        // 1. When an item is created (Invoice Saved), subtract stock
        static::created(function ($item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->decrement('stock_quantity', $item->quantity);
            }
        });

        // 2. If an item is deleted (Invoice Cancelled), give stock back
        static::deleted(function ($item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('stock_quantity', $item->quantity);
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}