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
        Schema::table('offers', function (Blueprint $table) {
            $table->string('hotel')->nullable();
            $table->string('offer_type')->nullable();
            $table->string('status')->default('Active');
            $table->string('booking_period')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn([
                'hotel',
                'offer_type',
                'status',
                'booking_period',
                'banner_image',
                'meta_title',
                'meta_description',
                'meta_keywords',
            ]);
        });
    }
};
