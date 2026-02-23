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
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained()->onDelete('cascade');
            $table->decimal('price_adjustment', 15, 2)->default(0)->comment('Added/Subtracted from base price');
            $table->decimal('price', 15, 2)->nullable()->comment('Override base price if set');
            $table->integer('stock')->default(0);
            $table->string('sku')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['product_id', 'attribute_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};
