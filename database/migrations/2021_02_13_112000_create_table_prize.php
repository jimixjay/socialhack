<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePrize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prize', function (Blueprint $table) {
            $table->bigIncrements('prize_id');
            $table->bigInteger('partner_id');
            $table->bigInteger('budget_id')->nullable();
            $table->bigInteger('rank_id')->nullable();
            $table->string('title', 256);
            $table->text('content');
            $table->integer('given_extra_points');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
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