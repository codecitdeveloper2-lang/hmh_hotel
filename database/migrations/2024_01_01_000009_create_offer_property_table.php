<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_property', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete(); // brand OR hotel
            $table->string('travelclick_rate_plan_id')->nullable(); // hotel-level assignments only
            $table->timestamps();

            $table->unique(['offer_id', 'property_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_property');
    }
};
