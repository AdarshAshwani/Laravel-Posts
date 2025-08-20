<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('posts')) return;

        Schema::table('posts', function (Blueprint $table) {
            // Add FULLTEXT index on title + description
            $table->fullText(['title', 'description'], 'posts_title_description_fulltext');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('posts')) return;

        Schema::table('posts', function (Blueprint $table) {
            try {
                $table->dropFullText('posts_title_description_fulltext');
            } catch (\Throwable $e) {
                // ignore if index name differs
            }
        });
    }
};
