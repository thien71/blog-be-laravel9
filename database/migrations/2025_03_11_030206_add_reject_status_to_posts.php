<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'pending', 'published', 'hidden', 'rejected') NOT NULL DEFAULT 'draft'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'pending', 'published', 'hidden') NOT NULL DEFAULT 'draft'");
    }
};
