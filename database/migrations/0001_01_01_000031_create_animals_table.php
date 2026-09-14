<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('tag_id', 50)->unique(); // ear tag / RFID
            $table->foreignId('batch_id')->constrained();
            $table->foreignId('species_id')->constrained();
            $table->string('breed', 80)->nullable();
            $table->enum('sex', ['male', 'female']);
            $table->unsignedSmallInteger('estimated_age_months')->nullable();
            $table->date('entry_date');
            $table->decimal('entry_weight_kg', 6, 2);
            $table->decimal('purchase_price', 12, 2);
            $table->foreignId('supplier_id')->nullable()->constrained();
            $table->foreignId('current_pen_id')->nullable()->constrained('pens');
            $table->enum('status', ['on_feed', 'sold', 'dead', 'transferred'])->default('on_feed');
            $table->date('exit_date')->nullable();
            $table->decimal('exit_weight_kg', 6, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
