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
        Schema::table('individual_customers', function (Blueprint $table) {
            if (!Schema::hasColumn('individual_customers', 'nin')) {
                $table->string('nin')->unique()->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('individual_customers', 'gender')) {
                $table->enum('gender', ['Male', 'Female'])->nullable()->after('nin');
            }
            if (!Schema::hasColumn('individual_customers', 'dob')) {
                $table->date('dob')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('individual_customers', 'lga_id')) {
                $table->foreignId('lga_id')->nullable()->after('dob')->constrained('lgas')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('individual_customers', function (Blueprint $table) {
            $table->dropForeign(['lga_id']);
            $table->dropColumn(['nin', 'gender', 'dob', 'lga_id']);
        });
    }
};
