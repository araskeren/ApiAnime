<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AnimeGenre extends Model
{
    use SoftDeletes;

    protected $table='anime_genre';

    protected $fillable=[
      'anime_id','genre_id'
    ];
    protected $dates = ['deleted_at'];

}
