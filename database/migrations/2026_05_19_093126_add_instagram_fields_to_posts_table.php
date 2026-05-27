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
            if (!Schema::hasColumn('posts', 'post_to_facebook')) {
                $table->boolean('post_to_facebook')->default(true)->after('facebook_page_id');
            }
            if (!Schema::hasColumn('posts', 'post_to_instagram')) {
                $table->boolean('post_to_instagram')->default(false)->after('post_to_facebook');
            }
            if (!Schema::hasColumn('posts', 'instagram_status')) {
                $table->string('instagram_status')->default('pending')->after('status');
            }
            if (!Schema::hasColumn('posts', 'instagram_post_id')) {
                $table->string('instagram_post_id')->nullable()->after('facebook_post_id');
            }
            if (!Schema::hasColumn('posts', 'instagram_error')) {
                $table->text('instagram_error')->nullable()->after('instagram_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('posts', 'post_to_facebook')) $cols[] = 'post_to_facebook';
            if (Schema::hasColumn('posts', 'post_to_instagram')) $cols[] = 'post_to_instagram';
            if (Schema::hasColumn('posts', 'instagram_status')) $cols[] = 'instagram_status';
            if (Schema::hasColumn('posts', 'instagram_post_id')) $cols[] = 'instagram_post_id';
            if (Schema::hasColumn('posts', 'instagram_error')) $cols[] = 'instagram_error';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
