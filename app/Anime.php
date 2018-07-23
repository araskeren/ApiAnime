<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Anime extends Model
{
    use SoftDeletes;
    protected $table='anime';

    protected $fillable=[
      'user_id','judul','judul_alternatif','slug','sumber','cover'
    ];
    protected $dates = ['deleted_at'];
    public function Genre(){
      return $this->belongsToMany(Genre::class)->withTimestamps();
    }
    
}
