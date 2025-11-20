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
        Schema::table('properties', function (Blueprint $table) {
            // Remove width and length columns
            $table->dropColumn(['width', 'length']);
            
            // Rename possession_date to start_date
            $table->renameColumn('possession_date', 'start_date');
            
            // Add new columns
            $table->foreignId('property_type_id')->nullable()->after('id')->constrained('property_types')->onDelete('set null');
            $table->enum('payment_frequency', ['daily', 'weekly', 'monthly', 'yearly'])->nullable()->after('start_date');
            $table->decimal('amount', 15, 2)->nullable()->after('payment_frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Remove new columns
            $table->dropForeign(['property_type_id']);
            $table->dropColumn(['property_type_id', 'payment_frequency', 'amount']);
            
            // Rename start_date back to possession_date
            $table->renameColumn('start_date', 'possession_date');
            
            // Add back width and length columns
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
        });
    }
};

