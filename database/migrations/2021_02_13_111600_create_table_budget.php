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
        Schema::create('budget', function (Blueprint $table) {
            $table->bigIncrements('budget_id');
            $table->string('name', 128);
            $table->text('description');
            $table->bigInteger('given_points');
            $table->string('code', 64);
            $table->boolean('active');

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