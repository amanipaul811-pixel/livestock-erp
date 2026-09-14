<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_code', 30)->unique(); // e.g. BATCH-2026-001
            $table->foreignId('species_id')->constrained();
            $table->foreignId('pen_id')->nullable()->constrained();
            $table->date('start_date');
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->enum('status', ['active', 'partially_sold', 'closed'])->default('active');
            $table->string('notes', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
