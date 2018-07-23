<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genre', function (Blueprint $table) {
            $table->increments('id');
            $table->string('genre',30)->unique();
            $table->string('slug',40)->unique();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::table('genre')->insert([
          'genre'=>'Comedy',
          'slug'=>'comedy'
        ]);
        DB::table('genre')->insert([
          'genre'=>'Romance',
          'slug'=>'romance'
        ]);
        DB::table('genre')->insert([
          'genre'=>'Action',
          'slug'=>'action'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genre');
    }
}
