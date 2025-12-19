<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('chra_risk_evaluations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chra_exposure_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->tinyInteger('hazard_rating');
            $table->tinyInteger('exposure_rating');
            $table->tinyInteger('risk_score');

            $table->enum('risk_level', ['low', 'moderate', 'high']);
            $table->enum('action_priority', ['AP-1', 'AP-2', 'AP-3']);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chra_risk_evaluations');
    }
};
