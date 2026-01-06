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
        Schema::create('nras', function (Blueprint $table) {
            $table->id();

            $table->string('company_name');
            $table->text('company_address')->nullable();
            $table->string('assessor_name');
            $table->string('assessment_type')->nullable();
            $table->date('assessment_date')->nullable();
            $table->text('assessment_scope')->nullable();

            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nras');
    }
};
