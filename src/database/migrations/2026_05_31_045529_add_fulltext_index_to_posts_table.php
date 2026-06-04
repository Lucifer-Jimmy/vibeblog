<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE posts ADD FULLTEXT INDEX posts_fulltext (title, content)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE posts DROP INDEX posts_fulltext');
    }
};
