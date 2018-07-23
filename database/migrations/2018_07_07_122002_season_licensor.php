<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeasonLicensor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licensor_season', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('season_id')->unsigned()->index();
          $table->foreign('season_id')->references('id')->on('season')->onUpdate('cascade')->onDelete('cascade');
          $table->integer('licensor_id')->unsigned()->index();
          $table->foreign('licensor_id')->references('id')->on('licensor')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('licensor_season');
    }
}
