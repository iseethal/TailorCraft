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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_color_id')->default(0)->constrained('product_colors')->onDelete('cascade'); 
            $table->foreignId('product_variant_id')->default(0)->constrained('product_variants')->onDelete('cascade');
            $table->string('image_path')->nullable(); // image path or url
            $table->tinyInteger('is_primary')->default(0);
            $table->unsignedInteger('position')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
