<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_user', function (Blueprint $table) {
            $table->bigIncrements('ms_user_id');
            $table->string('username',255);
            $table->string('tel',255);
            $table->string('email',255)->unique();
            $table->string('gender',255)->nullable();
            $table->string('birth_year',255)->nullable();
            $table->string('user_type',255)->default('user');
            $table->string('password',255);
            $table->string('deleteflg',1)->default(0);
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
        Schema::dropIfExists('ms_user');
    }
}
