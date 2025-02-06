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
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
            $table->foreign('equipe_id')->references('id')->on('equipes')->onDelete('set null');
        });
    
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
        });
    
        Schema::table('demandes', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    
        Schema::table('demandes_conge', function (Blueprint $table) {
            $table->foreign('demande_id')->references('id')->on('demandes')->onDelete('cascade');
        });
    
        Schema::table('absences', function (Blueprint $table) {
            $table->foreign('demande_id')->references('id')->on('demandes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
