<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('properties')->cascadeOnDelete(); // null = group-level
            $table->json('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            // actual image file lives in the `media` table via spatie/laravel-medialibrary
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};
