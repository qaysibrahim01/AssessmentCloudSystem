<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_approved')) {
                $table->boolean('is_approved')
                    ->default(false)
                    ->after('approval_status');
            }

            if (!Schema::hasColumn('users', 'approval_email_sent_at')) {
                $table->timestamp('approval_email_sent_at')
                    ->nullable()
                    ->after('approved_at');
            }
        });

        // Approve existing users to avoid locking out current accounts
        DB::table('users')->update([
            'is_approved' => true,
            'approval_status' => DB::raw("COALESCE(approval_status, 'approved')"),
            'approved_at' => DB::raw("COALESCE(approved_at, NOW())"),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'approval_email_sent_at')) {
                $table->dropColumn('approval_email_sent_at');
            }
            if (Schema::hasColumn('users', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
        });
    }
};
