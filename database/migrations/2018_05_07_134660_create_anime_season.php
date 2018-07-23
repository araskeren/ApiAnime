<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnimeSeason extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('season', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('anime_id')->unsigned()->index();
            $table->foreign('anime_id')->references('id')->on('anime')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('studio_id')->unsigned()->nullable();;
            $table->foreign('studio_id')->references('id')->on('studio')->onUpdate('cascade')->onDelete('cascade');
            $table->string('season',10);
            $table->string('slug',10);
            $table->integer('durasi')->unsigned()->nullable();
            $table->integer('episode')->unsigned()->nullable();
            $table->date('tanggal_tayang')->nullable();
            $table->date('tanggal_end')->nullable();
            $table->string('musim',11)->nullable();
            $table->string('broadcast')->nullable();
            $table->string('type',15)->nullable();
            $table->string('cover')->nullable();
            $table->text('sinopsis')->nullable();
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
        Schema::dropIfExists('season');
    }
}
