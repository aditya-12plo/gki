<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Response,View,Input,Auth,Session,Validator,File,Hash,DB,Mail;
use Illuminate\Support\Facades\Crypt;


use App\Models\LogActivity;
use App\Models\NonPerorangan;
use App\Models\Kuasa;

class NonPeroranganController extends Controller
{ 
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $perPage = $request->per_page;
        $search = $request->filter; 
        $query = NonPerorangan::with('kuasa','owner')->orderBy('id','DESC');
        if ($search) {
            $like = "%{$search}%";
            $query = $query->where('nomor_akun', 'LIKE', $like)->orWhere('nama_perusahaan', 'LIKE', $like);
        } 
         
        return $query->paginate($perPage);
    }
    public function store(Request $request)
    {

    }

    public function edit($id)
    {

    }
    
    public function update(Request $request, $id)
    {
        $cek = NonPerorangan::findOrFail($id);
        if(!$cek)
        {
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }
        else
        {
            $valid = $this->validate($request, [
                'nomor_rekening_bank' => 'required|numeric|not_in:0', 
                'nomor_telepon' => 'required|numeric|not_in:0', 
                'email' => 'required|max:255|unique:profil_perorangan,email,'.$id, 
            ]);
            $edit = array('email' => $request->email , 'nomor_rekening_bank' =>$request->nomor_rekening_bank); 
            $editKuasa = array('nomor_telepon' => $request->nomor_telepon); 
            LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'profil_non_perorangan' ,'action' => 'update', 'data' => json_encode($cek)]);
            Kuasa::where('id', $cek->kuasa_non_perorangan_id)->update($editKuasa);
            $cek->update($edit); 
            return response()->json(['status'=>200,'data'=>'','message'=>'Update Successfully']);
        }
    }

    public function destroy($id)
    {

    }
}
