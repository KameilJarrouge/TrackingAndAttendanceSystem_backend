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
        Schema::create('cams', function (Blueprint $table) {
            $table->id();
            $table->string('cam_url')->unique();
            $table->string('location');
            $table->integer('type');// 0 classroom, 1 entrance, 2 exit, 3 other
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
        Schema::dropIfExists('cams');
    }
};
