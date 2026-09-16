<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animal_movements', function (Blueprint $table) {
            // Frozen at the moment of the move, not recomputed later --
            // an audit trail of what the animal weighed when it left a pen,
            // independent of any weigh-in corrections added afterward.
            $table->decimal('weight_kg_at_move', 6, 2)->nullable()->after('to_pen_id');
        });
    }

    public function down(): void
    {
        Schema::table('animal_movements', function (Blueprint $table) {
            $table->dropColumn('weight_kg_at_move');
        });
    }
};
