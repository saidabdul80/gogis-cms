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
        Schema::create('invoice_types', function (Blueprint $table) {
            $table->id();
            $table->string('template')->nullable();
            $table->string('currency')->default('NGN');
            $table->enum('region_type', ['STATE', 'LGA', 'FEDERAL', 'PRIVATE']);
            $table->enum('frequency', ['ONE_TIME', 'YEARLY', 'MONTHLY', 'WEEKLY']);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('revenue_sub_head_id')->constrained('revenue_sub_heads')->cascadeOnDelete();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_types');
    }
};
