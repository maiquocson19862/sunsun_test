<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrYoyakuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_yoyaku', function (Blueprint $table) {
            $table->bigIncrements('tr_yoyaku_id');
            $table->string('booking_id',12);
            $table->string('ref_booking_id',12)->nullable();
            $table->string('history_id',12)->nullable();
            $table->string('name',255)->nullable();
            $table->string('phone',255)->nullable();
            $table->string('email',255)->nullable();
            $table->string('repeat_user',2)->nullable();
            $table->string('transport',2)->nullable();
            $table->string('bus_arrive_time_slide',2)->nullable();
            $table->string('bus_arrive_time_value',4)->nullable();
            $table->string('pick_up',2)->nullable();
            $table->string('course',2);
            $table->string('gender',2)->nullable();
            $table->integer('age_type')->nullable();
            $table->integer('age_value')->nullable();
            $table->string('service_date_start',8)->nullable();
            $table->string('service_date_end',8)->nullable();
            $table->string('service_time_1',4)->nullable();
            $table->string('service_time_2',4)->nullable();
            $table->longText('time_json')->nullable();
            $table->string('bed',3)->nullable();
            $table->string('service_guest_num',2)->nullable();
            $table->string('service_pet_num',2)->nullable();
            $table->string('lunch',2)->nullable();
            $table->string('lunch_guest_num',2)->nullable();
            $table->string('whitening',2)->nullable();
            $table->string('whitening_repeat',1)->nullable();
            $table->string('whitening_time',9)->nullable();
            $table->longText('whitening_time_json')->nullable();
            $table->string('pet_keeping',2)->nullable();
            $table->string('stay_room_type',2)->nullable();
            $table->string('stay_guest_num',2)->nullable();
            $table->string('stay_checkin_date',8)->nullable();
            $table->string('stay_checkout_date',8)->nullable();
            $table->string('breakfast',2)->nullable();
            $table->string('payment_method',1)->nullable();
            $table->string('fake_booking_flg',1)->nullable();
            $table->string('notes',255)->nullable();
            $table->tinyInteger('tea')->nullable()->default('0');
            $table->integer('ms_user_id')->nullable();
            $table->string('del_flg',1)->nullable();
            $table->string('whitening2',2)->nullable();
            $table->string('whitening_repeat2',1)->nullable();
            $table->string('core_tuning',2)->nullable();
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
        Schema::dropIfExists('tr_yoyaku');
    }
}