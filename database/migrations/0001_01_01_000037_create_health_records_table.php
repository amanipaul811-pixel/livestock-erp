<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained()->cascadeOnDelete();
            $table->enum('record_type', ['vaccination', 'deworming', 'treatment', 'checkup', 'death']);
            $table->date('record_date');
            $table->string('description', 255)->nullable();
            $table->string('medicine_used', 150)->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->foreignId('performed_by')->nullable()->constrained('users');
            $table->string('cause_of_death', 255)->nullable(); // only for record_type = death
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
