<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->enum('type', ['brand', 'hotel'])->index();

            // translatable fields (spatie/laravel-translatable) -> JSON columns
            $table->json('name');
            $table->json('description')->nullable();

            $table->string('slug')->unique(); // flat namespace shared by brand + hotel
            $table->unsignedTinyInteger('star_rating')->nullable();

            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('travelclick_hotel_id')->nullable()->index();
            $table->string('attractions_page_slug')->default('local-attractions');

            $table->enum('status', ['live', 'coming_soon', 'closed'])->default('live');

            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();

            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
