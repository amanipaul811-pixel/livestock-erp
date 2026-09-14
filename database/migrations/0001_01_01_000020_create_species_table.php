<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->unique(); // Cattle, Goat, Sheep
            $table->unsignedSmallInteger('default_cycle_days');
            $table->decimal('target_adg_kg', 5, 2)->nullable();
            $table->decimal('target_entry_weight_kg', 6, 2)->nullable();
            $table->decimal('target_exit_weight_kg', 6, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('species');
    }
};
