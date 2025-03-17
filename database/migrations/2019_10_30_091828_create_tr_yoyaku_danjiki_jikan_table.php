<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrYoyakuDanjikiJikanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_yoyaku_danjiki_jikan', function (Blueprint $table) {
            $table->bigIncrements('tr_yoyaku_danjiki_jikan_id');
            $table->string('booking_id')->comment('primary key( booking_id service_date )');
            $table->string('service_date')->comment('primary key( booking_id service_date )');
            $table->string('service_time_1',255)->nullable();
            $table->string('service_time_2',255)->nullable();
            $table->string('notes',255)->nullable();
            $table->longText('time_json')->nullable();
            // $table->primary(['booking_id','service_date','service_time_1']);
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
        Schema::dropIfExists('tr_yoyaku_danjiki_jikan');
    }
}
