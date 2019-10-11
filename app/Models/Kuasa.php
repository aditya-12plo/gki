<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kuasa extends Model
{

    protected $table = 'kuasa_non_perorangan';
    protected $primaryKey = 'id';
    protected $fillable = array('nama','nama_alias','jenis_kartu_identitas','nomor_identitas',
    'masa_berlaku','npwp','tempat_lahir','tanggal_lahir','status_perkawinan',
    'jenis_kelamin','nomor_telepon','alamat','alamat_sekarang');
    public $timestamps = true;
 
  
    public function non_perorangan()
    {
        return $this->belongsTo('App\Models\NonPerorangan','kuasa_non_perorangan_id');
    }
 
  
}
