<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Genre extends Model
{
    use SoftDeletes;
    protected $table='genre';

    protected $fillable=[
      'genre'
    ];
    protected $dates = ['deleted_at'];
    public function Anime(){
      return $this->belongsToMany(Anime::class)->withTimestamps();
    }
}
