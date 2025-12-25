<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $guarded = []; // Allow updates

    // 👇 This is required for "Used By" to work
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    
    protected static function booted()
    {
        static::updated(function ($license) {
            if ($license->store) {
                // Sync store status if license is suspended via Top Admin
                $isNotExpired = $license->expires_at ? now()->lt($license->expires_at) : true;
                $shouldBeActive = $license->status === 'active' && $isNotExpired;
                $license->store->update(['is_active' => $shouldBeActive]);
            }
        });

        static::deleted(function ($license) {
            // If this license was attached to a store...
            if ($license->store) {
                // ...Lock that store immediately
                $license->store->update([
                    'is_active' => false,
                    'activation_key' => null, // Clear the key so they know they need a new one
                ]);
            }
        });

    }
}