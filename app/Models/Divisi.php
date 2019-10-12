<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{

    protected $table = 'divisi';
    protected $primaryKey = 'id';
    protected $fillable = array('divisi');
    public $timestamps = true; 
  
}
