<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Komentar extends Model
{
    use SoftDeletes;

    protected $table='komentar';

    protected $fillable=[
      'user','episode','komentar'
    ];
    protected $dates = ['deleted_at'];
    public function User(){
      return $this->belongsToMany(User::class)->withTimestamps();
    }
}
