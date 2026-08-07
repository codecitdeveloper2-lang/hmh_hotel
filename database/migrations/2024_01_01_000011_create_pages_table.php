<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('properties')->cascadeOnDelete(); // null = group-level
            $table->enum('page_type', [
                'about', 'careers', 'best-rate-guarantee', 'sustainability',
                'accessibility', 'terms-conditions', 'privacy-statement', 'newsletter', 'custom',
            ])->index();
            $table->string('slug');
            $table->json('title');
            $table->json('body')->nullable();
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // slug unique per property (group-level pages share the null property_id "namespace")
            $table->unique(['property_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
