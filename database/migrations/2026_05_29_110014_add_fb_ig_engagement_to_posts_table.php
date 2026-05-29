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
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('fb_likes_count')->default(0)->after('comments_count');
            $table->unsignedBigInteger('fb_comments_count')->default(0)->after('fb_likes_count');
            $table->unsignedBigInteger('ig_likes_count')->default(0)->after('fb_comments_count');
            $table->unsignedBigInteger('ig_comments_count')->default(0)->after('ig_likes_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['fb_likes_count', 'fb_comments_count', 'ig_likes_count', 'ig_comments_count']);
        });
    }
};
