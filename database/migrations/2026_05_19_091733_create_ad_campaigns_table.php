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
        Schema::create('ad_campaigns', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('facebook_page_id')->nullable()->constrained('facebook_pages')->onDelete('set null');
            $blueprint->string('campaign_id')->nullable(); // Facebook Campaign ID if live
            $blueprint->string('name');
            $blueprint->string('objective'); // LINK_CLICKS, PAGE_LIKES, BRAND_AWARENESS
            $blueprint->decimal('daily_budget', 10, 2);
            $blueprint->string('status')->default('ACTIVE'); // ACTIVE, PAUSED, COMPLETED
            
            // Stats (supports mock simulation + live sync)
            $blueprint->integer('clicks')->default(0);
            $blueprint->integer('impressions')->default(0);
            $blueprint->decimal('spend', 10, 2)->default(0.00);
            $blueprint->decimal('ctr', 5, 2)->default(0.00);
            
            // Targeting
            $blueprint->string('target_location')->default('Worldwide');
            $blueprint->integer('target_age_min')->default(18);
            $blueprint->integer('target_age_max')->default(65);
            
            // Ad Creative
            $blueprint->text('ad_text')->nullable();
            $blueprint->string('ad_image')->nullable();
            
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_campaigns');
    }
};
