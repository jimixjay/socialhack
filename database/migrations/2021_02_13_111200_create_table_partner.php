<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePartner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner', function (Blueprint $table) {
            $table->bigIncrements('partner_id');
            $table->string('slug', 128);
            $table->string('name', 128);
            $table->string('username', 128);
            $table->string('password', 128);
            $table->string('phone', 128)->nullable();
            $table->string('email', 128);
            $table->string('web', 128)->nullable();
            $table->string('address', 256)->nullable();
            $table->string('cp', 128)->nullable();
            $table->string('city', 128)->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->text('description');
            $table->string('logo', 512);

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