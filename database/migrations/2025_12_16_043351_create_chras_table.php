<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chras', function (Blueprint $table) {
            $table->id();

            // Company & assessor info
            $table->string('company_name');
            $table->text('company_address')->nullable();
            $table->string('assessor_name');
            $table->date('assessment_date');
            $table->date('completion_date')->nullable();

            // Section A
            $table->text('assessment_objective')->nullable();
            $table->string('assessment_type')->nullable(); // Initial / Re-assessment
            $table->string('assessment_location')->nullable();

            // Section B
            $table->text('process_description')->nullable();
            $table->text('work_activities')->nullable();
            $table->text('chemical_usage_areas')->nullable();

            // Section G (Assessor)
            $table->string('overall_risk_profile')->nullable(); // Low / Moderate / High
            $table->text('assessor_conclusion')->nullable();
            $table->string('implementation_timeframe')->nullable();

            // Status & admin decision
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->text('admin_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // Owner
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chras');
    }
};
