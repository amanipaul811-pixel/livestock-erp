<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pens', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->unsignedInteger('capacity');
            $table->enum('stage', ['quarantine', 'growing', 'finishing'])->default('growing');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pens');
    }
};
