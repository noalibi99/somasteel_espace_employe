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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demande_id');
            $table->date('date_demandé');
            $table->string('motif', 255);
            $table->string('piece_jointe', 255)->nullable();
            $table->unsignedInteger('to_responsable_id')->nullable();
            $table->timestamp('approved_at')->useCurrent();
            $table->enum('absence_type', ['Medicale', 'Personnel', 'Formation', 'Autre']);
            $table->timestamps();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
