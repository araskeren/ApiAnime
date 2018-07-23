<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SeasonProduser extends Model
{
    use SoftDeletes;

    protected $table='produser_season';

    protected $fillable=[
      'season_id','produser_id'
    ];
    protected $dates = ['deleted_at'];

}
