<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProduser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produser', function (Blueprint $table) {
          $table->increments('id');
          $table->string('nama',50)->unique();
          $table->string('slug',60)->unique();
          $table->timestamps();
          $table->softDeletes();
        });
        DB::table('produser')->insert([
          'nama'=>'Frontier Work',
        ]);
        DB::table('produser')->insert([
          'nama'=>'Kadokawa Shoten',
        ]);
        DB::table('produser')->insert([
          'nama'=>'Movic',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produser');
    }
}
