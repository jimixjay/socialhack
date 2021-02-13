<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('slug', 128);
            $table->string('name', 128);
            $table->string('username', 32);
            $table->string('password', 32);
            $table->string('google_token', 512);
            $table->string('email', 64);
            $table->string('phone', 32);
            $table->string('cp', 5);
            $table->string('city', 128);
            $table->integer('province_id');
            $table->integer('country_id');
            $table->string('avatar', 512);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner');
    }
}