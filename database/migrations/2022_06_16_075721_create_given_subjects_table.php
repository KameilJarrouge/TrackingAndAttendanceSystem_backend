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
        Schema::create('given_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('cam_id')->nullable();
            $table->time('time');
            $table->integer('day'); // 0 sun
            $table->string('group')->default("");
            $table->boolean('is_theory');
            $table->integer('attendance_pre')->nullable();
            $table->integer('attendance_post')->nullable();
            $table->integer('attendance_present')->nullable();
            $table->time('attendance_extend')->nullable();
            $table->time('restart_start_time')->nullable();
            $table->integer('restart_duration')->nullable();
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
        Schema::dropIfExists('given_subjects');
    }
};
