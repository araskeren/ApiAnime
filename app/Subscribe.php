<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Subscribe extends Model
{
    use SoftDeletes;

    protected $table='subscribe';

    protected $fillable=[
      'user','anime'
    ];
    protected $dates = ['deleted_at'];
    public function User(){
      return $this->belongsToMany(User::class)->withTimestamps();
    }
}
