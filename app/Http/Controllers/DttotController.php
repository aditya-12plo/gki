<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Response,View,Input,Auth,Session,Validator,File,Hash,DB,Mail;
use Illuminate\Support\Facades\Crypt;
use Smalot\PdfParser\Parser;
use PHPExcel; 
use PHPExcel_IOFactory;

use App\Jobs\DttotFilesJob;


use App\Models\UkkDttot;
use App\Models\LogActivity;
use App\Models\DataExtract;
use App\Models\Karyawan; 
use App\Models\Perorangan;
use App\Models\ViewProfilNonPerorangan;

class DttotController extends Controller
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
        $query = UkkDttot::orderBy('id','DESC');
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
            $destinationPath = public_path().'/dokumen-dttot/'; // upload path
            $fileName   = str_replace(' ', '-', $request->dokumen_name).'-'.time().'.'.$extension; // renameing image
            if(file_exists($destinationPath.$fileName)){
                File::delete($destinationPath .$fileName);
            }

            $upload_success     = $file->move($destinationPath, $fileName);
            if(!$upload_success){
                return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File upload fail']]]); 
            }else{
                $masuk = array('dokumen_name' => $request->dokumen_name , 'tanggal_update' =>$request->tanggal_update, 'status' => 'upload', 'dokumen' =>$fileName); 
                $ddtot = UkkDttot::create($masuk);
                $processFiles = (new DttotFilesJob($ddtot))->delay(Carbon::now()->addMinutes(1));
                dispatch($processFiles); 
                LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'dokumen_dttot' ,'action' => 'insert', 'data' => json_encode($masuk)]); 
                return response()->json(['status'=>200,'data'=>'','message'=>'Add Successfully']);
            }

        }else{
            return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File must be < 10 MB']]]); 
        } 
    }

    public function destroy($id)
    {
        $cek = UkkDttot::findOrFail($id);
        if(!$cek)
        {
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
            $destinationPath = public_path().'/dokumen-dttot/'; // upload path
            File::delete($destinationPath .$cek->dokumen);
            LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'dokumen_dttot' ,'action' => 'delete', 'data' => json_encode($cek)]);
            UkkDttot::where('id',$id)->delete();
            DataExtract::where('dokumen_dttot_id',$id)->delete();
            return response()->json(['status'=>200,'data'=>'','message'=>'Delete Successfully']);
        } 
    }

    public function updateDttot(Request $request, $id)
    {
        $cek = UkkDttot::findOrFail($id);
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
                    $destinationPath = public_path().'/dokumen-dttot/'; // upload path
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
                        LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'dokumen_dttot' ,'action' => 'update', 'data' => json_encode($cek)]);
                        DataExtract::where('dokumen_dttot_id',$id)->delete();
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
                LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'dokumen_dttot' ,'action' => 'update', 'data' => json_encode($cek)]);
                $cek->update($edit); 
                $processFiles = (new DttotFilesJob($cek))->delay(Carbon::now()->addMinutes(3));
                dispatch($processFiles); 
                return response()->json(['status'=>200,'data'=>'','message'=>'Edit Successfully']);

            }
        } 
    }


    /*

    public function extractPdfToArray($id,$fileName){
        $cek = DataExtract::where('dokumen_dttot_id',$id)->first();
        if(!$cek)
        {
            $path = public_path().'/dokumen-dttot/'.$fileName; 
            $PDFParser = new Parser();
            $pdf = $PDFParser->parseFile($path);
            $pages  = $pdf->getPages();
            $totalPages = count($pages);
            $currentPage = 1;
            $text = ""; 
            $jml = 100;
            foreach ($pages as $page) { 
                $text .= $page->getText(); 
                $currentPage++;
            }

            $str = preg_replace('/(\v|\s)+/', ' ', $text);
            $add = str_replace('KEPOLISIAN NEGARA REPUBLIK INDONESIA MARKAS BESAR Jalan Trunojoyo 3, Kebayoran Baru, Jakarta 12110 “Pro Justitia ” DAFTAR TERDUGA TERORIS DAN ORGANISASI TERORIS', '', $str);
            $add = str_replace('I. INDIVIDU: ', '', $add);
            $add = str_replace('II. ENTITAS: ', '', $add);
            $variable = substr($add, 0, strpos($add, "III. KETERANGAN"));
            $start = 'Nomor:';
            $end = '1. Nama'; 
            $pattern = sprintf(
                    '/%s(.+?)%s/ims',
                    preg_quote($start, '/'), preg_quote($end, '/')
            ); 
            if (preg_match($pattern, $variable, $matches)) {
                    list(, $match) = $matches;
                    $concat = $start.$match;
                    $nextString = str_replace($concat, "", $variable);   
                    //echo $nextString;
                    $this->stringToArrayNya($nextString,1,$id,$fileName);

                    return response()->json(['status'=>200,'data'=>'','message'=>'Ekstrak Successfully']);
                }
            else{
                return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File different format']]]);
            } 
        }else{
            return response()->json(['status'=>200,'data'=>'','message'=>'Data Has Extract']);
        }
    }

    private function stringToArrayNya($string,$no,$id,$fileName){
        $jml= $no+1;
        for($no;$no<$jml;$no++){ 
            $start = ' '.$no.'.';
            $end = ' '.$jml.'.'; 
            $pattern = sprintf(
                '/%s(.+?)%s/ims',
                preg_quote($start, '/'), preg_quote($end, '/')
            );
            $rmv = $start.$end;
            if (preg_match($pattern, $string, $matches)) {
                list(, $match) = $matches;
                $concat = $start.$match;
                $nextString = str_replace($concat, "", $string); 
                $explode = explode(':', $match );  
                DataExtract::create(array('dokumen_dttot_id'=>$id,'string'=>json_encode($explode)));
                $this->stringToArrayNya($nextString,$jml,$id,$fileName); 
               // echo json_encode($explode); 
            }
        }
    }

 */

    public function downloadDttotKaryawan(Request $request){
        $id = $request->id;
        $filename = $request->filename;
        $x=0;
        $data = DataExtract::where('dokumen_dttot_id',$id)->get();
        $dataKaryawan = Karyawan::orderBy('id','DESC')->get();
        $res = [];

        if(count($data) > 0){

            foreach($dataKaryawan as $d){
                $like = "%{$d->nama}%";
                $cek =  DataExtract::where([['dokumen_dttot_id',$id],['string', 'LIKE', $like]])->get();
                if(count($cek) > 0){ 
                    $res[$x] = array('nama_karyawan'=>$d->nama,'nomor_aplikasi'=>$d->nomor_aplikasi,'jabatan'=>$d->jabatan,'divisi'=>$d->divisi,'awal_masuk'=>$d->awal_masuk,'dttot'=>$cek); 
                }
    
                $x++;
            }

            if($x<0){
                $this->downloadCompareKarywan(array());
            }else{
                $this->downloadCompareKarywan($res);
            }  
        }else{
             $this->downloadCompareKarywan(array());
        }

    }

    
    public function withDttotKaryawan(Request $request){
        $id = $request->id;
        $filename = $request->filename;
        $min = $request->min;
        $max = $request->max;
        $x=0;
        $data = DataExtract::where('dokumen_dttot_id',$id)->get();
        $dataKaryawan = Karyawan::orderBy('id','DESC');
        if($min && !$max)
        {
            $dataKaryawan = $dataKaryawan->whereDate('awal_masuk','=',$min);
        }
        if(!$min && $max)
        {
            $dataKaryawan = $dataKaryawan->whereDate('awal_masuk','=',$max);
        }
        if($min && $max)
        {
            $dataKaryawan = $dataKaryawan->whereDate('awal_masuk','>=',$min)->whereDate('awal_masuk','<=',$max);
        }
        $dataQuery =  $dataKaryawan->get();

        $res = [];

        if(count($data) > 0){

            foreach($dataQuery as $k){
                $like = "%{$k->nama}%";
                $cek =  DataExtract::where([['dokumen_dttot_id',$id],['string', 'LIKE', $like]])->get();
                if(count($cek) > 0){
                    foreach ($cek as $d){
                        $aa = json_decode($d->string,true);
                        $ss = $this->getDataFromArray($aa); 
                        $res[$x] = ['nama_karyawan'=>$k->nama,'nomor_aplikasi'=>$k->nomor_aplikasi, 'jabatan'=>$k->jabatan, 'divisi'=>$k->divisi, 'awal_masuk'=>$k->awal_masuk, 'nama'=>$ss['nama'], 'alias'=>$ss['alias'], 'lahir'=>$ss['lahir'], 'negara'=>$ss['negara'], 'alamat'=>$ss['alamat'], 'keterangan'=>$ss['keterangan']];
                        $x++;
                    }
                }
            }
              
        }
            return response()->json($res);
    }


    
    public function withDttotNonPerorangan(Request $request){
        $id = $request->id;
        $filename = $request->filename;
        $min = $request->min;
        $max = $request->max;
        $x=0;
        $data = DataExtract::where('dokumen_dttot_id',$id)->get();
        $NonPerorangan = ViewProfilNonPerorangan::orderBy('id','DESC');
        if($min && !$max)
        {
            $NonPerorangan = $NonPerorangan->whereDate('Created_at','=',$min);
        }
        if(!$min && $max)
        {
            $NonPerorangan = $NonPerorangan->whereDate('Created_at','=',$max);
        }
        if($min && $max)
        {
            $NonPerorangan = $NonPerorangan->whereDate('Created_at','>=',$min)->whereDate('Created_at','<=',$max);
        }
        $dataQuery =  $NonPerorangan->get();

        $res = [];

        if(count($data) > 0){

            foreach($dataQuery as $k){
                $like  = "%{$k->nama_perusahaan}%";
                $like2 = "%{$k->no_rekening_bank_perusahaan}%";
                $like3 = "%{$k->nama_bo}%";
                $like4 = "%{$k->nama_alias_bo}%";
                $like5 = "%{$k->nomor_identitas_bo}%"; 
                $like6 = "%{$k->nama_knp}%";
                $like7 = "%{$k->nama_alias_knp}%";
                $like8 = "%{$k->nomor_identitas_knp}%"; 
                $cek =  DataExtract::where([['dokumen_dttot_id',$id],['string', 'LIKE', $like]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like2]])
                ->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like3]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like4]])
                ->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like5]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like6]])
                ->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like7]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like8]])->get();
                if(count($cek) > 0){
                    foreach ($cek as $d){
                        $aa = json_decode($d->string,true);
                        $ss = $this->getDataFromArray($aa); 
                        $res[$x] = [
                            'nomor_akun'                    => $k->nomor_akun,
                            'nama_perusahaan'               => $k->nama_perusahaan,
                            'nomor_ijin_usaha'              => $k->nomor_ijin_usaha,
                            'bidang_usaha'                  => $k->bidang_usaha,
                            'tempat_pendirian'              => $k->tempat_pendirian,
                            'tanggal_pendirian'             => $k->tanggal_pendirian, 
                            'bentuk_hukum'                  => $k->bentuk_hukum,
                            'npwp_perusahaan'               => $k->npwp_perusahaan,
                            'profil_perusahaan'             => $k->profil_perusahaan,
                            'no_rekening_bank_perusahaan'   => $k->no_rekening_bank_perusahaan,
                            'nomor_telepon_perusahaan'      => $k->nomor_telepon_perusahaan,
                            'email_perusahaan'              => $k->email_perusahaan,
                            'wilayah_domisili_perusahaan'   => $k->wilayah_domisili_perusahaan,
                            'alamat_perusahaan'             => $k->alamat_perusahaan,
                            'nama_bo'                       => $k->nama_bo,
                            'nama_alias_bo'                 => $k->nama_alias_bo,
                            'jenis_kartu_identitas_bo'      => $k->jenis_kartu_identitas_bo, 
                            'nomor_identitas_bo'            => $k->nomor_identitas_bo, 
                            'masa_berlaku_bo'               => $k->masa_berlaku_bo, 
                            'npwp_bo'                       => $k->npwp_bo, 
                            'tempat_lahir_bo'               => $k->tempat_lahir_bo, 
                            'tanggal_lahir_bo'              => $k->tanggal_lahir_bo, 
                            'status_perkawinan_bo'          => $k->status_perkawinan_bo, 
                            'jenis_kelamin_bo'              => $k->jenis_kelamin_bo, 
                            'nomor_telepon_bo'              => $k->nomor_telepon_bo, 
                            'alamat_bo'                     => $k->alamat_bo, 
                            'alamat_sekarang_bo'            => $k->alamat_sekarang_bo, 
                            'nama_knp'                      => $k->nama_knp, 
                            'nama_alias_knp'                => $k->nama_alias_knp, 
                            'jenis_kartu_identitas_knp'     => $k->jenis_kartu_identitas_knp, 
                            'nomor_identitas_knp'           => $k->nomor_identitas_knp, 
                            'masa_berlaku_knp'              => $k->masa_berlaku_knp, 
                            'npwp_knp'                      => $k->npwp_knp, 
                            'tempat_lahir_knp'              => $k->tempat_lahir_knp, 
                            'tanggal_lahir_knp'             => $k->tanggal_lahir_knp, 
                            'status_perkawinan_knp'         => $k->status_perkawinan_knp, 
                            'jenis_kelamin_knp'             => $k->jenis_kelamin_knp, 
                            'nomor_telepon_knp'             => $k->nomor_telepon_knp, 
                            'alamat_knp'                    => $k->alamat_knp, 
                            'alamat_sekarang_knp'           => $k->alamat_sekarang_knp,  
                        'nama_dttot'=>$ss['nama'], 'alias_dttot'=>$ss['alias'], 'lahir_dttot'=>$ss['lahir'], 'negara_dttot'=>$ss['negara'], 
                        'alamat_dttot'=>$ss['alamat'], 'keterangan_dttot'=>$ss['keterangan']];
                        $x++;
                    }
                }
            }
              
        }
            return response()->json($res);
    }
    
    public function withDttotPerorangan(Request $request){
        $id = $request->id;
        $filename = $request->filename;
        $min = $request->min;
        $max = $request->max;
        $x=0;
        $data = DataExtract::where('dokumen_dttot_id',$id)->get();
        $Perorangan = Perorangan::orderBy('id','DESC');
        if($min && !$max)
        {
            $Perorangan = $Perorangan->whereDate('tanggal_registrasi','=',$min);
        }
        if(!$min && $max)
        {
            $Perorangan = $Perorangan->whereDate('tanggal_registrasi','=',$max);
        }
        if($min && $max)
        {
            $Perorangan = $Perorangan->whereDate('tanggal_registrasi','>=',$min)->whereDate('tanggal_registrasi','<=',$max);
        }
        $dataQuery =  $Perorangan->get();

        $res = [];

        if(count($data) > 0){

            foreach($dataQuery as $k){
                $like  = "%{$k->nama}%";
                $like2 = "%{$k->nomor_identitas}%";
                $like3 = "%{$k->npwp}%";
                $like4 = "%{$k->nomor_rekening_bank}%";
                $like5 = "%{$k->nomor_telepon}%"; 
                $cek =  DataExtract::where([['dokumen_dttot_id',$id],['string', 'LIKE', $like]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like2]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like3]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like4]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like5]])->get();
                if(count($cek) > 0){
                    foreach ($cek as $d){
                        $aa = json_decode($d->string,true);
                        $ss = $this->getDataFromArray($aa); 
                        $res[$x] = ['nama'=>$k->nama,'nomor_akun'=>$k->nomor_akun, 'tanggal_registrasi'=>$k->tanggal_registrasi, 'tanggal_nasabah'=>$k->tanggal_nasabah, 
                        'jenis_identitas'=>$k->jenis_identitas, 'nomor_identitas'=>$k->nomor_identitas, 'masa_berlaku'=>$k->masa_berlaku, 'npwp'=>$k->npwp, 
                        'tempat_lahir'=>$k->tempat_lahir, 'tanggal_lahir'=>$k->tanggal_lahir, 'jenis_kelamin'=>$k->jenis_kelamin, 'profesi'=>$k->profesi, 'nomor_rekening_bank'=>$k->nomor_rekening_bank, 'nomor_telepon'=>$k->nomor_telepon, 
                        'email'=>$k->email, 'wilayah_domisili'=>$k->wilayah_domisili, 'alamat'=>$k->alamat, 
                        'nama_dttot'=>$ss['nama'], 'alias_dttot'=>$ss['alias'], 'lahir_dttot'=>$ss['lahir'], 'negara_dttot'=>$ss['negara'], 
                        'alamat_dttot'=>$ss['alamat'], 'keterangan_dttot'=>$ss['keterangan']];
                        $x++;
                    }
                }
            }
              
        }
            return response()->json($res);
    }

    public function excelDttotPerorangan(Request $request){
        $array = $request->data;
        $fileName = $request->fileName.'.xls';      
        $objPHPExcel = new PHPExcel(); 
        $objPHPExcel->setActiveSheetIndex(0); 
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', 'NAMA PERORANGAN')
            ->setCellValue('B1', 'NOMOR AKUN')
            ->setCellValue('C1', 'TANGGAL REGISTRASI')
            ->setCellValue('D1', 'TANGGAL MENJADI NASABAH') 
            ->setCellValue('E1', 'JENIS IDENTITAS') 
            ->setCellValue('F1', 'NOMOR IDENTITAS')
            ->setCellValue('G1', 'MASA BERLAKU')
            ->setCellValue('H1', 'NOMOR NPWP')
            ->setCellValue('I1', 'TAMPAT / TANGGAL LAHIR')
            ->setCellValue('J1', 'JENIS KELAMIN')
            ->setCellValue('K1', 'PROFESI')
            ->setCellValue('L1', 'NOMOR RREKENING BANK')
            ->setCellValue('M1', 'NOMOR TELEPON')
            ->setCellValue('N1', 'EMAIL')
            ->setCellValue('O1', 'WILAYAH DOMISILI')
            ->setCellValue('P1', 'ALAMAT')
            ->setCellValue('Q1', 'NAMA DTTOT')
            ->setCellValue('R1', 'NAMA DTTOT ALIAS')
            ->setCellValue('S1', 'TEMPAT TANGGAL LAHIR DTTOT')
            ->setCellValue('T1', 'NEGARA DTTOT')
            ->setCellValue('U1', 'ALAMAT DTTOT')
            ->setCellValue('V1', 'KETERANGAN DTTOT')
                ;

        $objPHPExcel->getActiveSheet()->getStyle('A1:V1')->getFont()->setBold(true);  
        $no=1;
        $row=2; 

        if(count($array) > 0){
            //Put each record in a new cell
            foreach ($array as $a){
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $a['nama']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $a['nomor_akun']);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $a['tanggal_registrasi']);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $a['tanggal_nasabah']);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $a['jenis_identitas']);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $a['nomor_identitas']);
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $a['masa_berlaku']);
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $a['npwp']);
                $objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $a['tempat_lahir'].' / '.$a['tanggal_lahir']);
                $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $a['jenis_kelamin']);
                $objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $a['profesi']);
                $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $a['nomor_rekening_bank']);
                $objPHPExcel->getActiveSheet()->setCellValue('M'.$row, $a['nomor_telepon']);
                $objPHPExcel->getActiveSheet()->setCellValue('N'.$row, $a['email']);
                $objPHPExcel->getActiveSheet()->setCellValue('O'.$row, $a['wilayah_domisili']);
                $objPHPExcel->getActiveSheet()->setCellValue('P'.$row, $a['alamat']); 
                $objPHPExcel->getActiveSheet()->setCellValue('Q'.$row, $a['nama_dttot']);
                $objPHPExcel->getActiveSheet()->setCellValue('R'.$row, $a['alias_dttot']);
                $objPHPExcel->getActiveSheet()->setCellValue('S'.$row, $a['lahir_dttot']);
                $objPHPExcel->getActiveSheet()->setCellValue('T'.$row, $a['negara_dttot']);
                $objPHPExcel->getActiveSheet()->setCellValue('U'.$row, $a['alamat_dttot']);
                $objPHPExcel->getActiveSheet()->setCellValue('V'.$row, $a['keterangan_dttot']);
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

    public function excelDttotNonPerorangan(Request $request){
        $array = $request->data;
        $fileName = $request->fileName.'.xls';      
        $objPHPExcel = new PHPExcel(); 
        $objPHPExcel->setActiveSheetIndex(0); 
        $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', 'NOMOR AKUN')
        ->setCellValue('B1', 'NAMA PERUSAHAAN')
        ->setCellValue('C1', 'NOMOR IJIN USAHA')
        ->setCellValue('D1', 'BIDANG USAHA')
        ->setCellValue('E1', 'TEMPAT PENDIRIAN , TANGGAL PENDIRIAN') 
        ->setCellValue('F1', 'BENTUK HUKUM') 
        ->setCellValue('G1', 'NOMOR NPWP PERUSAHAAN ')
        ->setCellValue('H1', 'PROFIL PERUSAHAAN')
        ->setCellValue('I1', 'NOMOR REKENING PERUSAHAAN')
        ->setCellValue('J1', 'NOMOR TELEPON PERUSAHAAN')
        ->setCellValue('K1', 'EMAIL PERUSAHAAN')  
        ->setCellValue('L1', 'WILAYAH DOMISILI PERUSAHAAN')
        ->setCellValue('M1', 'ALAMAT PERUSAHAAN')
        ->setCellValue('N1', 'NAMA BENEFECIAL OWNER')
        ->setCellValue('O1', 'NAMA ALIAS BENEFECIAL OWNER')
        ->setCellValue('P1', 'JENIS KARTU IDENTITAS BENEFECIAL OWNER')
        ->setCellValue('Q1', 'NOMOR IDENTITAS BENEFECIAL OWNER') 
        ->setCellValue('R1', 'MASA BERLAKU BENEFECIAL OWNER') 
        ->setCellValue('S1', 'NOMOR NPWP BENEFECIAL OWNER')
        ->setCellValue('T1', 'TEMPAT / TANGGAL LAHIR BENEFECIAL OWNER')
        ->setCellValue('U1', 'STATUS PERKAWINAN BENEFECIAL OWNER')
        ->setCellValue('V1', 'JENIS KELAMIN BENEFECIAL OWNER')
        ->setCellValue('W1', 'NOMOR TELEPON BENEFECIAL OWNER') 
        ->setCellValue('X1', 'ALAMAT IDENTITAS BENEFECIAL OWNER') 
        ->setCellValue('Y1', 'ALAMAT BENEFECIAL OWNER')  
        ->setCellValue('Z1', 'NAMA PENERIMA KUASA')
        ->setCellValue('AA1', 'NAMA ALIAS PENERIMA KUASA')
        ->setCellValue('AB1', 'JENIS KARTU IDENTITAS PENERIMA KUASA')
        ->setCellValue('AC1', 'NOMOR IDENTITAS PENERIMA KUASA') 
        ->setCellValue('AD1', 'MASA BERLAKU PENERIMA KUASA') 
        ->setCellValue('AE1', 'NOMOR NPWP PENERIMA KUASA')
        ->setCellValue('AF1', 'TEMPAT / TANGGAL LAHIR PENERIMA KUASA')
        ->setCellValue('AG1', 'STATUS PERKAWINAN PENERIMA KUASA')
        ->setCellValue('AH1', 'JENIS KELAMIN PENERIMA KUASA')
        ->setCellValue('AI1', 'NOMOR TELEPON PENERIMA KUASA') 
        ->setCellValue('AJ1', 'ALAMAT IDENTITAS PENERIMA KUASA') 
        ->setCellValue('AK1', 'ALAMAT PENERIMA KUASA')   
        ->setCellValue('AL1', 'NAMA DTTOT')
        ->setCellValue('AM1', 'NAMA DTTOT ALIAS')
        ->setCellValue('AN1', 'TEMPAT TANGGAL LAHIR DTTOT')
        ->setCellValue('AO1', 'NEGARA DTTOT')
        ->setCellValue('AP1', 'ALAMAT DTTOT')
        ->setCellValue('AQ1', 'KETERANGAN DTTOT')
                ;

        $objPHPExcel->getActiveSheet()->getStyle('A1:AQ1')->getFont()->setBold(true);  
        $no=1;
        $row=2; 

        if(count($array) > 0){
            //Put each record in a new cell
            foreach ($array as $a){
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $a['nomor_akun']);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $a['nama_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $a['nomor_ijin_usaha']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $a['bidang_usaha']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $a['tempat_pendirian'].'/'.$a['tanggal_pendirian']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $a['bentuk_hukum']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $a['npwp_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $a['profil_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $a['no_rekening_bank_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $a['nomor_telepon_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $a['email_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $a['wilayah_domisili_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$row, $a['alamat_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$row, $a['nama_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$row, $a['nama_alias_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$row, $a['jenis_kartu_identitas_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$row, $a['nomor_identitas_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$row, $a['masa_berlaku_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$row, $a['npwp_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$row, $a['tempat_lahir_bo'].'/'.$a['tanggal_lahir_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('U'.$row, $a['status_perkawinan_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$row, $a['jenis_kelamin_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('W'.$row, $a['nomor_telepon_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('X'.$row, $a['alamat_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('Y'.$row, $a['alamat_sekarang_bo']); 
                    $objPHPExcel->getActiveSheet()->setCellValue('Z'.$row, $a['nama_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AA'.$row, $a['nama_alias_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$row, $a['jenis_kartu_identitas_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AC'.$row, $a['nomor_identitas_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AD'.$row, $a['masa_berlaku_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AE'.$row, $a['npwp_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AF'.$row, $a['tempat_lahir_knp'].'/'.$a['tanggal_lahir_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AG'.$row, $a['status_perkawinan_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AH'.$row, $a['jenis_kelamin_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AI'.$row, $a['nomor_telepon_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$row, $a['alamat_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AK'.$row, $a['alamat_sekarang_knp']);  
                    $objPHPExcel->getActiveSheet()->setCellValue('AL'.$row, $a['nama_dttot']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AM'.$row, $a['alias_dttot']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AN'.$row, $a['lahir_dttot']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AO'.$row, $a['negara_dttot']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AP'.$row, $a['alamat_dttot']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AQ'.$row, $a['keterangan_dttot']);
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

    public function cobaDttotNonPerorangan(Request $request){
        $id = $request->id;
        $filename = $request->filename;
        $x=0;
        $data = DataExtract::where('dokumen_dttot_id','5')->get();
        $dataNonPerorangan = ViewProfilNonPerorangan::orderBy('id','DESC')->get();
        $res = [];

        if(count($data) > 0){

            foreach($dataNonPerorangan as $d){
                $like  = "%{$d->nama_perusahaan}%";
                $like2 = "%{$d->no_rekening_bank_perusahaan}%";
                $like3 = "%{$d->nama_bo}%";
                $like4 = "%{$d->nama_alias_bo}%";
                $like5 = "%{$d->nomor_identitas_bo}%"; 
                $like6 = "%{$d->nama_knp}%";
                $like7 = "%{$d->nama_alias_knp}%";
                $like8 = "%{$d->nomor_identitas_knp}%"; 
                $cek =  DataExtract::where([['dokumen_dttot_id',$id],['string', 'LIKE', $like]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like2]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like3]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like4]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like5]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like5]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like6]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like7]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like8]])->get();
                if(count($cek) > 0){
                    $decode = $cek;
                    $res[$x] = array(
                                'nomor_akun'                    => $d->nomor_akun,
                                'nama_perusahaan'               => $d->nama_perusahaan,
                                'nomor_ijin_usaha'              => $d->nomor_ijin_usaha,
                                'bidang_usaha'                  => $d->bidang_usaha,
                                'tempat_pendirian'              => $d->tempat_pendirian,
                                'tanggal_pendirian'             => $d->tanggal_pendirian, 
                                'bentuk_hukum'                  => $d->bentuk_hukum,
                                'npwp_perusahaan'               => $d->npwp_perusahaan,
                                'profil_perusahaan'             => $d->profil_perusahaan,
                                'no_rekening_bank_perusahaan'   => $d->no_rekening_bank_perusahaan,
                                'nomor_telepon_perusahaan'      => $d->nomor_telepon_perusahaan,
                                'email_perusahaan'              => $d->email_perusahaan,
                                'wilayah_domisili_perusahaan'   => $d->wilayah_domisili_perusahaan,
                                'alamat_perusahaan'             => $d->alamat_perusahaan,
                                'nama_bo'                       => $d->nama_bo,
                                'nama_alias_bo'                 => $d->nama_alias_bo,
                                'jenis_kartu_identitas_bo'      => $d->jenis_kartu_identitas_bo, 
                                'nomor_identitas_bo'            => $d->nomor_identitas_bo, 
                                'masa_berlaku_bo'               => $d->masa_berlaku_bo, 
                                'npwp_bo'                       => $d->npwp_bo, 
                                'tempat_lahir_bo'               => $d->tempat_lahir_bo, 
                                'tanggal_lahir_bo'              => $d->tanggal_lahir_bo, 
                                'status_perkawinan_bo'          => $d->status_perkawinan_bo, 
                                'jenis_kelamin_bo'              => $d->jenis_kelamin_bo, 
                                'nomor_telepon_bo'              => $d->nomor_telepon_bo, 
                                'alamat_bo'                     => $d->alamat_bo, 
                                'alamat_sekarang_bo'            => $d->alamat_sekarang_bo, 
                                'nama_knp'                      => $d->nama_knp, 
                                'nama_alias_knp'                => $d->nama_alias_knp, 
                                'jenis_kartu_identitas_knp'     => $d->jenis_kartu_identitas_knp, 
                                'nomor_identitas_knp'           => $d->nomor_identitas_knp, 
                                'masa_berlaku_knp'              => $d->masa_berlaku_knp, 
                                'npwp_knp'                      => $d->npwp_knp, 
                                'tempat_lahir_knp'              => $d->tempat_lahir_knp, 
                                'tanggal_lahir_knp'             => $d->tanggal_lahir_knp, 
                                'status_perkawinan_knp'         => $d->status_perkawinan_knp, 
                                'jenis_kelamin_knp'             => $d->jenis_kelamin_knp, 
                                'nomor_telepon_knp'             => $d->nomor_telepon_knp, 
                                'alamat_knp'                    => $d->alamat_knp, 
                                'alamat_sekarang_knp'           => $d->alamat_sekarang_knp, 
                                'dttot'                         => $decode
                            ); 
                }
    
                $x++;
            } 
            print_r($res);  
        }else{ 
            print_r($res);
        }

    }

    
    public function downloadDttotNonPerorangan(Request $request){
        $id = $request->id;
        $filename = $request->filename;
        $x=0;
        $data = DataExtract::where('dokumen_dttot_id','5')->get();
        $dataNonPerorangan = ViewProfilNonPerorangan::orderBy('id','DESC')->get();
        $res = [];

        if(count($data) > 0){

            foreach($dataNonPerorangan as $d){
                $like  = "%{$d->nama_perusahaan}%";
                $like2 = "%{$d->no_rekening_bank_perusahaan}%";
                $like3 = "%{$d->nama_bo}%";
                $like4 = "%{$d->nama_alias_bo}%";
                $like5 = "%{$d->nomor_identitas_bo}%"; 
                $like6 = "%{$d->nama_knp}%";
                $like7 = "%{$d->nama_alias_knp}%";
                $like8 = "%{$d->nomor_identitas_knp}%"; 
                $cek =  DataExtract::where([['dokumen_dttot_id',$id],['string', 'LIKE', $like]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like2]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like3]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like4]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like5]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like5]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like6]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like7]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like8]])->get();
                if(count($cek) > 0){
                    $decode = $cek;
                    $res[$x] = array(
                                'nomor_akun'                    => $d->nomor_akun,
                                'nama_perusahaan'               => $d->nama_perusahaan,
                                'nomor_ijin_usaha'              => $d->nomor_ijin_usaha,
                                'bidang_usaha'                  => $d->bidang_usaha,
                                'tempat_pendirian'              => $d->tempat_pendirian,
                                'tanggal_pendirian'             => $d->tanggal_pendirian, 
                                'bentuk_hukum'                  => $d->bentuk_hukum,
                                'npwp_perusahaan'               => $d->npwp_perusahaan,
                                'profil_perusahaan'             => $d->profil_perusahaan,
                                'no_rekening_bank_perusahaan'   => $d->no_rekening_bank_perusahaan,
                                'nomor_telepon_perusahaan'      => $d->nomor_telepon_perusahaan,
                                'email_perusahaan'              => $d->email_perusahaan,
                                'wilayah_domisili_perusahaan'   => $d->wilayah_domisili_perusahaan,
                                'alamat_perusahaan'             => $d->alamat_perusahaan,
                                'nama_bo'                       => $d->nama_bo,
                                'nama_alias_bo'                 => $d->nama_alias_bo,
                                'jenis_kartu_identitas_bo'      => $d->jenis_kartu_identitas_bo, 
                                'nomor_identitas_bo'            => $d->nomor_identitas_bo, 
                                'masa_berlaku_bo'               => $d->masa_berlaku_bo, 
                                'npwp_bo'                       => $d->npwp_bo, 
                                'tempat_lahir_bo'               => $d->tempat_lahir_bo, 
                                'tanggal_lahir_bo'              => $d->tanggal_lahir_bo, 
                                'status_perkawinan_bo'          => $d->status_perkawinan_bo, 
                                'jenis_kelamin_bo'              => $d->jenis_kelamin_bo, 
                                'nomor_telepon_bo'              => $d->nomor_telepon_bo, 
                                'alamat_bo'                     => $d->alamat_bo, 
                                'alamat_sekarang_bo'            => $d->alamat_sekarang_bo, 
                                'nama_knp'                      => $d->nama_knp, 
                                'nama_alias_knp'                => $d->nama_alias_knp, 
                                'jenis_kartu_identitas_knp'     => $d->jenis_kartu_identitas_knp, 
                                'nomor_identitas_knp'           => $d->nomor_identitas_knp, 
                                'masa_berlaku_knp'              => $d->masa_berlaku_knp, 
                                'npwp_knp'                      => $d->npwp_knp, 
                                'tempat_lahir_knp'              => $d->tempat_lahir_knp, 
                                'tanggal_lahir_knp'             => $d->tanggal_lahir_knp, 
                                'status_perkawinan_knp'         => $d->status_perkawinan_knp, 
                                'jenis_kelamin_knp'             => $d->jenis_kelamin_knp, 
                                'nomor_telepon_knp'             => $d->nomor_telepon_knp, 
                                'alamat_knp'                    => $d->alamat_knp, 
                                'alamat_sekarang_knp'           => $d->alamat_sekarang_knp, 
                                'dttot'                         => $decode
                            ); 
                }
    
                $x++;
            } 
            if($x<0){
                
                $res[$x] = array(); 

                $this->downloadCompareNonPerorangan($res);
            }else{
                $this->downloadCompareNonPerorangan($res);
            }  
        }else{
            
            $res = array(); 

             $this->downloadCompareNonPerorangan($res);
        }

    }
    

    public function downloadDttotPerorangan(Request $request){
        $id = $request->id;
        $filename = $request->filename;
        $x=0;
        $data = DataExtract::where('dokumen_dttot_id',$id)->get();
        $dataKaryawan = Perorangan::orderBy('id','DESC')->get();
        $res = [];

        if(count($data) > 0){

            foreach($dataKaryawan as $d){
                $like  = "%{$d->nama}%";
                $like2 = "%{$d->nomor_identitas}%";
                $like3 = "%{$d->npwp}%";
                $like4 = "%{$d->nomor_rekening_bank}%";
                $like5 = "%{$d->nomor_telepon}%"; 
                $cek =  DataExtract::where([['dokumen_dttot_id',$id],['string', 'LIKE', $like]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like2]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like3]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like4]])->orWhere([['dokumen_dttot_id',$id],['string', 'LIKE', $like5]])->get();
                if(count($cek) > 0){
                    $decode = $cek;
                    $res[$x] = array('nama'=>$d->nama,
                                'nomor_akun'=>$d->nomor_akun,
                                'tanggal_registrasi'=>$d->tanggal_registrasi,
                                'tanggal_nasabah'=>$d->tanggal_nasabah,
                                'jenis_identitas'=>$d->jenis_identitas,
                                'nomor_identitas'=>$d->nomor_identitas,
                                'masa_berlaku'=>$d->masa_berlaku,
                                'npwp'=>$d->npwp,
                                'tempat_lahir'=>$d->tempat_lahir,
                                'tanggal_lahir'=>$d->tanggal_lahir,
                                'jenis_kelamin'=>$d->jenis_kelamin,
                                'profesi'=>$d->profesi,
                                'nomor_rekening_bank'=>$d->nomor_rekening_bank,
                                'nomor_telepon'=>$d->nomor_telepon,
                                'email'=>$d->email,
                                'wilayah_domisili'=>$d->wilayah_domisili,
                                'alamat'=>$d->alamat, 
                                'dttot'=>$decode); 
                }
    
                $x++;
            } 
            if($x<0){
                
            $res = array(); 

                $this->downloadComparePerorangan($res);
            }else{
                $this->downloadComparePerorangan($res);
            }  
        }else{
            
            $res = array(); 

             $this->downloadComparePerorangan($res);
        }

    }

    public function downloadDttot(Request $request){
        $id = $request->id;
        $filename = $request->filename;
        $x=0;
        $data = DataExtract::where('dokumen_dttot_id',$id)->get();
        $res = [];

        if(count($data) > 0){
            foreach($data as $d){
                $array = json_decode($d->string);
                $res[$x] = $this->getDataFromArray($array);
    
                $x++;
            }
    
            $this->downloadExcel($res);
        }else{
            $this->downloadExcel(array());
        }

    }

    public function downloadFileDttot(Request $request){
        $fileName = $request->filename;
        $path = public_path().'/dokumen-dttot/'.$fileName; 
        return response()->download($path); 
    }

    private function getDataFromArray($array){
        $nama ='';
        $alias ='';
        $lahir ='';
        $negara = '';
        $alamat = '';
        $keterangan = '';
        for($x=0;$x < count($array);$x++){

            if($x == 1){
                $nama = $this->extractDataFromParam($array[1],';');
            }

            
            if($x == 2){
                $alias = $this->extractDataFromParam($array[2],';');
            }

            
            if($x == 3){
                $lahir = $this->extractDataFromParam($array[3],';');
            }

            if($x == 4){
                $negara = $this->extractDataFromParam($array[4],';');
            }
            
 
            if($x == 5){
                $alamat = $this->extractDataFromParam($array[5],';');
            } 

            
            if($x >= 6){
                $explode = explode(';', $array[$x]); 
                foreach($explode as $e){
                    $keterangan .= $e;
                }  
            } 

            
        }

        return array('nama'=>$nama,'alias'=>$alias,'lahir'=>$lahir,'negara'=>$negara,'alamat'=>$alamat,'keterangan'=>$keterangan);
    }

    private function extractDataFromParam($data,$param)
    {
        $res = substr($data, 0, strpos($data, $param)); 
        return $res; 
    }


    private function downloadExcel($array)
    {
        $fileName = 'export';            
        $objPHPExcel = new PHPExcel(); 
        $objPHPExcel->setActiveSheetIndex(0); 
        $objPHPExcel->getActiveSheet()
                    ->setCellValue('A1', 'NAMA')
                    ->setCellValue('B1', 'NAMA ALIAS')
                    ->setCellValue('C1', 'TEMPAT TANGGAL LAHIR')
                    ->setCellValue('D1', 'NEGARA')
                    ->setCellValue('E1', 'ALAMAT')
                    ->setCellValue('F1', 'KETERANGAN')
                    ;

        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);  
        $no=1;
        $row=2; 
        if(count($array) > 0){
            //Put each record in a new cell
            foreach ($array as $a){ 
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $a['nama']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $a['alias']);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $a['lahir']);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $a['negara']);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $a['alamat']);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $a['keterangan']);
            $no++; 
            $row++;   
            } 
        }
         
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1'); 
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); 

    }

    private function arrayCompareKarywan($array){
        $no=0; 
        $res = [];
        if(count($array) > 0){
            foreach ($array as $a){
                foreach ($a['dttot'] as $d){
                    $ss = json_decode($d->string);
                    $res = $this->getDataFromArray($ss); 
                    $res[$no] = ['nama_karyawan'=>$a['nama_karyawan'],'nomor_aplikasi'=>$a['nomor_aplikasi'], 'jabatan'=>$a['jabatan'], 'divisi'=>$a['divisi'], 'awal_masuk'=>$a['awal_masuk'], 'nama'=>$res['nama'], 'alias'=>$res['alias'], 'lahir'=>$res['lahir'], 'negara'=>$res['negara'], 'alamat'=>$res['alamat'], 'keterangan'=>$res['keterangan']];
                    $no++;
                }
            }
        }

        return response()->json($res);
    }

    public function excelDttotKaryawan(Request $request){
        $array = $request->data;
        $fileName = $request->fileName.'.xls';      
        $objPHPExcel = new PHPExcel(); 
        $objPHPExcel->setActiveSheetIndex(0); 
        $objPHPExcel->getActiveSheet()
                    ->setCellValue('A1', 'NAMA KARYAWAN')
                    ->setCellValue('B1', 'NOMOR APLIKASI')
                    ->setCellValue('C1', 'JABATAN')
                    ->setCellValue('D1', 'DIVISI')
                    ->setCellValue('E1', 'AWAL MASUK') 
                    ->setCellValue('F1', 'NAMA DTTOT')
                    ->setCellValue('G1', 'NAMA DTTOT ALIAS')
                    ->setCellValue('H1', 'TEMPAT TANGGAL LAHIR DTTOT')
                    ->setCellValue('I1', 'NEGARA DTTOT')
                    ->setCellValue('J1', 'ALAMAT DTTOT')
                    ->setCellValue('K1', 'KETERANGAN DTTOT')
                    ;

        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold(true);  
        $no=1;
        $row=2; 

        if(count($array) > 0){
            //Put each record in a new cell
            foreach ($array as $a){
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $a['nama_karyawan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $a['nomor_aplikasi']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $a['jabatan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $a['divisi']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $a['awal_masuk']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $a['nama']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $a['alias']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $a['lahir']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $a['negara']);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $a['alamat']);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $a['keterangan']);
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

    private function downloadCompareKarywan($array){ 
        $fileName = 'export';            
        $objPHPExcel = new PHPExcel(); 
        $objPHPExcel->setActiveSheetIndex(0); 
        $objPHPExcel->getActiveSheet()
                    ->setCellValue('A1', 'NAMA KARYAWAN')
                    ->setCellValue('B1', 'NOMOR APLIKASI')
                    ->setCellValue('C1', 'JABATAN')
                    ->setCellValue('D1', 'DIVISI')
                    ->setCellValue('E1', 'AWAL MASUK') 
                    ->setCellValue('F1', 'NAMA DTTOT')
                    ->setCellValue('G1', 'NAMA DTTOT ALIAS')
                    ->setCellValue('H1', 'TEMPAT TANGGAL LAHIR DTTOT')
                    ->setCellValue('I1', 'NEGARA DTTOT')
                    ->setCellValue('J1', 'ALAMAT DTTOT')
                    ->setCellValue('K1', 'KETERANGAN DTTOT')
                    ;

        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold(true);  
        $no=1;
        $row=2; 

        if(count($array) > 0){
            //Put each record in a new cell
            foreach ($array as $a){
                foreach ($a['dttot'] as $d){
                    $array = json_decode($d->string);
                    $res = $this->getDataFromArray($array); 
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $a['nama_karyawan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $a['nomor_aplikasi']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $a['jabatan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $a['divisi']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $a['awal_masuk']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $res['nama']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $res['alias']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $res['lahir']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $res['negara']);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $res['alamat']);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $res['keterangan']);
                    $no++; 
                    $row++; 
                } 
            }

        }
         
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1'); 
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); 

    }

    
    private function downloadComparePerorangan($array)
    {
        $fileName = 'export';            
        $objPHPExcel = new PHPExcel(); 
        $objPHPExcel->setActiveSheetIndex(0); 
        $objPHPExcel->getActiveSheet()
                    ->setCellValue('A1', 'NAMA PERORANGAN')
                    ->setCellValue('B1', 'NOMOR AKUN')
                    ->setCellValue('C1', 'TANGGAL REGISTRASI')
                    ->setCellValue('D1', 'TANGGAL MENJADI NASABAH') 
                    ->setCellValue('E1', 'JENIS IDENTITAS') 
                    ->setCellValue('F1', 'NOMOR IDENTITAS')
                    ->setCellValue('G1', 'MASA BERLAKU')
                    ->setCellValue('H1', 'NOMOR NPWP')
                    ->setCellValue('I1', 'TAMPAT / TANGGAL LAHIR')
                    ->setCellValue('J1', 'JENIS KELAMIN')
                    ->setCellValue('K1', 'PROFESI')
                    ->setCellValue('L1', 'NOMOR RREKENING BANK')
                    ->setCellValue('M1', 'NOMOR TELEPON')
                    ->setCellValue('N1', 'EMAIL')
                    ->setCellValue('O1', 'WILAYAH DOMISILI')
                    ->setCellValue('P1', 'ALAMAT')
                    ->setCellValue('Q1', 'NAMA DTTOT')
                    ->setCellValue('R1', 'NAMA DTTOT ALIAS')
                    ->setCellValue('S1', 'TEMPAT TANGGAL LAHIR DTTOT')
                    ->setCellValue('T1', 'NEGARA DTTOT')
                    ->setCellValue('U1', 'ALAMAT DTTOT')
                    ->setCellValue('V1', 'KETERANGAN DTTOT')
                    ;

        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);  
        $no=1;
        $row=2; 
        if(count($array) > 0){
            //Put each record in a new cell
            foreach ($array as $a){ 
                foreach ($a['dttot'] as $d){
                    $array = json_decode($d->string);
                    $res = $this->getDataFromArray($array); 
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $a['nama']);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $a['nomor_akun']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $a['tanggal_registrasi']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $a['tanggal_nasabah']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $a['jenis_identitas']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $a['nomor_identitas']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $a['masa_berlaku']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $a['npwp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $a['tempat_lahir'].' / '.$a['tanggal_lahir']);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $a['jenis_kelamin']);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $a['profesi']);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $a['nomor_rekening_bank']);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$row, $a['nomor_telepon']);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$row, $a['email']);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$row, $a['wilayah_domisili']);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$row, $a['alamat']); 
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$row, $res['nama']);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$row, $res['alias']);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$row, $res['lahir']);
                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$row, $res['negara']);
                    $objPHPExcel->getActiveSheet()->setCellValue('U'.$row, $res['alamat']);
                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$row, $res['keterangan']);
                    $no++; 
                    $row++;

                } 
            }
        }
         
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1'); 
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); 

    }

    
    private function downloadCompareNonPerorangan($array)
    {
        $fileName = 'export';            
        $objPHPExcel = new PHPExcel(); 
        $objPHPExcel->setActiveSheetIndex(0); 
        $objPHPExcel->getActiveSheet()
                    ->setCellValue('A1', 'NOMOR AKUN')
                    ->setCellValue('B1', 'NAMA PERUSAHAAN')
                    ->setCellValue('C1', 'NOMOR IJIN USAHA')
                    ->setCellValue('D1', 'BIDANG USAHA')
                    ->setCellValue('E1', 'TEMPAT PENDIRIAN , TANGGAL PENDIRIAN') 
                    ->setCellValue('F1', 'BENTUK HUKUM') 
                    ->setCellValue('G1', 'NOMOR NPWP PERUSAHAAN ')
                    ->setCellValue('H1', 'PROFIL PERUSAHAAN')
                    ->setCellValue('I1', 'NOMOR REKENING PERUSAHAAN')
                    ->setCellValue('J1', 'NOMOR TELEPON PERUSAHAAN')
                    ->setCellValue('K1', 'EMAIL PERUSAHAAN')  
                    ->setCellValue('L1', 'WILAYAH DOMISILI PERUSAHAAN')
                    ->setCellValue('M1', 'ALAMAT PERUSAHAAN')
                    ->setCellValue('N1', 'NAMA BENEFECIAL OWNER')
                    ->setCellValue('O1', 'NAMA ALIAS BENEFECIAL OWNER')
                    ->setCellValue('P1', 'JENIS KARTU IDENTITAS BENEFECIAL OWNER')
                    ->setCellValue('Q1', 'NOMOR IDENTITAS BENEFECIAL OWNER') 
                    ->setCellValue('R1', 'MASA BERLAKU BENEFECIAL OWNER') 
                    ->setCellValue('S1', 'NOMOR NPWP BENEFECIAL OWNER')
                    ->setCellValue('T1', 'TEMPAT / TANGGAL LAHIR BENEFECIAL OWNER')
                    ->setCellValue('U1', 'STATUS PERKAWINAN BENEFECIAL OWNER')
                    ->setCellValue('V1', 'JENIS KELAMIN BENEFECIAL OWNER')
                    ->setCellValue('W1', 'NOMOR TELEPON BENEFECIAL OWNER') 
                    ->setCellValue('X1', 'ALAMAT IDENTITAS BENEFECIAL OWNER') 
                    ->setCellValue('Y1', 'ALAMAT BENEFECIAL OWNER')  
                    ->setCellValue('Z1', 'NAMA PENERIMA KUASA')
                    ->setCellValue('AA1', 'NAMA ALIAS PENERIMA KUASA')
                    ->setCellValue('AB1', 'JENIS KARTU IDENTITAS PENERIMA KUASA')
                    ->setCellValue('AC1', 'NOMOR IDENTITAS PENERIMA KUASA') 
                    ->setCellValue('AD1', 'MASA BERLAKU PENERIMA KUASA') 
                    ->setCellValue('AE1', 'NOMOR NPWP PENERIMA KUASA')
                    ->setCellValue('AF1', 'TEMPAT / TANGGAL LAHIR PENERIMA KUASA')
                    ->setCellValue('AG1', 'STATUS PERKAWINAN PENERIMA KUASA')
                    ->setCellValue('AH1', 'JENIS KELAMIN PENERIMA KUASA')
                    ->setCellValue('AI1', 'NOMOR TELEPON PENERIMA KUASA') 
                    ->setCellValue('AJ1', 'ALAMAT IDENTITAS PENERIMA KUASA') 
                    ->setCellValue('AK1', 'ALAMAT PENERIMA KUASA')   
                    ->setCellValue('AL1', 'NAMA DTTOT')
                    ->setCellValue('AM1', 'NAMA DTTOT ALIAS')
                    ->setCellValue('AN1', 'TEMPAT TANGGAL LAHIR DTTOT')
                    ->setCellValue('AO1', 'NEGARA DTTOT')
                    ->setCellValue('AP1', 'ALAMAT DTTOT')
                    ->setCellValue('AQ1', 'KETERANGAN DTTOT')
                    ;

        $objPHPExcel->getActiveSheet()->getStyle('A1:AQ1')->getFont()->setBold(true);  
        $no=1;
        $row=2;
        
        if(count($array) > 0){ 
            //Put each record in a new cell
            foreach ($array as $a){ 
                foreach ($a['dttot'] as $d){
                    $array = json_decode($d->string);
                    $res = $this->getDataFromArray($array); 
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $a['nomor_akun']);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $a['nama_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $a['nomor_ijin_usaha']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $a['bidang_usaha']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $a['tempat_pendirian'].'/'.$a['tanggal_pendirian']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $a['bentuk_hukum']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $a['npwp_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $a['profil_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $a['no_rekening_bank_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $a['nomor_telepon_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $a['email_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $a['wilayah_domisili_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$row, $a['alamat_perusahaan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$row, $a['nama_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$row, $a['nama_alias_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$row, $a['jenis_kartu_identitas_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$row, $a['nomor_identitas_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$row, $a['masa_berlaku_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$row, $a['npwp_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$row, $a['tempat_lahir_bo'].'/'.$a['tanggal_lahir_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('U'.$row, $a['status_perkawinan_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$row, $a['jenis_kelamin_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('W'.$row, $a['nomor_telepon_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('X'.$row, $a['alamat_bo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('Y'.$row, $a['alamat_sekarang_bo']); 
                    $objPHPExcel->getActiveSheet()->setCellValue('Z'.$row, $a['nama_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AA'.$row, $a['nama_alias_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$row, $a['jenis_kartu_identitas_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AC'.$row, $a['nomor_identitas_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AD'.$row, $a['masa_berlaku_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AE'.$row, $a['npwp_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AF'.$row, $a['tempat_lahir_knp'].'/'.$a['tanggal_lahir_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AG'.$row, $a['status_perkawinan_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AH'.$row, $a['jenis_kelamin_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AI'.$row, $a['nomor_telepon_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$row, $a['alamat_knp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AK'.$row, $a['alamat_sekarang_knp']);  
                    $objPHPExcel->getActiveSheet()->setCellValue('AL'.$row, $res['nama']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AM'.$row, $res['alias']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AN'.$row, $res['lahir']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AO'.$row, $res['negara']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AP'.$row, $res['alamat']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AQ'.$row, $res['keterangan']);
                    $no++; 
                    $row++;

                } 
            }
        }
         
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1'); 
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); 

    }

}