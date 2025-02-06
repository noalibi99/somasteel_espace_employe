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
        Schema::create('deleted_users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('email', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->unsignedInteger('matricule');
            $table->string('nom', 100);
            $table->string('prénom', 100);
            $table->string('fonction', 255);
            $table->string('service', 255)->nullable();
            $table->string('type', 255);
            $table->Double('solde_conge')->default(0);
            $table->string('responsable_hiarchique', 255)->nullable();
            $table->string('directeur', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('deleted_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_users');
    }
};
