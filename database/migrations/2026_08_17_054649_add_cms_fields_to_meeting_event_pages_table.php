<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meeting_event_pages', function (Blueprint $table) {
            $table->string('subtitle')->nullable();
            $table->string('rfp_url')->nullable();
            $table->json('banner_slides')->nullable();
            $table->json('event_cards')->nullable();
            $table->json('gallery')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_event_pages', function (Blueprint $table) {
            $table->dropColumn([
                'subtitle',
                'rfp_url',
                'banner_slides',
                'event_cards',
                'gallery',
            ]);
        });
    }
};
