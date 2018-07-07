<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Anime extends Model
{
    use SoftDeletes;
    protected $table='anime';

    protected $fillable=[
      'user_id','judul','judul_alternatif','studio_id','durasi','episode','tanggal_tayang','tanggal_end','type','sumber','musim','status','broadcast','cover','sinopsis'
    ];
    protected $dates = ['deleted_at'];
    public function Genre(){
      return $this->belongsToMany(Genre::class)->withTimestamps();
    }
    public function Produser(){
      return $this->belongsToMany(Produser::class)->withTimestamps();
    }
    public function Licensor(){
      return $this->belongsToMany(Licensor::class)->withTimestamps();
    }
    public function Studio(){
      return $this->belongsTo(Studio::class,'studio_id');
    }
}
