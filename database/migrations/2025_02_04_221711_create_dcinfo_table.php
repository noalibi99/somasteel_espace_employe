<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW dcinfo AS
            SELECT u.id AS user_id, u.nom, u.prénom, u.solde_conge AS solde_rest, d.created_at AS dcreated_at, d.status, d.raison_refus, dc.id, d.id AS d_id, dc.start_date, dc.end_date, to_days(dc.end_date) - to_days(dc.start_date) AS nj_decompter, dc.motif, dc.approuvé_responsable AS v_resp, dc.approuvé_directeur AS v_dir, dc.approuvé_rh AS v_rh, dc.Autre AS autre, dc.to_responsable_id AS to_resp, dc.nom_pdf, dc.to_directeur_id AS to_dir
            FROM demandes d
            JOIN demandes_conge dc ON d.id = dc.demande_id
            JOIN users u ON d.user_id = u.id
            ORDER BY d.created_at ASC
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dcinfo');
    }
};
