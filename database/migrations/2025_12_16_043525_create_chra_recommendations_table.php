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
        Schema::create('chra_recommendations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chra_id')->constrained()->cascadeOnDelete();

            $table->enum('category', ['TC', 'PPE', 'OC', 'ERP', 'Monitoring']);
            $table->text('recommendation');
            $table->enum('action_priority', ['AP-1', 'AP-2', 'AP-3'])->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chra_recommendations');
    }
};
