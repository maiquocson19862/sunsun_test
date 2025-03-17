<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_reminders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email_target',255);
            $table->longText('content')->nullable();
            $table->string('time',25)->nullable();
            $table->string('turn',1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_reminders');
    }
}
