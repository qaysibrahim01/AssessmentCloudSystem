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
        Schema::create('chra_chemicals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chra_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chra_work_unit_id')->nullable()->constrained()->nullOnDelete();

            $table->string('chemical_name');

            $table->boolean('is_chth')->default(false);
            $table->text('health_hazard')->nullable();
            $table->string('h_code')->nullable();

            $table->boolean('route_inhalation')->default(false);
            $table->boolean('route_dermal')->default(false);
            $table->boolean('route_ingestion')->default(false);

            $table->tinyInteger('hazard_rating')->nullable(); // 1–5

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chra_chemicals');
    }
};
