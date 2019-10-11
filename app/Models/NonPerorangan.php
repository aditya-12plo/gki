<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NonPerorangan extends Model
{

    protected $table = 'profil_non_perorangan';
    protected $primaryKey = 'id';
    protected $fillable = array('kuasa_non_perorangan_id','benefecial_owner_id','nama_perusahaan','nomor_akun','nomor_ijin_usaha','bidang_usaha',
    'tempat_pendirian','tanggal_pendirian','bentuk_hukum','npwp','profil','nomor_rekening_bank','nomor_telepon','email',
    'wilayah_domisili','alamat');
    public $timestamps = true;
 
  
    public function kuasa()
    {
        return $this->hasOne('App\Models\Kuasa','id','kuasa_non_perorangan_id');
    }
 
  
    public function owner()
    {
        return $this->hasOne('App\Models\BenefecialOwner','id','benefecial_owner_id');
    }
}
