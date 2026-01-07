<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nras', function (Blueprint $table) {
            $table->text('introduction')->nullable();
            $table->text('objectives')->nullable();
            $table->text('process_description')->nullable();
            $table->text('work_activities')->nullable();
            $table->text('work_schedule')->nullable();
            $table->text('work_force')->nullable();
            $table->text('work_unit_description')->nullable();
            $table->text('methodology')->nullable();
            $table->text('instrumentation')->nullable();
            $table->text('area_monitoring')->nullable();
            $table->text('noise_mapping')->nullable();
            $table->text('personal_exposure_monitoring')->nullable();
            $table->text('findings_area')->nullable();
            $table->text('findings_personal')->nullable();
            $table->text('discussion')->nullable();
            $table->text('recommendation')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nras', function (Blueprint $table) {
            $table->dropColumn([
                'introduction',
                'objectives',
                'process_description',
                'work_activities',
                'work_schedule',
                'work_force',
                'work_unit_description',
                'methodology',
                'instrumentation',
                'area_monitoring',
                'noise_mapping',
                'personal_exposure_monitoring',
                'findings_area',
                'findings_personal',
                'discussion',
                'recommendation',
            ]);
        });
    }
};
