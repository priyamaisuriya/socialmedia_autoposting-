<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('facebook_page_id')
                ->nullable()
                ->constrained('facebook_pages')
                ->onDelete('cascade');

            $table->text('message');

            $table->string('image')->nullable();

            $table->string('status')->default('pending'); 
            // pending | success | failed

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};