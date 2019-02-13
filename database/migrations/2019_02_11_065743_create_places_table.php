<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('seat_type');
            $table->integer('priviledge');
            $table->string('label', 100);
            $table->string('qr_code', 255);
            $table->string('remark', 255);
            $table->integer('reserve_staff_id');
            $table->dateTime('reserve_expire');
            $table->integer('checkin_staff_id');
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('places');
    }
}
