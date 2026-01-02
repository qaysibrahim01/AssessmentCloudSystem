<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('chras', function (Blueprint $table) {
            $table->enum('source', ['system', 'uploaded'])
                  ->default('system')
                  ->after('uploaded_pdf_path');
        });
    }

    public function down()
    {
        Schema::table('chras', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
