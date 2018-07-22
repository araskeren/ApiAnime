<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Playlist extends Model
{
    use SoftDeletes;

    protected $table='playlist';

    protected $fillable=[
      'user','episode'
    ];
    protected $dates = ['deleted_at'];

}
