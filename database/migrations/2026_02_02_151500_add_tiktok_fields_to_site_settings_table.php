<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('tiktok_url')->nullable()->after('instagram_url');
            $table->boolean('tiktok_enabled')->default(true)->after('instagram_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['tiktok_url', 'tiktok_enabled']);
        });
    }
};
