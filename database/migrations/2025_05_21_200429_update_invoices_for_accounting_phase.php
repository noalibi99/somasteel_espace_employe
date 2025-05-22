<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Invoice; // Pour les constantes

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // La migration originale avait purchase_order_id, supplier_id, amount, status, document_path
            // Renommer 'amount' en 'total_amount' si c'est ce qu'il représentait
            // if (Schema::hasColumn('invoices', 'amount') && !Schema::hasColumn('invoices', 'total_amount')) {
            //     $table->renameColumn('amount', 'total_amount');
            // }

            // if (Schema::hasColumn('invoices', 'amount') && !Schema::hasColumn('invoices', 'total_amount')) {
            //     $table->renameColumn('amount', 'total_amount');
            // }

            // Ajouter les nouvelles colonnes
            if (!Schema::hasColumn('invoices', 'invoice_number')) {
                $table->string('invoice_number')->after('supplier_id'); // Ou après 'purchase_order_id'
            }
            if (!Schema::hasColumn('invoices', 'invoice_date')) {
                $table->date('invoice_date')->nullable()->after('invoice_number');
            }
            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable()->after('invoice_date');
            }
            if (!Schema::hasColumn('invoices', 'amount_ht')) {
                $table->decimal('amount_ht', 10, 2)->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('invoices', 'vat_amount')) {
                $table->decimal('vat_amount', 10, 2)->nullable()->after('amount_ht');
            }
            // S'assurer que total_amount est bien decimal si on le recrée (ou le modifie)
            if (Schema::hasColumn('invoices', 'total_amount')) {
                 $table->decimal('total_amount', 10, 2)->change(); // Assurer le type
            } else {
                 $table->decimal('total_amount', 10, 2)->after('vat_amount');
            }


            if (!Schema::hasColumn('invoices', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('invoices', 'payment_date')) {
                $table->timestamp('payment_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_date');
            }
            if (!Schema::hasColumn('invoices', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('invoices', 'notes')) {
                $table->text('notes')->nullable()->after('payment_reference');
            }
             if (!Schema::hasColumn('invoices', 'payment_document_path')) {
                $table->string('payment_document_path')->nullable()->after('document_path');
            }
            if (!Schema::hasColumn('invoices', 'validated_by_id')) {
                $table->foreignId('validated_by_id')->nullable()->constrained('users')->onDelete('set null')->after('payment_document_path');
            }
            if (!Schema::hasColumn('invoices', 'validated_at')) {
                $table->timestamp('validated_at')->nullable()->after('validated_by_id');
            }


            // Mettre à jour la colonne status
            // La migration originale avait 'unpaid', 'paid'
            $table->enum('status', [
                Invoice::STATUS_PENDING_VALIDATION,
                Invoice::STATUS_VALIDATED,
                Invoice::STATUS_PARTIALLY_PAID,
                Invoice::STATUS_PAID,
                Invoice::STATUS_REJECTED,
                Invoice::STATUS_CANCELLED,
            ])->default(Invoice::STATUS_PENDING_VALIDATION)->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Revenir à l'ancien set de statuts si nécessaire
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid')->change();
            // Dropper les colonnes ajoutées (exemple)
            // $table->dropColumn(['invoice_number', 'invoice_date', ...]);
            // if (Schema::hasColumn('invoices', 'total_amount') && !Schema::hasColumn('invoices', 'amount')) {
            //     $table->renameColumn('total_amount', 'amount');
            // }
        });
    }
};
