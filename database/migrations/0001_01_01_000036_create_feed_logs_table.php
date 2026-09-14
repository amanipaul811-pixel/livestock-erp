<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained(); // fed at batch/pen level
            $table->foreignId('feed_item_id')->constrained();
            $table->date('feed_date');
            $table->decimal('quantity_kg', 10, 2);
            $table->decimal('total_cost', 12, 2);
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_logs');
    }
};
