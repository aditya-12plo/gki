<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Response,View,Input,Auth,Session,Validator,File,Hash,DB,Mail;
use Illuminate\Support\Facades\Crypt;
use PHPExcel; 
use PHPExcel_IOFactory;


use App\Models\LogActivity;
use App\Models\Jabatan;

class JabatanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
        $query = Jabatan::orderBy('id','DESC');
        if ($search) {
            $like = "%{$search}%";
            $query = $query->where('jabatan', 'LIKE', $like);
        }
        if($min && !$max)
        {
            $query = $query->whereDate('created_at','=',$min);
        }
        if(!$min && $max)
        {
            $query = $query->whereDate('created_at','=',$max);
        }
        if($min && $max)
        {
            $query = $query->whereDate('created_at','>=',$min)->whereDate('created_at','<=',$max);
        }
         
        return $query->paginate($perPage);
    }

    public function store(Request $request)
    { 
        $valid = $this->validate($request, [ 
            'jabatan' => 'required|max:255|unique:jabatan,jabatan',
        ]);
        $masuk = array('jabatan' => $request->jabatan); 
        Jabatan::create($masuk);
        LogActivity::create(['name' => Auth::user()->id, 'email' => Auth::user()->email, 'table'=>'jabatan' ,'action' => 'insert', 'data' => json_encode($masuk)]);
        return response()->json(['status'=>200,'data'=>'','message'=>'Add Successfully']);
    }


    public function destroy($id)
    {
        $cek = Jabatan::findOrFail($id);
        if(!$cek)
        {
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
            LogActivity::create(['name' => Auth::user()->id, 'email' => Auth::user()->email, 'table'=>'jabatan' ,'action' => 'delete', 'data' => json_encode($cek)]);
            Jabatan::where('id',$id)->delete();
            return response()->json(['status'=>200,'data'=>'','message'=>'Delete Successfully']);
        } 

    }

    public function update(Request $request, $id)
    {
        $cek = Jabatan::findOrFail($id);
        if(!$cek)
        {
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
            $valid = $this->validate($request, [ 
                'jabatan' => 'required|max:255|unique:jabatan,jabatan,'.$id,
            ]);
            $edit = array('jabatan' => $request->jabatan);
            $cek->update($edit);
            LogActivity::create(['name' => Auth::user()->id, 'email' => Auth::user()->email, 'table'=>'jabatan' ,'action' => 'update', 'data' => json_encode($cek)]);
            return response()->json(['status'=>200,'data'=>'','message'=>'Edit Successfully']);
        }

    }

    public function deleteAll($id)
    {
        $get =  Jabatan::whereIn('id',explode(",",$id))->get();
        LogActivity::create(['name' => Auth::user()->id, 'email' => Auth::user()->email, 'table'=>'jabatan' ,'action' => 'delete', 'data' => json_encode($get)]); 
        Jabatan::whereIn('id',explode(",",$id))->delete();
        return response()->json(['status'=>200,'data'=>'','message'=>'Delete Successfully']);
    }

    public function dropDown()
    {
        $get = Jabatan::select(['jabatan'])->orderBy('id','DESC')->get();
        return $get;
    }

    public function print(Request $request)
    { 
        $search = $request->filter;
        $min = $request->min;
        $max = $request->max;
        $query = Jabatan::select(['jabatan'])->orderBy('id','DESC');
        if ($search) {
            $like = "%{$search}%";
            $query = $query->where('jabatan', 'LIKE', $like);
        }
        if($min && !$max)
        {
            $query = $query->whereDate('created_at','=',$min);
        }
        if(!$min && $max)
        {
            $query = $query->whereDate('created_at','=',$max);
        }
        if($min && $max)
        {
            $query = $query->whereDate('created_at','>=',$min)->whereDate('created_at','<=',$max);
        }
         
        return array("gridColumns" =>["jabatan"],"gridData"=> $query->get());
    }

    public function downloadFile(Request $request){

        $search = $request->filter;
        $min = $request->min;
        $max = $request->max; 
        $filename = $request->filename;
        $x=0;
        $res = [];
        $query = Jabatan::select(['jabatan','created_at'])->orderBy('id','DESC');
        if ($search) {
            $like = "%{$search}%";
            $query = $query->where('jabatan', 'LIKE', $like);
        }
        if($min && !$max)
        {
            $query = $query->whereDate('created_at','=',$min);
        }
        if(!$min && $max)
        {
            $query = $query->whereDate('created_at','=',$max);
        }
        if($min && $max)
        {
            $query = $query->whereDate('created_at','>=',$min)->whereDate('created_at','<=',$max);
        }
        $data =  $query->get();

        $fileName = $filename;
        $objPHPExcel = new PHPExcel(); 
        $objPHPExcel->setActiveSheetIndex(0); $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', 'NAMA JABATAN')
        ->setCellValue('B1', 'TANGGAL DIBUAT');
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);  
        $no=1;
        $row=2;
        if(count($data) > 0){
            foreach ($data as $d){
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $d->jabatan);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $d->created_at);
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