<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {

        Schema::table('users', function (Blueprint $table) {

            $table->string('facebook_id')->nullable();

            $table->text('facebook_token')->nullable();

        });

    }

    public function down(): void
    {

        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn('facebook_id');

            $table->dropColumn('facebook_token');

        });

    }

};