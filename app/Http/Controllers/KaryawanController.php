<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Response,View,Input,Auth,Session,Validator,File,Hash,DB,Mail,Excel;
use Illuminate\Support\Facades\Crypt;

use Maatwebsite\Excel\HeadingRowImport;


use App\Imports\KaryawanImport;

use App\Models\LogActivity;
use App\Models\Karyawan; 

class KaryawanController extends Controller
{ 
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $perPage = $request->per_page;
        $search = $request->filter;
        $min = $request->min;
        $max = $request->max;
        $query = Karyawan::orderBy('id','DESC');
        if ($search) {
            $like = "%{$search}%";
            $query = $query->where('nama', 'LIKE', $like)->orWhere('nomor_aplikasi', 'LIKE', $like)->orWhere('jabatan', 'LIKE', $like);
        }
        if($min && !$max)
        {
            $query = $query->whereDate('awal_masuk','=',$min);
        }
        if(!$min && $max)
        {
            $query = $query->whereDate('awal_masuk','=',$max);
        }
        if($min && $max)
        {
            $query = $query->whereDate('awal_masuk','>=',$min)->whereDate('awal_masuk','<=',$max);
        }
        return $query->paginate($perPage);
    }

    public function store(Request $request)
    {
        $valid = $this->validate($request, [ 
            'nama'              => 'required|max:255', 
            'nomor_aplikasi'    => 'required|digits_between:1,255',
            'jabatan'           => 'required|max:255', 
            'awal_masuk'        => 'required|date_format:Y-m-d',
            'cuti'              => 'required|digits_between:1,3',
            'dokumen'           => 'required|mimes:pdf'
        ]);

        
        $extension  = $request->dokumen->extension(); 
        $destinationPath = public_path('/dokumen-karyawan/'); 
        $fileName   = str_replace(' ', '-', $request->nama).'-'.time().'.'.$extension; // renameing image
        if(file_exists($destinationPath.$fileName)){
            File::delete($destinationPath .$fileName);
        }

        $upload_success     = $request->dokumen->move($destinationPath, $fileName);
        if(!$upload_success){
            return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File upload fail']]]); 
        }else{
            $masuk = array('nama' => $request->nama , 'nomor_aplikasi' =>$request->nomor_aplikasi, 'awal_masuk'=>$request->awal_masuk , 'jabatan' =>$request->jabatan , 'cuti' =>$request->cuti, 'dokumen' =>$fileName); 
            LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'karyawan' ,'action' => 'insert', 'data' => json_encode($masuk)]);
            Karyawan::create($masuk);
            return response()->json(['status'=>200,'data'=>'','message'=>'Add Successfully']);
        } 
    }

    public function importData(Request $request)
    {
        $valid = $this->validate($request, [  
            'fileUpload'  => 'required|mimes:xlsx'
        ]); 
        try {
            Excel::import(new KaryawanImport,$request->file('fileUpload'));
            return response()->json(['status'=>200,'data'=>'','message'=>'Import Successfully']);
        } catch (\Exception $e) { 
            return response()->json(['status'=>422,'data'=>'','message'=>array('fileUpload'=>$e->errors())]);
        }
    }
    
    public function downloadThemplate()
    {   
        $fileName = 'impor-data-karyawan.xlsx';
        $path = public_path().'/themplate-file/'.$fileName; 
        return response()->download($path); 
    }

    public function updateKaryawan(Request $request, $id)
    {  
        $cek = Karyawan::findOrFail($id);
        if(!$cek){
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
            if(Input::file('dokumen')){
                $valid = $this->validate($request, [ 
                    'nama'              => 'required|max:255', 
                    'nomor_aplikasi'    => 'required|digits_between:1,255',
                    'jabatan'           => 'required|max:255', 
                    'awal_masuk'        => 'required|date_format:Y-m-d',
                    'cuti'              => 'required|digits_between:1,3',
                    'dokumen'           => 'required|mimes:pdf'
                ]);

                $file = Input::file('dokumen');
                $extension  = Input::file('dokumen')->getClientOriginalExtension(); 
                if ($file->getSize() <= 10000000){
                    $destinationPath = 'public/dokumen-karyawan/'; // upload path
                    $fileName   = str_replace(' ', '-', $request->nama).'-'.time().'.'.$extension; // renameing image
                    if(file_exists($destinationPath.$fileName)){
                        File::delete($destinationPath .$fileName);
                    }
        
                    $upload_success     = $file->move($destinationPath, $fileName);
                    if(!$upload_success){
                        return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File upload fail']]]); 
                    }else{
                        File::delete($destinationPath .$cek->dokumen);
                        $edit = array('nama' => $request->nama , 'nomor_aplikasi' =>$request->nomor_aplikasi, 'awal_masuk'=>$request->awal_masuk , 'jabatan' =>$request->jabatan , 'cuti' =>$request->cuti, 'dokumen' =>$fileName); 
                        LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'karyawan' ,'action' => 'update', 'data' => json_encode($edit)]);
                        $cek->update($edit); 
                        return response()->json(['status'=>200,'data'=>'','message'=>'Edit Successfully']);
                    }
        
                }else{
                    return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File must be < 10 MB']]]); 
                } 

            }else{
                $valid = $this->validate($request, [ 
                    'nama'              => 'required|max:255', 
                    'nomor_aplikasi'    => 'required|digits_between:1,255',
                    'jabatan'           => 'required|max:255', 
                    'awal_masuk'        => 'required|date_format:Y-m-d',
                    'cuti'              => 'required|digits_between:1,3'
                ]);
                $edit = array('nama' => $request->nama , 'nomor_aplikasi' =>$request->nomor_aplikasi, 'awal_masuk'=>$request->awal_masuk , 'jabatan' =>$request->jabatan , 'cuti' =>$request->cuti); 
                LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'karyawan' ,'action' => 'update', 'data' => json_encode($edit)]);
                $cek->update($edit); 
                return response()->json(['status'=>200,'data'=>'','message'=>'Edit Successfully']);
        
            }
        }  
    }

    public function destroy($id)
    {
        $cek = Karyawan::findOrFail($id);
        if(!$cek){
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
            $destinationPath = 'public/dokumen-karyawan/'; // upload path
            File::delete($destinationPath .$cek->dokumen);
            LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'karyawan' ,'action' => 'delete', 'data' => json_encode($cek)]);
            Karyawan::where('id',$id)->delete();
            return response()->json(['status'=>200,'data'=>'','message'=>'Delete Successfully']);
        }  
    }
}
