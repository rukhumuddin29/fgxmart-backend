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
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            
            // Content
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('image_path')->nullable();
            $table->string('background_color')->default('#F9F7F2');

            // Title Styling
            $table->string('title_font_size')->default('48px');
            $table->string('title_font_weight')->default('800');
            $table->string('title_color')->default('#0E4B4C');
            $table->string('title_font_family')->default('inherit');

            // Subtitle Styling
            $table->string('subtitle_font_size')->default('17px');
            $table->string('subtitle_font_weight')->default('400');
            $table->string('subtitle_color')->default('#0E4B4C');
            $table->string('subtitle_font_family')->default('inherit');

            // CTA Styling
            $table->string('cta_bg_color')->default('#0E4B4C');
            $table->string('cta_text_color')->default('#FFFFFF');
            $table->string('cta_font_size')->default('16px');
            $table->string('cta_font_weight')->default('700');

            // Image Styling/Positioning
            $table->string('image_object_fit')->default('cover');
            $table->string('image_object_position')->default('bottom center');

            // Display Logic
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
