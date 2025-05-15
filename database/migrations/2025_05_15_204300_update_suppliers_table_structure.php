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
        Schema::table('suppliers', function (Blueprint $table) {
            // Rename 'name' to 'company_name' if it exists
            if (Schema::hasColumn('suppliers', 'name')) {
                $table->renameColumn('name', 'company_name');
            }
            // Add new columns if they don't exist
            if (!Schema::hasColumn('suppliers', 'contact_first_name')) {
                $table->string('contact_first_name')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'contact_last_name')) {
                $table->string('contact_last_name')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'country')) {
                $table->string('country')->nullable();
            }
            // Make sure other columns exist and are nullable if needed
            if (!Schema::hasColumn('suppliers', 'contact_phone')) {
                $table->string('contact_phone')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'contact_email')) {
                $table->string('contact_email')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'company_name')) {
                $table->renameColumn('company_name', 'name');
            }
            if (Schema::hasColumn('suppliers', 'contact_first_name')) {
                $table->dropColumn('contact_first_name');
            }
            if (Schema::hasColumn('suppliers', 'contact_last_name')) {
                $table->dropColumn('contact_last_name');
            }
            if (Schema::hasColumn('suppliers', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('suppliers', 'country')) {
                $table->dropColumn('country');
            }
        });
    }
};
