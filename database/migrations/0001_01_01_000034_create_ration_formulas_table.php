<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ration_formulas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // e.g. "Cattle Finishing Ration"
            $table->foreignId('species_id')->constrained();
            $table->enum('stage', ['starter', 'growing', 'finishing'])->default('growing');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ration_formulas');
    }
};
