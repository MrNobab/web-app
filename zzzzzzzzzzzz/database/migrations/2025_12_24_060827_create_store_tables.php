<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    // 1. Products Table
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('image')->nullable();
        $table->decimal('price', 10, 2);
        $table->integer('stock_quantity')->default(0);
        $table->timestamps();
    });

    // 2. Customers Table
    Schema::create('customers', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->timestamps();
    });

    // 3. Invoices Table
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
        $table->date('date');
        $table->decimal('subtotal', 10, 2);
        $table->decimal('tax', 10, 2)->default(0); // 5% tax from your mockup
        $table->decimal('total', 10, 2);
        $table->timestamps();
    });

    // 4. Invoice Items (Pivot table for products inside an invoice)
    Schema::create('invoice_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
        $table->foreignId('product_id')->constrained();
        $table->integer('quantity');
        $table->decimal('unit_price', 10, 2); // Price at moment of sale
        $table->decimal('row_total', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::dropIfExists('invoice_items');
    Schema::dropIfExists('invoices');
    Schema::dropIfExists('customers');
    Schema::dropIfExists('products');
}
};
