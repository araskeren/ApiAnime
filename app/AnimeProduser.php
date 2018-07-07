<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AnimeProduser extends Model
{
    use SoftDeletes;

    protected $table='anime_produser';

    protected $fillable=[
      'anime_id','produser_id'
    ];
    protected $dates = ['deleted_at'];

}
