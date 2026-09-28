<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Set once, at receipt, for order_type=animal -- each animal PO
            // gets its own dedicated batch rather than being allowed to add
            // into an existing one, so two separate buys never get merged
            // into a single batch just for sharing a species or a supplier.
            $table->foreignId('batch_id')->nullable()->after('quantity')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('batch_id');
        });
    }
};
