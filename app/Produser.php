<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Produser extends Model
{
    use SoftDeletes;
    protected $table='produser';

    protected $fillable=[
      'nama'
    ];
    protected $dates = ['deleted_at'];
    public function Anime(){
      return $this->belongsToMany(Anime::class)->withTimestamps();
    }
}