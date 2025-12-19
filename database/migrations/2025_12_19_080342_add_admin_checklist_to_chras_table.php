<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chras', function (Blueprint $table) {

            $table->boolean('admin_checked_sections')->default(false);
            $table->boolean('admin_checked_chemicals')->default(false);
            $table->boolean('admin_checked_risk')->default(false);
            $table->boolean('admin_checked_recommendations')->default(false);
            $table->boolean('admin_checked_conclusion')->default(false);

            $table->text('admin_notes')->nullable();
        });
    }

    public function down()
    {
        Schema::table('chras', function (Blueprint $table) {
            $table->dropColumn([
                'admin_checked_sections',
                'admin_checked_chemicals',
                'admin_checked_risk',
                'admin_checked_recommendations',
                'admin_checked_conclusion',
                'admin_notes',
            ]);
        });
    }
};
