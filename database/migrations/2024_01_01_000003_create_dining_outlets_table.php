<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dining_outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete(); // brand OR hotel
            $table->json('name');
            $table->json('description')->nullable();
            $table->string('slug');
            $table->string('cuisine_type')->nullable();
            $table->string('opening_hours')->nullable();
            $table->boolean('has_table_booking')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['property_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_outlets');
    }
};
