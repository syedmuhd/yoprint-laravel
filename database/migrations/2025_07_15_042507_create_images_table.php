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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('brand_logo_image')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->string('color_swatch_image')->nullable();
            $table->string('product_image')->nullable();
            $table->string('name')->nullable();
            $table->string('color_square_image')->nullable();
            $table->string('color_product_image')->nullable();
            $table->string('color_product_image_thumbnail')->nullable();
            $table->string('front_model_image_url')->nullable();
            $table->string('back_model_image')->nullable();
            $table->string('front_flat_image')->nullable();
            $table->string('back_flat_image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
