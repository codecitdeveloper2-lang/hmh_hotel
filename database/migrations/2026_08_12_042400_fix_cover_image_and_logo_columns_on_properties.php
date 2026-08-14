<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Revert cover_image and logo back to string columns
        Schema::table('properties', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->change();
            $table->string('logo')->nullable()->change();
        });

        // Fix any double-encoded JSON data in cover_image
        $properties = DB::table('properties')->whereNotNull('cover_image')->get();
        foreach ($properties as $property) {
            $value = $property->cover_image;
            // If it's a JSON-encoded string (e.g., "\"filename.png\""), decode it
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) {
                DB::table('properties')
                    ->where('id', $property->id)
                    ->update(['cover_image' => $decoded]);
            }
        }

        // Fix any double-encoded JSON data in logo
        $properties = DB::table('properties')->whereNotNull('logo')->get();
        foreach ($properties as $property) {
            $value = $property->logo;
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) {
                DB::table('properties')
                    ->where('id', $property->id)
                    ->update(['logo' => $decoded]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->json('cover_image')->nullable()->change();
            $table->json('logo')->nullable()->change();
        });
    }
};
