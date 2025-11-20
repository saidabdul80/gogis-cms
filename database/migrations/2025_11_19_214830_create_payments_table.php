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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->dateTime('payment_date')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2);
            $table->decimal('charges', 15, 2);
            $table->enum('status', ['SUCCESS', 'FAILED', 'PENDING'])->default('PENDING');
            $table->string('gateway');
            $table->string('channel')->nullable();
            $table->string('redirect')->nullable();
            $table->morphs('owner');
            $table->foreignId('revenue_sub_head_id')->nullable()->constrained('revenue_sub_heads')->nullOnDelete();
            $table->foreignId('revenue_head_id')->nullable()->constrained('revenue_heads')->nullOnDelete();
            $table->unsignedBigInteger('revenue_source_id')->nullable();
            $table->string('revenue_source_type')->nullable();
            $table->text('description')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
