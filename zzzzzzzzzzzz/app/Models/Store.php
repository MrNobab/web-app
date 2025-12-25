<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;

    protected $guarded = [];

    // 1. Existing relationship (Users)
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // 2. 👇 ADD THIS (Fixes the error)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // 3. 👇 Add these too, or you will get errors when creating Customers/Invoices later
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}