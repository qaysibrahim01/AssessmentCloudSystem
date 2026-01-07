<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chras', function (Blueprint $table) {
            if (!Schema::hasColumn('chras', 'methodology_team')) {
                $table->text('methodology_team')->nullable()->after('assessment_location');
            }
            if (!Schema::hasColumn('chras', 'methodology_degree_hazard')) {
                $table->text('methodology_degree_hazard')->nullable()->after('methodology_team');
            }
            if (!Schema::hasColumn('chras', 'methodology_assess_exposure')) {
                $table->text('methodology_assess_exposure')->nullable()->after('methodology_degree_hazard');
            }
            if (!Schema::hasColumn('chras', 'methodology_control_adequacy')) {
                $table->text('methodology_control_adequacy')->nullable()->after('methodology_assess_exposure');
            }
            if (!Schema::hasColumn('chras', 'methodology_conclusion')) {
                $table->text('methodology_conclusion')->nullable()->after('methodology_control_adequacy');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chras', function (Blueprint $table) {
            foreach ([
                'methodology_team',
                'methodology_degree_hazard',
                'methodology_assess_exposure',
                'methodology_control_adequacy',
                'methodology_conclusion',
            ] as $col) {
                if (Schema::hasColumn('chras', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
