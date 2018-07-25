<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Licensor extends Model
{
    use SoftDeletes;
    protected $table='licensor';

    protected $fillable=[
      'nama','slug'
    ];
    protected $dates = ['deleted_at'];
    public function Season(){
      return $this->belongsToMany(Season::class)->withTimestamps();
    }
}
