<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_item_id')->constrained();
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->decimal('quantity_kg', 10, 2);
            $table->string('reason')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_stock_movements');
    }
};
