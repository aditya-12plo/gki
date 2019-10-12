<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Response,View,Input,Auth,Session,Validator,File,Hash,DB,Mail,Excel;
use Illuminate\Support\Facades\Crypt;
use PHPExcel; 
use PHPExcel_IOFactory;

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
            'divisi'            => 'required|max:255', 
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
            $masuk = array('nama' => $request->nama , 'nomor_aplikasi' =>$request->nomor_aplikasi, 'awal_masuk'=>$request->awal_masuk , 'jabatan' =>$request->jabatan , 'divisi' =>$request->divisi , 'cuti' =>$request->cuti, 'dokumen' =>$fileName); 
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
                    'divisi'            => 'required|max:255', 
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
                        $edit = array('nama' => $request->nama , 'nomor_aplikasi' =>$request->nomor_aplikasi, 'awal_masuk'=>$request->awal_masuk , 'jabatan' =>$request->jabatan , 'divisi' =>$request->divisi , 'cuti' =>$request->cuti, 'dokumen' =>$fileName); 
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
                    'divisi'            => 'required|max:255', 
                    'awal_masuk'        => 'required|date_format:Y-m-d',
                    'cuti'              => 'required|digits_between:1,3'
                ]);
                $edit = array('nama' => $request->nama , 'nomor_aplikasi' =>$request->nomor_aplikasi, 'awal_masuk'=>$request->awal_masuk , 'jabatan' =>$request->jabatan , 'divisi' =>$request->divisi , 'cuti' =>$request->cuti); 
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

    public function chartByJabatan()
    {
        $res = array("gridColumns"=>["jabatan"],"gridData"=>[0]);
        $results = Karyawan::select('jabatan', \DB::raw('COUNT(id) as total'))
                    ->groupBy('jabatan')
                    ->get();
        if(count($results) > 0){
            $gridColumns = $this->get_values($results,'jabatan');
            $gridData    = $this->get_values($results,'total');
            
            $res =array("gridColumns"=>$gridColumns,"gridData"=>$gridData);
        }
        
        return response()->json($res);
    }

    public function chartByDivisi()
    {
        $res = array("gridColumns"=>["divisi"],"gridData"=>[0]);
        $results = Karyawan::select('divisi', \DB::raw('COUNT(id) as total'))
                    ->groupBy('divisi')
                    ->get();
        if(count($results) > 0){
            $gridColumns = $this->get_values($results,'divisi');
            $gridData    = $this->get_values($results,'total');
            
            $res = $results;
        }
        
        return response()->json($res);
    }

    private function get_values($array,$key){
        $dd = [];
        foreach($array as $k => $value){ 
                array_push($dd,$value[$key]); 
        }
        return $dd;
    }

    public function print(Request $request)
    { 
        $search = $request->filter;
        $min = $request->min;
        $max = $request->max;
        $query = Karyawan::select(['nama','nomor_aplikasi','jabatan','divisi','awal_masuk','cuti'])->orderBy('id','DESC');
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
         
        return array("gridColumns" =>['nama','nomor_aplikasi','jabatan','divisi','awal_masuk','cuti'],"gridData"=> $query->get());
    }

    public function downloadFile(Request $request){

        $search = $request->filter;
        $min = $request->min;
        $max = $request->max; 
        $filename = $request->filename;
        $x=0;
        $res = [];
        $query = Karyawan::select(['nama','nomor_aplikasi','jabatan','divisi','awal_masuk','cuti'])->orderBy('id','DESC');
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
        $data =  $query->get();

        $fileName = $filename;
        $objPHPExcel = new PHPExcel(); 
        $objPHPExcel->setActiveSheetIndex(0); $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', 'NAMA KARYAWAN')
        ->setCellValue('B1', 'NOMOR APLIKASI')
        ->setCellValue('C1', 'JABATAN')
        ->setCellValue('D1', 'DIVISI')
        ->setCellValue('E1', 'AWAL MASUK')
        ->setCellValue('F1', 'CUTI');
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);  
        $no=1;
        $row=2;
        if(count($data) > 0){
            foreach ($data as $d){
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $d->nama);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $d->nomor_aplikasi);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $d->jabatan);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $d->divisi);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $d->awal_masuk);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $d->cuti);
                $no++; 
                $row++;
            }
        }

        $objPHPExcel->getActiveSheet()->setTitle('Sheet1'); 
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); 
    }
}
