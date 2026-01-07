<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hirarcs', function (Blueprint $table) {
            $table->text('introduction')->nullable();
            $table->text('objectives')->nullable();
            $table->text('process_description')->nullable();
            $table->text('work_activities')->nullable();
            $table->text('work_schedule')->nullable();
            $table->text('work_force')->nullable();
            $table->text('work_unit_description')->nullable();
            $table->text('hazard_identification')->nullable();
            $table->text('risk_assessment')->nullable();
            $table->text('discussion')->nullable();
            $table->text('recommendation')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('hirarcs', function (Blueprint $table) {
            $table->dropColumn([
                'introduction',
                'objectives',
                'process_description',
                'work_activities',
                'work_schedule',
                'work_force',
                'work_unit_description',
                'hazard_identification',
                'risk_assessment',
                'discussion',
                'recommendation',
            ]);
        });
    }
};
