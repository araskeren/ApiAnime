<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Studio extends Model
{
    use SoftDeletes;
    protected $table='studio';

    protected $fillable=[
      'nama','suka','tidak_suka','total_anime'
    ];
    protected $dates = ['deleted_at'];

}
