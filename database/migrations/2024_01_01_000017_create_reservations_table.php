<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete(); // guest bookings allowed
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete(); // hotel-type row
            $table->string('confirmation_number')->unique();
            $table->string('travelclick_reservation_id');
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedTinyInteger('adults')->default(1);
            $table->unsignedTinyInteger('children')->default(0);
            $table->unsignedTinyInteger('rooms')->default(1);
            $table->string('rate_plan_id')->nullable();
            $table->enum('status', ['confirmed', 'modified', 'cancelled'])->default('confirmed');
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->json('raw_payload')->nullable(); // full booking-engine response, for support/debugging
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
