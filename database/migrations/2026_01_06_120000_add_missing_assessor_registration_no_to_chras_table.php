<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('chras', 'assessor_registration_no')) {
            return;
        }

        Schema::table('chras', function (Blueprint $table) {
            $table->string('assessor_registration_no')
                ->nullable()
                ->after('assessor_name');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('chras', 'assessor_registration_no')) {
            return;
        }

        Schema::table('chras', function (Blueprint $table) {
            $table->dropColumn('assessor_registration_no');
        });
    }
};
