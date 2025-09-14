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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->text('note')->nullable();
            $table->string('name_company')->nullable();
            $table->string('country')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('zip_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('national_number')->nullable();
            $table->string('commercial_registration_number')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('name');
            $table->index('email');
            $table->index('phone');
            $table->index('is_active');
            $table->index('tax_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
