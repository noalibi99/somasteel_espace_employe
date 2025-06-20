<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PurchaseRequest; // Importer le modèle pour les constantes
use App\Models\RFQ; // Importer le modèle pour les constantes

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Table pivot pour la relation ManyToMany entre RFQs et Suppliers
        Schema::create('rfq_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfq_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->timestamps(); // Optionnel, mais peut être utile pour savoir quand un fournisseur a été ajouté à un RFQ
        });

        // Modifier la table purchase_requests pour inclure les nouveaux statuts
        // Important: Assurez-vous que les valeurs existantes sont compatibles
        // ou prévoyez une logique de migration de données si nécessaire.
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->enum('status', [
                PurchaseRequest::STATUS_DRAFT,
                PurchaseRequest::STATUS_PENDING,
                PurchaseRequest::STATUS_APPROVED,
                PurchaseRequest::STATUS_REJECTED,
                PurchaseRequest::STATUS_RFQ_IN_PROGRESS, // Nouveau
                PurchaseRequest::STATUS_ORDERED,         // Nouveau (pour plus tard)
                PurchaseRequest::STATUS_PROCESSED
            ])->change();
        });

        // Modifier la table rfqs pour inclure les nouveaux statuts
        // (le statut 'draft', 'sent', 'closed' existe déjà, on ajoute 'processing_offers')
        Schema::table('rfqs', function (Blueprint $table) {
            $table->enum('status', [
                RFQ::STATUS_DRAFT,
                RFQ::STATUS_SENT,
                RFQ::STATUS_PROCESSING_OFFERS, // Nouveau
                RFQ::STATUS_CLOSED
            ])->default(RFQ::STATUS_DRAFT)->change();

            // Ajouter des colonnes optionnelles au RFQ
            $table->text('notes')->nullable()->after('status');
            $table->timestamp('deadline_for_offers')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rfqs', function (Blueprint $table) {
            $table->dropColumn(['notes', 'deadline_for_offers']);
            // Revenir à l'ancien set de statuts pour rfqs (si besoin)
            $table->enum('status', [
                RFQ::STATUS_DRAFT,
                RFQ::STATUS_SENT,
                RFQ::STATUS_CLOSED
            ])->default(RFQ::STATUS_DRAFT)->change();
        });

        Schema::table('purchase_requests', function (Blueprint $table) {
            // Revenir à l'ancien set de statuts pour purchase_requests
            $table->enum('status', [
                PurchaseRequest::STATUS_DRAFT,
                PurchaseRequest::STATUS_PENDING,
                PurchaseRequest::STATUS_APPROVED,
                PurchaseRequest::STATUS_REJECTED,
                PurchaseRequest::STATUS_PROCESSED
            ])->change();
        });

        Schema::dropIfExists('rfq_supplier');
    }
};
