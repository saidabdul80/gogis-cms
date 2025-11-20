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
            $table->unsignedBigInteger('giras_invoice_id')->nullable()->after('invoice_type_id');
            $table->string('giras_invoice_number')->nullable()->after('giras_invoice_id');
            $table->string('giras_reference')->nullable()->after('giras_invoice_number');
            $table->text('giras_payment_url')->nullable()->after('giras_reference');
            $table->string('giras_gateway')->nullable()->after('giras_payment_url');
            $table->json('giras_response')->nullable()->after('giras_gateway');
            $table->timestamp('giras_synced_at')->nullable()->after('giras_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'giras_invoice_id',
                'giras_invoice_number',
                'giras_reference',
                'giras_payment_url',
                'giras_gateway',
                'giras_response',
                'giras_synced_at'
            ]);
        });
    }
};
