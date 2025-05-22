<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            // Lien optionnel vers la ligne de l'offre, si on veut tracer directement
            $table->foreignId('offer_line_id')->nullable()->constrained('offer_lines')->onDelete('set null');
            $table->foreignId('article_id')->nullable()->constrained('articles')->onDelete('set null');
            $table->text('description'); // Description de l'article tel que commandé
            $table->integer('quantity_ordered');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2); // Calculé
            $table->date('expected_delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
    }
};
