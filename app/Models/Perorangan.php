<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perorangan extends Model
{

    protected $table = 'profil_perorangan';
    protected $primaryKey = 'id';
    protected $fillable = array('nama','nomor_akun','tanggal_registrasi','tanggal_nasabah',
    'jenis_identitas','nomor_identitas','masa_berlaku','npwp','tempat_lahir','tanggal_lahir',
    'jenis_kelamin','profesi','nomor_rekening_bank','nomor_telepon','email','wilayah_domisili','alamat',
    'image_akun');
    public $timestamps = true;
 
  
 
  
}
