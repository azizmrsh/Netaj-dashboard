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
        Schema::table('transporters', function (Blueprint $table) {
            $table->foreignId('transport_company_id')->nullable()->after('id')->constrained('transport_companies')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transporters', function (Blueprint $table) {
            $table->dropForeign(['transport_company_id']);
            $table->dropColumn('transport_company_id');
        });
    }
};
