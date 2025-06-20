<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->string('matricule', 50)->unique();
            $table->string('nom', 100);
            $table->string('prénom', 100);
            $table->string('fonction', 255);
            $table->string('service', 255);
            $table->enum('type', ['ouvrier', 'responsable', 'rh', 'directeur', 'administrateur', 'purchase', 'magasinier', 'comptable'])->default('ouvrier');;
            $table->Double('solde_conge')->default(0);
            $table->string('responsable_hiarchique', 255)->nullable();
            $table->string('directeur', 255)->nullable();
            $table->string('profile_picture', 255)->nullable();
            $table->unsignedBigInteger('shift_id')->nullable(); // Ensure this matches the type of `shifts.id`
            $table->string('projet', 100)->nullable();
            $table->date('date_entrée')->nullable();
            $table->string('affectation', 255)->nullable();
            $table->unsignedBigInteger('equipe_id')->nullable(); // Ensure this matches the type of `equipes.id`

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
