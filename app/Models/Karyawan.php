<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{

    protected $table = 'karyawan';
    protected $primaryKey = 'id';
    protected $fillable = array('nama','nomor_aplikasi','jabatan','awal_masuk','cuti','dokumen');
    public $timestamps = true; 
  
}
