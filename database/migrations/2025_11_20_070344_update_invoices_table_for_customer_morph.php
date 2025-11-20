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
        Schema::table('invoices', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['taxpayer_id']);

            // Drop the taxpayer_id column
            $table->dropColumn('taxpayer_id');

            // Add polymorphic columns for customer
            $table->morphs('customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Drop polymorphic columns
            $table->dropMorphs('customer');

            // Re-add taxpayer_id
            $table->foreignId('taxpayer_id')->nullable()->constrained('taxpayers')->cascadeOnDelete();
        });
    }
};
