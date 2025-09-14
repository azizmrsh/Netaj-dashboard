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
        Schema::create('transporters', function (Blueprint $table) {
            $table->id();
            
            // Required columns
            $table->string('name');
            $table->string('phone');
            
            // Optional columns
            $table->string('email')->nullable();
            $table->text('note')->nullable();
            $table->text('id_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('document_no')->nullable();
            $table->string('car_no')->nullable();
            
            // Default values
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('name');
            $table->index('phone');
            $table->index('email');
            $table->index('is_active');
            $table->index('tax_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transporters');
    }
};
