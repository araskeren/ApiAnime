<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SeasonLicensor extends Model
{
    use SoftDeletes;

    protected $table='licensor_season';

    protected $fillable=[
      'season_id','licensor_id'
    ];
    protected $dates = ['deleted_at'];

}
