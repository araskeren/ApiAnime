<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AnimeProduser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anime_produser', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('anime_id')->unsigned()->index();
          $table->foreign('anime_id')->references('id')->on('anime')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('anime_produser');
    }
}
