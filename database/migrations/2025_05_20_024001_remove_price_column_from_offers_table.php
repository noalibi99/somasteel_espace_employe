<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (Schema::hasColumn('offers', 'price')) {
                $table->dropColumn('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            // Si vous revenez en arrière, vous voudrez peut-être recréer la colonne
            // Mais cela dépend si vous aviez des données. Pour un nouveau développement,
            // on peut supposer qu'elle était nullable ou avait une valeur par défaut.
            $table->decimal('price', 10, 2)->nullable()->after('supplier_id');
        });
    }
};
