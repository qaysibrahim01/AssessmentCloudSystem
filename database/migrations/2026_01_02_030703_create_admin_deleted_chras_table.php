<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_deleted_chras', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('chra_id');
            $table->string('company_name');

            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('deleted_by');

            $table->text('reason')->nullable();
            $table->timestamp('deleted_at');

            $table->timestamps();

            // optional FK safety
            $table->foreign('requested_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_deleted_chras');
    }
};
