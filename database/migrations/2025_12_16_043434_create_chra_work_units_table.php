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
        Schema::create('chra_work_units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chra_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('work_area');

            $table->integer('male_count')->nullable();
            $table->integer('female_count')->nullable();

            $table->text('main_task')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chra_work_units');
    }
};
