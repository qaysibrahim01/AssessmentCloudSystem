<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chra_work_units', function (Blueprint $table) {

            // already have male_count + female_count
            // DO NOT add main_task again

            $table->string('exposure_duration')->nullable()
                  ->after('main_task'); // e.g. "4–6 hours/day"
        });
    }

    public function down(): void
    {
        Schema::table('chra_work_units', function (Blueprint $table) {
            $table->dropColumn('exposure_duration');
        });
    }
};
