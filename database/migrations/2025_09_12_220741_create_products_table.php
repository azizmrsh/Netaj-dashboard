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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('product_code')->unique();
            $table->string('performance_grade')->nullable();
            $table->string('modification_type')->nullable();
            $table->enum('unit', ['ton', 'barrel']);
            $table->boolean('is_active')->default(true);
            $table->decimal('price1', 10, 2)->nullable();
            $table->decimal('price2', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
