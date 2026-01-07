<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chras')) {
            DB::statement('ALTER TABLE chras ALTER COLUMN assessment_location TYPE text');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chras')) {
            DB::statement('ALTER TABLE chras ALTER COLUMN assessment_location TYPE varchar(255)');
        }
    }
};
