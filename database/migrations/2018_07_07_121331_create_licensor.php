<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLicensor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licensor', function (Blueprint $table) {
          $table->increments('id');
          $table->string('nama',50)->unique();
          $table->timestamps();
          $table->softDeletes();
        });
        DB::table('licensor')->insert([
          'nama'=>'Funimation',
        ]);
        DB::table('licensor')->insert([
          'nama'=>'Aniplex of America',
        ]);
        DB::table('licensor')->insert([
          'nama'=>'Crunchyroll',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('licensor');
    }
}
