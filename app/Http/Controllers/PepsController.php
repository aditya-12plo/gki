<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Response,View,Input,Auth,Session,Validator,File,Hash,DB,Mail;
use Illuminate\Support\Facades\Crypt;


use App\Models\UkkPeps;
use App\Models\LogActivity;

class PepsController extends Controller
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
        $query = UkkPeps::orderBy('id','DESC');
        if ($search) {
            $like = "%{$search}%";
            $query = $query->where('dokumen_name', 'LIKE', $like);
        }
        if($min && !$max)
        {
            $query = $query->whereDate('tanggal_update','=',$min);
        }
        if(!$min && $max)
        {
            $query = $query->whereDate('tanggal_update','=',$max);
        }
        if($min && $max)
        {
            $query = $query->whereDate('tanggal_update','>=',$min)->whereDate('tanggal_update','<=',$max);
        }
         
        return $query->paginate($perPage);
    }

    public function store(Request $request)
    {
        $valid = $this->validate($request, [ 
            'dokumen_name'      => 'required|max:255',  
            'tanggal_update'    => 'required|date_format:Y-m-d', 
            'dokumen'           => 'required|mimes:pdf'
        ]);

        
        $file = Input::file('dokumen');
        $extension  = Input::file('dokumen')->getClientOriginalExtension(); 
        if ($file->getSize() <= 10000000){
            $destinationPath = public_path().'/dokumen-peps/'; // upload path
            $fileName   = str_replace(' ', '-', $request->dokumen_name).'-'.time().'.'.$extension; // renameing image
            if(file_exists($destinationPath.$fileName)){
                File::delete($destinationPath .$fileName);
            }

            $upload_success     = $file->move($destinationPath, $fileName);
            if(!$upload_success){
                return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File upload fail']]]); 
            }else{
                $masuk = array('dokumen_name' => $request->dokumen_name , 'tanggal_update' =>$request->tanggal_update, 'dokumen' =>$fileName); 
                LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'karyawan' ,'action' => 'insert', 'data' => json_encode($masuk)]);
                UkkPeps::create($masuk);
                return response()->json(['status'=>200,'data'=>'','message'=>'Add Successfully']);
            }

        }else{
            return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File must be < 10 MB']]]); 
        } 
    }

    public function destroy($id)
    {
        $cek = UkkPeps::findOrFail($id);
        if(!$cek)
        {
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
            $destinationPath = public_path().'/dokumen-peps/'; // upload path
            File::delete($destinationPath .$cek->dokumen);
            LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'users' ,'action' => 'delete', 'data' => json_encode($cek)]);
            UkkPeps::where('id',$id)->delete();
            return response()->json(['status'=>200,'data'=>'','message'=>'Delete Successfully']);
        } 
    }

    public function updatePeps(Request $request, $id)
    {
        $cek = UkkPeps::findOrFail($id);
        if(!$cek)
        {
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
            if(Input::file('dokumen')){
                $valid = $this->validate($request, [ 
                    'dokumen_name'      => 'required|max:255',  
                    'tanggal_update'    => 'required|date_format:Y-m-d', 
                    'dokumen'           => 'required|mimes:pdf'
                ]);
                $file = Input::file('dokumen');
                $extension  = Input::file('dokumen')->getClientOriginalExtension(); 
                if ($file->getSize() <= 10000000){
                    $destinationPath = public_path().'/dokumen-peps/'; // upload path
                    $fileName   = str_replace(' ', '-', $request->dokumen_name).'-'.time().'.'.$extension; // renameing image
                    if(file_exists($destinationPath.$fileName)){
                        File::delete($destinationPath .$fileName);
                    }
        
                    $upload_success     = $file->move($destinationPath, $fileName);
                    if(!$upload_success){
                        return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File upload fail']]]); 
                    }else{
                        File::delete($destinationPath .$cek->dokumen);
                        $edit = array('dokumen_name' => $request->dokumen_name , 'tanggal_update' =>$request->tanggal_update, 'dokumen' =>$fileName); 
                        LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'karyawan' ,'action' => 'update', 'data' => json_encode($cek)]);
                        $cek->update($edit); 
                        return response()->json(['status'=>200,'data'=>'','message'=>'Edit Successfully']);
                    }
        
                }else{
                    return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File must be < 10 MB']]]); 
                } 
            }else{
                $valid = $this->validate($request, [ 
                    'dokumen_name'      => 'required|max:255',  
                    'tanggal_update'    => 'required|date_format:Y-m-d'
                ]);

                $edit = array('dokumen_name' => $request->dokumen_name , 'tanggal_update' =>$request->tanggal_update); 
                LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'karyawan' ,'action' => 'update', 'data' => json_encode($cek)]);
                $cek->update($edit); 
                return response()->json(['status'=>200,'data'=>'','message'=>'Edit Successfully']);

            }
        } 
    } 

    public function downloadFilePeps(Request $request){
        $fileName = $request->filename;
        $path = public_path().'/dokumen-peps/'.$fileName; 
        return response()->download($path); 
    }

}