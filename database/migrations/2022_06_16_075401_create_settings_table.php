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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->integer('warning_thresh')->nullable();
            $table->integer('suspension_thresh')->nullable();
            $table->integer('attendance_pre')->nullable();
            $table->integer('attendance_post')->nullable();
            $table->integer('attendance_present')->nullable();
            $table->boolean('should_send_sms')->nullable();
            $table->string('sms_number')->nullable();
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
        Schema::dropIfExists('settings');
    }
};
