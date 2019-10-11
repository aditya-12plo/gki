<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataExtract extends Model
{

    protected $table = 'data_extract';
    protected $primaryKey = 'id';
    protected $fillable = array('dokumen_dttot_id','string');
    public $timestamps = false;
  
}
