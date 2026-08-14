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
        Schema::table('properties', function (Blueprint $table) {
            // Hotel-specific fields
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->json('banner_slides')->nullable()->after('is_featured');
            $table->string('cover_image')->nullable()->after('banner_slides');
            $table->string('website')->nullable()->after('cover_image');

            // Brand-specific fields
            $table->string('tagline')->nullable()->after('website');
            $table->string('google_location')->nullable()->after('tagline');
            $table->string('location_title')->nullable()->after('google_location');
            $table->string('contact_button_text')->nullable()->after('location_title');
            $table->string('contact_button_url')->nullable()->after('contact_button_text');
            $table->string('star_segment')->nullable()->after('contact_button_url');
            $table->string('logo')->nullable()->after('star_segment');
            $table->json('intro_text')->nullable()->after('logo');
            $table->string('banner_title')->nullable()->after('intro_text');
            $table->json('banner_images')->nullable()->after('banner_title');

            // Shared fields
            $table->json('intro_subtitle')->nullable()->after('banner_images');
            $table->json('intro_title')->nullable()->after('intro_subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'is_featured',
                'banner_slides',
                'cover_image',
                'website',
                'tagline',
                'google_location',
                'location_title',
                'contact_button_text',
                'contact_button_url',
                'star_segment',
                'logo',
                'intro_text',
                'banner_title',
                'banner_images',
                'intro_subtitle',
                'intro_title'
            ]);
        });
    }
};
