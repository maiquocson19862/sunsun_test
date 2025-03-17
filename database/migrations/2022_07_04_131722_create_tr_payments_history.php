<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrPaymentsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_payments_history', function (Blueprint $table) {
            $table->bigIncrements('tr_payments_history_id');
            $table->string('booking_id',12);
            $table->string('gender',100)->nullable();
            $table->string('age_value',100)->nullable();
            $table->string('date_value',1000)->nullable();
            $table->string('repeat_user',100)->nullable();
            $table->string('product_name',1000)->nullable();
            $table->integer('price')->nullable()->default('0');
            $table->tinyInteger('quantity')->nullable()->default('0');
            $table->string('unit',10)->nullable();
            $table->integer('money')->nullable()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_payments_history');
    }
}