<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Only set for order_type=animal -- lets the order capture what's
            // being bought (species, headcount) so receiving it can guide the
            // user through recording those animals, mirroring how feed_item_id
            // and quantity_kg do the same for order_type=feed.
            $table->foreignId('species_id')->nullable()->after('order_type')->constrained();
            $table->unsignedInteger('quantity')->nullable()->after('species_id');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('species_id');
            $table->dropColumn('quantity');
        });
    }
};
