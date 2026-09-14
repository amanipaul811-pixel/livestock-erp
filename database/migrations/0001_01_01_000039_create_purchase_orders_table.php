<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 30)->unique();
            $table->foreignId('supplier_id')->constrained();
            $table->enum('order_type', ['animal', 'feed', 'medicine', 'other']);
            $table->date('order_date');
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['pending', 'received', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
