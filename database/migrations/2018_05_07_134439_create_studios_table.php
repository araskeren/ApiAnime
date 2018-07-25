<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studio', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->string('nama',100)->unique();
            $table->string('slug',120)->unique();
            $table->integer('suka')->nullable();
            $table->integer('tidak_suka')->nullable();
            $table->integer('total_anime')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::table('studio')->insert([
          'user_id'=>1,
          'nama'=>'J.C Staff',
          'slug'=>'j-c-staf',
          'suka'=>200,
          'tidak_suka'=>10,
          'total_anime'=>143,
        ]);
        DB::table('studio')->insert([
          'user_id'=>1,
          'nama'=>'Madhouse',
          'slug'=>'madhouse',
          'suka'=>200,
          'tidak_suka'=>15,
          'total_anime'=>153,
        ]);
        DB::table('studio')->insert([
          'user_id'=>1,
          'nama'=>'P.A Works',
          'slug'=>'p-a-works',
          'suka'=>200,
          'tidak_suka'=>40,
          'total_anime'=>53,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('studio');
    }
}
