<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkPeps extends Model
{

    protected $table = 'dokumen_peps';
    protected $primaryKey = 'id';
    protected $fillable = array('tanggal_update','dokumen_name','dokumen');
    public $timestamps = true;
  
  
}
