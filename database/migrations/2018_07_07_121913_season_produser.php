<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeasonProduser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('season_produser', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('season_id')->unsigned()->index();
          $table->foreign('season_id')->references('id')->on('season')->onUpdate('cascade')->onDelete('cascade');
          $table->integer('produser_id')->unsigned()->index();
          $table->foreign('produser_id')->references('id')->on('produser')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('season_produser');
    }
}
