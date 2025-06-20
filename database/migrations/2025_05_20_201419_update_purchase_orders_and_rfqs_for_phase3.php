<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PurchaseOrder; // Pour les constantes
use App\Models\Rfq; // Pour les constantes

return new class extends Migration
{
    public function up(): void
    {
        // Modifier la table purchase_orders existante
        Schema::table('purchase_orders', function (Blueprint $table) {
            // S'assurer que les colonnes FK sont bien là et correctement typées
            // La migration originale avait rfq_id et supplier_id
            if (!Schema::hasColumn('purchase_orders', 'rfq_id')) {
                $table->foreignId('rfq_id')->constrained('rfqs'); // rfqs au pluriel
            }
             if (!Schema::hasColumn('purchase_orders', 'supplier_id')) {
                $table->foreignId('supplier_id')->constrained('suppliers');
            }

            // Ajouter les nouvelles colonnes
            if (!Schema::hasColumn('purchase_orders', 'po_number')) {
                $table->string('po_number')->unique()->after('id');
            }
            if (!Schema::hasColumn('purchase_orders', 'offer_id')) {
                $table->foreignId('offer_id')->nullable()->constrained('offers')->onDelete('set null')->after('rfq_id');
            }
            if (!Schema::hasColumn('purchase_orders', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->after('supplier_id');
            }
            if (!Schema::hasColumn('purchase_orders', 'order_date')) {
                $table->date('order_date')->after('status'); // Assurez-vous que status est déjà là
            }
             if (!Schema::hasColumn('purchase_orders', 'expected_delivery_date_global')) {
                $table->date('expected_delivery_date_global')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'shipping_address')) {
                $table->text('shipping_address')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'billing_address')) {
                $table->text('billing_address')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'payment_terms')) {
                $table->string('payment_terms')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'sent_to_supplier_at')) {
                $table->timestamp('sent_to_supplier_at')->nullable();
            }

            // Mettre à jour la colonne status
            // La migration originale avait 'pending', 'sent', 'partial', 'completed'
            $table->enum('status', [
                PurchaseOrder::STATUS_DRAFT,
                PurchaseOrder::STATUS_PENDING_APPROVAL,
                PurchaseOrder::STATUS_APPROVED,
                PurchaseOrder::STATUS_SENT_TO_SUPPLIER,
                PurchaseOrder::STATUS_ACKNOWLEDGED,
                PurchaseOrder::STATUS_PARTIALLY_DELIVERED,
                PurchaseOrder::STATUS_FULLY_DELIVERED,
                PurchaseOrder::STATUS_COMPLETED,
                PurchaseOrder::STATUS_CANCELLED,
            ])->default(PurchaseOrder::STATUS_DRAFT)->change();
        });

        // Mettre à jour les statuts possibles pour Rfq
        Schema::table('rfqs', function (Blueprint $table) {
            $table->enum('status', [
                Rfq::STATUS_DRAFT,
                Rfq::STATUS_SENT,
                Rfq::STATUS_RECEIVING_OFFERS,
                Rfq::STATUS_PROCESSING_OFFERS,
                Rfq::STATUS_SELECTION_DONE,
                Rfq::STATUS_ORDER_CREATED, // Nouveau
                Rfq::STATUS_CLOSED
            ])->default(Rfq::STATUS_DRAFT)->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Revenir à l'ancien set de statuts si nécessaire (complexe si des données existent)
            // Pour la simplicité, on ne fait pas de rollback complet des colonnes ajoutées ici
            // mais on pourrait les dropper si besoin.
             $table->enum('status', [
                'pending', // Ancien statut
                'sent',    // Ancien statut
                'partial', // Ancien statut
                'completed'// Ancien statut
            ])->default('pending')->change();

            // Exemple de drop de colonnes (à adapter selon ce qui est sûr de faire)
            // $table->dropForeign(['offer_id']);
            // $table->dropColumn('offer_id');
            // $table->dropForeign(['user_id']);
            // $table->dropColumn('user_id');
            // $table->dropColumn(['po_number', 'order_date', ...]);
        });

         Schema::table('rfqs', function (Blueprint $table) {
            // Revenir à l'ancien set de statuts pour Rfq
            $table->enum('status', [
                Rfq::STATUS_DRAFT,
                Rfq::STATUS_SENT,
                Rfq::STATUS_RECEIVING_OFFERS,
                Rfq::STATUS_PROCESSING_OFFERS,
                Rfq::STATUS_SELECTION_DONE,
                Rfq::STATUS_CLOSED
            ])->default(Rfq::STATUS_DRAFT)->change();
        });
    }
};
