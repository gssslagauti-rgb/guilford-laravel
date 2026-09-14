<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_posts', function (Blueprint $table) {
            $table->id();
            $table->string('source_page');        // e.g. town.guilford.ct
            $table->text('original_text');         // raw Facebook post
            $table->text('summary')->nullable();   // Claude-generated article
            $table->string('post_url')->nullable();
            $table->date('post_date')->nullable();
            $table->string('added_by')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_posts');
    }
};