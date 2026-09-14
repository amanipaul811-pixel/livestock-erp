<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->nullable()->constrained(); // null = general overhead
            $table->enum('category', ['labor', 'utilities', 'transport', 'rent', 'other']);
            $table->date('expense_date');
            $table->decimal('amount', 12, 2);
            $table->string('description', 255)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
