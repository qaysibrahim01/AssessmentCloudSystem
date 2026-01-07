<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hirarcs', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_reason')->nullable();
        });

        Schema::table('nras', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('hirarcs', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'approved_at', 'admin_reason']);
        });
        Schema::table('nras', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'approved_at', 'admin_reason']);
        });
    }
};
