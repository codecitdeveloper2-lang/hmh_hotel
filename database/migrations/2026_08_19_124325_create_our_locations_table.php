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
        Schema::create('our_locations', function (Blueprint $table) {
            $table->id();
            $table->string('city_name');
            $table->unsignedBigInteger('destination_id')->nullable();
            $table->text('home_teaser')->nullable();
            $table->string('home_image')->nullable();
            $table->boolean('featured_on_home')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('our_locations');
    }
};
