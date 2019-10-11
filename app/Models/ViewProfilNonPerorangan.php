<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewProfilNonPerorangan extends Model
{

    protected $table = 'view_profil_non_perorangan';
    protected $primaryKey = 'id';
    protected $fillable = array(
        'kuasa_non_perorangan_id','benefecial_owner_id','nama_perusahaan','nomor_akun',
        'nomor_ijin_usaha','bidang_usaha','tempat_pendirian','tanggal_pendirian',
        'bentuk_hukum','npwp_perusahaan','profil_perusahaan','no_rekening_bank_perusahaan','nomor_telepon_perusahaan',
        'email_perusahaan','wilayah_domisili_perusahaan','alamat_perusahaan','created_at','updated_at',
        'nama_bo','nama_alias_bo','jenis_kartu_identitas_bo','nomor_identitas_bo','masa_berlaku_bo','npwp_bo','tempat_lahir_bo',
        'tanggal_lahir_bo','status_perkawinan_bo','jenis_kelamin_bo','nomor_telepon_bo','alamat_bo','alamat_sekarang_bo',
        'nama_knp','nama_alias_knp','jenis_kartu_identitas_knp','nomor_identitas_knp','masa_berlaku_knp',
        'npwp_knp','tempat_lahir_knp','tanggal_lahir_knp','status_perkawinan_knp','jenis_kelamin_knp',
        'nomor_telepon_knp','alamat_knp','alamat_sekarang_knp'
    );
    public $timestamps = false;
  
}
