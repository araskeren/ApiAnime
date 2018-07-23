<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Episode extends Model
{
    use SoftDeletes;
    protected $table='episode';

    protected $fillable=[
      'user','season','episode','cover','keterangan'
    ];

    protected $dates = ['deleted_at'];

    public function Anime(){
      return $this->belongsTo(Anime::class,'anime');
    }
    public function User(){
      return $this->belongsTo(User::class,'user');
    }
}
