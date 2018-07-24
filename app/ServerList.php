<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ServerList extends Model
{
    use SoftDeletes;

    protected $table='server_list';

    protected $fillable=[
      'episode_id','server','slug','download','streaming'
    ];
    protected $dates = ['deleted_at'];

}
