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
        Schema::table('facebook_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('facebook_pages', 'instagram_account_id')) {
                $table->string('instagram_account_id')->nullable()->after('access_token');
            }
            if (!Schema::hasColumn('facebook_pages', 'instagram_username')) {
                $table->string('instagram_username')->nullable()->after('instagram_account_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facebook_pages', function (Blueprint $table) {
            $table->dropColumn(['instagram_account_id', 'instagram_username']);
        });
    }
};
