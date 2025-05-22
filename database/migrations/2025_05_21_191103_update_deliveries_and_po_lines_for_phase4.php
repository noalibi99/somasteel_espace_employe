<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Delivery; // Pour les constantes

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // La migration originale avait purchase_order_id, article_id, quantity_received, status
            // On va adapter : article_id et quantity_received passent à DeliveryLine.

            if (Schema::hasColumn('deliveries', 'article_id')) {
                // Il faut dropper la FK avant la colonne
                // $table->dropForeign(['article_id']); // Nom conventionnel: deliveries_article_id_foreign
                try { $table->dropForeign(['article_id']); } catch (\Exception $e) {}
                $table->dropColumn('article_id');
            }
            if (Schema::hasColumn('deliveries', 'quantity_received')) {
                $table->dropColumn('quantity_received');
            }

            // Ajouter les nouvelles colonnes si elles n'existent pas
            if (!Schema::hasColumn('deliveries', 'delivery_reference')) {
                $table->string('delivery_reference')->nullable()->after('purchase_order_id'); // BL Fournisseur
            }
            if (!Schema::hasColumn('deliveries', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('delivery_reference');
            }
            if (!Schema::hasColumn('deliveries', 'received_by_id')) {
                $table->foreignId('received_by_id')->nullable()->constrained('users')->onDelete('set null')->after('delivery_date');
            }
             if (!Schema::hasColumn('deliveries', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }


            // Mettre à jour la colonne status
            // L'original avait 'pending', 'partial', 'completed'
            $table->enum('status', [
                Delivery::STATUS_PENDING_CONFIRMATION,
                Delivery::STATUS_PARTIALLY_RECEIVED,
                Delivery::STATUS_FULLY_RECEIVED,
                Delivery::STATUS_COMPLETED_WITH_ISSUES,
            ])->default(Delivery::STATUS_PENDING_CONFIRMATION)->change();
        });

        Schema::table('purchase_order_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_lines', 'quantity_received')) {
                $table->integer('quantity_received')->default(0)->after('quantity_ordered');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // Revenir à l'ancien schéma (simplifié)
            $table->enum('status', ['pending', 'partial', 'completed'])->default('pending')->change();
            // $table->dropColumn(['delivery_reference', 'delivery_date', 'notes']);
            // $table->dropForeign(['received_by_id']);
            // $table->dropColumn('received_by_id');
            // Recréer les anciennes colonnes (si nécessaire pour rollback complet)
            // $table->foreignId('article_id')->constrained('articles');
            // $table->integer('quantity_received');
        });

        Schema::table('purchase_order_lines', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_order_lines', 'quantity_received')) {
                $table->dropColumn('quantity_received');
            }
        });
    }
};
