<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkDttot extends Model
{

    protected $table = 'dokumen_dttot';
    protected $primaryKey = 'id';
    protected $fillable = array('tanggal_update','dokumen_name','status','dokumen','remarks');
    public $timestamps = true;
  
  
}
