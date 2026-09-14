<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ration_formula_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ration_formula_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feed_item_id')->constrained();
            $table->decimal('quantity_kg_per_head', 6, 2); // daily amount per animal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ration_formula_items');
    }
};
