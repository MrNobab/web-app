<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    // 👇 THIS IS THE MISSING PART CAUSING THE ERROR
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}