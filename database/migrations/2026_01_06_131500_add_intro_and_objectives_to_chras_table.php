<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chras', function (Blueprint $table) {
            if (!Schema::hasColumn('chras', 'business_nature')) {
                $table->string('business_nature')->nullable()->after('company_address');
            }

            if (!Schema::hasColumn('chras', 'assisted_by')) {
                $table->string('assisted_by')->nullable()->after('assessor_name');
            }

            if (!Schema::hasColumn('chras', 'dosh_ref_num')) {
                $table->string('dosh_ref_num')->nullable()->after('assessor_registration_no');
            }

            if (!Schema::hasColumn('chras', 'general_objective')) {
                $table->text('general_objective')->nullable()->after('assessment_objective');
            }

            if (!Schema::hasColumn('chras', 'specified_objectives')) {
                $table->json('specified_objectives')->nullable()->after('general_objective');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chras', function (Blueprint $table) {
            if (Schema::hasColumn('chras', 'business_nature')) {
                $table->dropColumn('business_nature');
            }
            if (Schema::hasColumn('chras', 'assisted_by')) {
                $table->dropColumn('assisted_by');
            }
            if (Schema::hasColumn('chras', 'dosh_ref_num')) {
                $table->dropColumn('dosh_ref_num');
            }
            if (Schema::hasColumn('chras', 'general_objective')) {
                $table->dropColumn('general_objective');
            }
            if (Schema::hasColumn('chras', 'specified_objectives')) {
                $table->dropColumn('specified_objectives');
            }
        });
    }
};
