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
        Schema::create('prof_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('given_subject_id')->constrained('given_subjects')->cascadeOnDelete();
            $table->integer('week');
            $table->boolean('attended')->default(false);
            $table->boolean('visited')->default(false);

            $table->string('verification_img')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->boolean('skipped')->default(false);
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
        Schema::dropIfExists('prof_attendances');
    }
};
