<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsHolidayAcomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_holiday_acom', function (Blueprint $table) {
            $table->bigIncrements('ms_holiday_acom_id');
            $table->string('date_holiday',8);
            $table->string('note_holiday',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_holiday_acom');
    }
}
