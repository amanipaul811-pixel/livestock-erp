<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('species', function (Blueprint $table) {
            // Set once per species, then reused as the default price/kg on
            // every future sale of that species -- still editable per sale.
            $table->decimal('default_price_per_kg', 10, 2)->nullable()->after('target_exit_weight_kg');
        });
    }

    public function down(): void
    {
        Schema::table('species', function (Blueprint $table) {
            $table->dropColumn('default_price_per_kg');
        });
    }
};
