<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->foreignId('purchase_order_line_id')->constrained()->onDelete('cascade'); // Important: cascade si la ligne de PO est supprimée
            $table->foreignId('article_id')->nullable()->constrained('articles')->onDelete('set null');
            $table->integer('quantity_received');
            $table->text('notes')->nullable();
            $table->boolean('is_confirmed')->default(false); // Le magasinier confirme-t-il cette réception spécifique ?
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_lines');
    }
};
