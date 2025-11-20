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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('charges', 15, 2)->default(0);
            $table->string('currency')->default('NGN');
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->foreignId('taxpayer_id')->constrained('taxpayers')->cascadeOnDelete();
            $table->foreignId('issuer_id')->constrained('admins')->cascadeOnDelete();
            $table->enum('payment_status', ['PENDING', 'PARTIAL', 'PAID', 'FAILED', 'CANCELLED'])->default('PENDING');
            $table->string('template')->nullable();
            $table->json('variables')->nullable();
            $table->text('description')->nullable();
            $table->integer('download_count')->default(0);
            $table->foreignId('invoice_type_id')->nullable()->constrained('invoice_types')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
