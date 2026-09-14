<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // e.g. Maize bran, Concentrate mix
            $table->enum('unit', ['kg', 'bag', 'liter'])->default('kg');
            $table->decimal('cost_per_unit', 10, 2);
            $table->foreignId('warehouse_id')->nullable()->constrained();
            $table->decimal('reorder_level', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_items');
    }
};
