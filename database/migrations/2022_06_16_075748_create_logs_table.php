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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cam_id')->constrained('cams')->cascadeOnDelete();
            $table->integer('person_id')->nullable();
            $table->timestamp('timestamp');
            $table->boolean('unidentified');
            $table->boolean('ignore');
            $table->string('verification_img')->default("");
            $table->boolean('warning_flag');
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
        Schema::dropIfExists('logs');
    }
};
