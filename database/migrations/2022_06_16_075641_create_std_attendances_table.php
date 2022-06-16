<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('std_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taken_subject_id')->constrained('taken_subjects')->cascadeOnDelete();
            $table->integer('week');
            $table->boolean('attended');
            $table->string('verification_img');
            $table->timestamp('timestamp');
            $table->boolean('skipped');
            $table->boolean('theory');
            $table->boolean('present');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('std_attendances');
    }
};
