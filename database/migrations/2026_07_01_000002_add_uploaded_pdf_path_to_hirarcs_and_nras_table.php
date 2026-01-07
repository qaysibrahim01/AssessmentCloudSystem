<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('hirarcs', function (Blueprint $table) {
            $table->string('uploaded_pdf_path')->nullable()->after('approved_at');
        });

        Schema::table('nras', function (Blueprint $table) {
            $table->string('uploaded_pdf_path')->nullable()->after('approved_at');
        });
    }

    public function down()
    {
        Schema::table('hirarcs', function (Blueprint $table) {
            $table->dropColumn('uploaded_pdf_path');
        });

        Schema::table('nras', function (Blueprint $table) {
            $table->dropColumn('uploaded_pdf_path');
        });
    }
};
