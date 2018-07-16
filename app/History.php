<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class History extends Model
{
    use SoftDeletes;

    protected $table='history';

    protected $fillable=[
      'user','episode'
    ];
    protected $dates = ['deleted_at'];

    public function User(){
      return $this->belongsTo(User::class,'user');
    }
    public function Episode(){
      return $this->belongsTo(Episode::class,'episode');
    }
}
