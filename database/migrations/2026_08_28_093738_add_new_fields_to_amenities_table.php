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
        Schema::table('amenities', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->string('read_more_label')->nullable()->after('description');
            $table->string('read_more_link')->nullable()->after('read_more_label');
            $table->string('call_us_no')->nullable()->after('read_more_link');
            $table->json('amenities_list')->nullable()->after('call_us_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amenities', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'read_more_label',
                'read_more_link',
                'call_us_no',
                'amenities_list',
            ]);
        });
    }
};
