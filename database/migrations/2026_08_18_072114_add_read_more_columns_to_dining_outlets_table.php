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
        Schema::table('dining_outlets', function (Blueprint $table) {
            $table->string('read_more_label')->nullable();
            $table->string('read_more_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dining_outlets', function (Blueprint $table) {
            $table->dropColumn(['read_more_label', 'read_more_link']);
        });
    }
};
