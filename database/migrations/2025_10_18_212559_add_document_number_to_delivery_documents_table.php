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
        Schema::table('delivery_documents', function (Blueprint $table) {
            $table->string('document_number')->nullable()->after('id');
            $table->index('document_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_documents', function (Blueprint $table) {
            $table->dropIndex(['document_number']);
            $table->dropColumn('document_number');
        });
    }
};
