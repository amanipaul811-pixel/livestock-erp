<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weigh_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained()->cascadeOnDelete();
            $table->date('weigh_date');
            $table->decimal('weight_kg', 6, 2);
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weigh_ins');
    }
};
