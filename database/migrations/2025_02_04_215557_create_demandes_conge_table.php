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
        Schema::create('demandes_conge', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demande_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('motif')->nullable();
            $table->boolean('approuvé_responsable')->default(false);
            $table->boolean('approuvé_directeur')->default(false);
            $table->boolean('approuvé_rh')->default(false);
            $table->text('Autre')->nullable();
            $table->text('nom_pdf')->nullable();
            $table->unsignedBigInteger('to_responsable_id')->nullable();
            $table->unsignedBigInteger('to_directeur_id')->nullable();
            $table->timestamps();
    
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes_conge');
    }
};
