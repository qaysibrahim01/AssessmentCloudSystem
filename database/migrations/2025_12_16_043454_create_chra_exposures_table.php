<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('chra_exposures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chra_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chra_work_unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chra_chemical_id')->constrained()->cascadeOnDelete();

            // Form A / B / C / D
            $table->enum('exposure_route', [
                'inhalation',
                'dermal',
                'ingestion',
                'combined'
            ]);

            // Exposure parameters
            $table->string('task')->nullable();
            $table->string('exposure_frequency')->nullable();
            $table->string('exposure_duration')->nullable();
            $table->text('existing_control')->nullable();
            $table->enum('control_adequacy', ['adequate', 'inadequate'])->nullable();

            // Exposure rating
            $table->tinyInteger('exposure_rating')->nullable(); // 1–5

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chra_exposures');
    }
};
