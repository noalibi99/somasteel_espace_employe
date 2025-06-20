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
        Schema::create('offer_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            // Lien vers la ligne de la demande d'achat originale.
            // Peut être nullable si l'offre propose un article non demandé initialement (rare, mais possible).
            $table->foreignId('purchase_request_line_id')->nullable()->constrained('lines')->onDelete('set null');
            // Lien vers l'article si c'est un article connu/standard.
            // Nullable si c'est un article "one-shot" décrit uniquement dans l'offre.
            $table->foreignId('article_id')->nullable()->constrained('articles')->onDelete('set null');

            $table->text('description')->nullable(); // Description telle que fournie par le fournisseur
            $table->integer('quantity_requested')->default(0); // Quantité de la demande d'achat pour référence
            $table->integer('quantity_offered');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2)->default(0); // Calculé: quantity_offered * unit_price
            $table->text('notes')->nullable(); // Notes spécifiques à cette ligne d'offre
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_lines');
    }
};
