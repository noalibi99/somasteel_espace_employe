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
        Schema::create('update_serv_fct', function (Blueprint $table) {
            $table->string('matricule', 50)->primary();
            $table->string('fonction', 255);
            $table->string('service', 255);
            $table->string('projet', 100)->nullable();
            $table->date('date_entrée')->nullable();
            $table->string('affectation', 255)->nullable();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_serv_fct');
    }
};
