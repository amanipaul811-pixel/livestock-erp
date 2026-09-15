<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Only set for order_type=feed -- lets marking a feed PO "received"
            // create the matching stock-in movement automatically instead of
            // requiring a separate, disconnected Restock action.
            $table->foreignId('feed_item_id')->nullable()->after('order_type')->constrained();
            $table->decimal('quantity_kg', 10, 2)->nullable()->after('feed_item_id');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('feed_item_id');
            $table->dropColumn('quantity_kg');
        });
    }
};
