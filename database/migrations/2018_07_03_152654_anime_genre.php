<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AnimeGenre extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('anime_genre', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('anime_id')->unsigned()->index();
          $table->foreign('anime_id')->references('id')->on('anime')->onUpdate('cascade')->onDelete('cascade');
          $table->integer('genre_id')->unsigned()->index();
          $table->foreign('genre_id')->references('id')->on('genre')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('anime_genre');
    }
}
