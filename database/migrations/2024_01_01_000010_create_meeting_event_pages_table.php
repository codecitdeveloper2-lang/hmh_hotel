<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_event_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete(); // brand OR hotel
            $table->enum('type', ['corporate', 'events', 'weddings', 'rfp', 'conference_room', 'outside_catering']);
            $table->json('title');
            $table->json('description')->nullable();
            $table->json('capacity_details')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_event_pages');
    }
};
