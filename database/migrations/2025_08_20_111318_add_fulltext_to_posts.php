<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only create FULLTEXT on MySQL/MariaDB
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                // Name the index explicitly so we can drop it reliably
                $table->fullText(['title', 'description'], 'posts_title_description_fulltext');
            });
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropFullText('posts_title_description_fulltext');
            });
        }
    }
};
