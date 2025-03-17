<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsHolidayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_holiday', function (Blueprint $table) {
            $table->bigIncrements('ms_holiday_id');
            $table->string('date_holiday',8);
            $table->string('time_holiday',4)->nullable();
            $table->string('type_holiday',1)->nullable();
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
        Schema::dropIfExists('ms_holiday');
    }
}
