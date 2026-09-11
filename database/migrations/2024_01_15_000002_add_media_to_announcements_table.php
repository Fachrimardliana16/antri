<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('announcements')) {
            return;
        }

        Schema::table('announcements', function (Blueprint $table) {
            if (! Schema::hasColumn('announcements', 'media_type')) {
                $table->enum('media_type', ['text', 'image', 'video', 'youtube'])->default('text')->after('content');
            }

            if (! Schema::hasColumn('announcements', 'media_path')) {
                $table->string('media_path')->nullable()->after('media_type');
            }

            if (! Schema::hasColumn('announcements', 'youtube_url')) {
                $table->string('youtube_url')->nullable()->after('media_path');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('announcements')) {
            return;
        }

        Schema::table('announcements', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('announcements', 'media_type')) {
                $dropColumns[] = 'media_type';
            }

            if (Schema::hasColumn('announcements', 'media_path')) {
                $dropColumns[] = 'media_path';
            }

            if (Schema::hasColumn('announcements', 'youtube_url')) {
                $dropColumns[] = 'youtube_url';
            }

            if (! empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
