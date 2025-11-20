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
        Schema::create('revenue_sub_heads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revenue_head_id')->nullable()->constrained('revenue_heads')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_sub_heads');
    }
};
