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
        Schema::table('cities', function (Blueprint $table) {
            $table->string('city_image')->nullable()->after('slug');
            $table->string('city_link')->nullable()->after('city_image');
            $table->string('layout_type')->nullable()->after('city_link');
            $table->integer('sort_order')->default(0)->after('layout_type');
            $table->boolean('is_active')->default(true)->after('sort_order');
            $table->json('hotel_labels')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['city_image', 'city_link', 'layout_type', 'sort_order', 'is_active', 'hotel_labels']);
        });
    }
};
