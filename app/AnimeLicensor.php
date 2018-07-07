<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AnimeLicensor extends Model
{
    use SoftDeletes;

    protected $table='anime_licensor';

    protected $fillable=[
      'anime_id','licensor_id'
    ];
    protected $dates = ['deleted_at'];

}
