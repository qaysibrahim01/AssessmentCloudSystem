<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chra_delete_requests', function (Blueprint $table) {
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users');

            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chra_delete_requests', function (Blueprint $table) {
            //
        });
    }
};
