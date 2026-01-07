<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('hirarcs', function (Blueprint $table) {
            $table->enum('source', ['system', 'uploaded'])
                ->default('system')
                ->after('status');
        });

        Schema::table('nras', function (Blueprint $table) {
            $table->enum('source', ['system', 'uploaded'])
                ->default('system')
                ->after('status');
        });
    }

    public function down()
    {
        Schema::table('hirarcs', function (Blueprint $table) {
            $table->dropColumn('source');
        });

        Schema::table('nras', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
