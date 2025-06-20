<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\RFQ; // Importer pour les constantes de statut

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rfqs', function (Blueprint $table) {
            // Ajouter la colonne pour l'offre sélectionnée
            $table->foreignId('selected_offer_id')->nullable()->constrained('offers')->onDelete('set null');

            // Mettre à jour les statuts possibles pour RFQ
            $table->enum('status', [
                RFQ::STATUS_DRAFT,
                RFQ::STATUS_SENT,
                RFQ::STATUS_RECEIVING_OFFERS, // Nouveau
                RFQ::STATUS_PROCESSING_OFFERS,
                RFQ::STATUS_SELECTION_DONE, // Nouveau
                RFQ::STATUS_CLOSED
            ])->default(RFQ::STATUS_DRAFT)->change();
        });

        // Mettre à jour la table offers (la migration originale avait price et terms, on ajoute valid_until, notes, attachment)
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'valid_until')) {
                $table->date('valid_until')->nullable()->after('terms');
            }
            if (!Schema::hasColumn('offers', 'notes')) {
                $table->text('notes')->nullable()->after('valid_until');
            }
            if (!Schema::hasColumn('offers', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('notes');
            }
            // Si vous décidez d'ajouter is_selected directement sur l'offre
            // if (!Schema::hasColumn('offers', 'is_selected')) {
            //     $table->boolean('is_selected')->default(false)->after('attachment_path');
            // }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rfqs', function (Blueprint $table) {
            // Attention: la suppression d'une contrainte FK nécessite de connaître son nom
            // Laravel génère souvent des noms comme: tableName_columnName_foreign
            // Il est plus sûr de le faire manuellement ou avec une convention de nommage connue.
            // Pour cet exemple, on suppose qu'on peut dropper la colonne.
            if (Schema::hasColumn('rfqs', 'selected_offer_id')) {
                 // Il faut d'abord dropper la contrainte étrangère avant la colonne
                // Le nom de la contrainte est typiquement 'rfqs_selected_offer_id_foreign'
                // $table->dropForeign(['selected_offer_id']); // Ou $table->dropForeign('rfqs_selected_offer_id_foreign');
                // Pour simplifier ici, on va supposer que la suppression de colonne fonctionne sans dropper explicitement la FK
                // mais en production, il faut être précis.
                // Une meilleure approche est de dropper la colonne uniquement si la FK peut être gérée,
                // ou de la laisser et de gérer la logique applicative.
                // Pour l'exercice, on tente de la dropper:
                 try {
                    $table->dropForeign(['selected_offer_id']);
                } catch (\Exception $e) {
                    // Silencieux si la contrainte n'existe pas ou ne peut être droppée facilement
                }
                $table->dropColumn('selected_offer_id');
            }


            // Revenir à l'ancien set de statuts pour rfqs
            $table->enum('status', [
                RFQ::STATUS_DRAFT,
                RFQ::STATUS_SENT,
                RFQ::STATUS_PROCESSING_OFFERS,
                RFQ::STATUS_CLOSED
            ])->default(RFQ::STATUS_DRAFT)->change();
        });

        Schema::table('offers', function (Blueprint $table) {
            if (Schema::hasColumn('offers', 'attachment_path')) {
                $table->dropColumn('attachment_path');
            }
            if (Schema::hasColumn('offers', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('offers', 'valid_until')) {
                $table->dropColumn('valid_until');
            }
            // if (Schema::hasColumn('offers', 'is_selected')) {
            //     $table->dropColumn('is_selected');
            // }
        });
    }
};
