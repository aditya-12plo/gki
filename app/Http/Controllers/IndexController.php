<?php
namespace App\Http\Controllers;
use App;
use Auth;
use Illuminate\Http\Request; 

use Smalot\PdfParser\Parser;
use PHPExcel; 
use PHPExcel_IOFactory;


use App\Models\DataExtract;

class IndexController extends Controller
{
	
	public function __construct()
    {
		
	}
	
	public function index()
    { 
        if(Auth::check()){
            return redirect('home');
        }else{ 
            return view('site.login');
        }
    }


    public function coba()
    {  
            return view('coba'); 
    }
    
    
	public function forgotPassword()
    { 
        if(Auth::check()){
            return redirect('home');
        }else{ 
            return view('site.forgot-password');
        }
    }
	
	public function pageNotFound()
    {   
        return view('site.error-404'); 
    }

    
	public function serverError()
    {   
        return view('site.error-500'); 
    }
    
	    public function lang($lang)
    {
        App::setLocale($lang);
        session()->put('locale', $lang);
        return redirect()->back();
    }




    public function dbTores(){
        $id = 3;
        $x=0;
        $data = DataExtract::where('dokumen_dttot_id',$id)->get();
        $res = [];
        foreach($data as $d){
            $array = json_decode($d->string);
            $res[$x] = $this->getDataFromArray($array);

            $x++;
        }

        $this->downloadExcel($res);
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
                $keterangan .= $this->extractDataFromParam($array[$x],';');
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
                    ->setCellValue('C1', 'TAMPAT TANGGAL LAHIR')
                    ->setCellValue('D1', 'NEGARA')
                    ->setCellValue('E1', 'ALAMAT')
                    ->setCellValue('F1', 'KETERANGAN')
                    ;

        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);  
        $no=1;
        $row=2; 
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
         
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1'); 
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); 

    }
    
    
    // public function potongString()
    // {  
    //     $str = '';
        
    //     $start = 'Kewarganegaraan';
    //     $end = 'Alamat'; 
        
    //     $pattern = sprintf(
    //         '/%s(.+?)%s/ims',
    //         preg_quote($start, '/'), preg_quote($end, '/')
    //     );
        
    //     if (preg_match($pattern, $str, $matches)) {
    //         list(, $match) = $matches;
    //         echo $match;
    //     }
    // }

    
    // // private function getNama($string)
    // // {      
    // //     $start = 'Nama :';
    // //     $end = ';'; 
        
    // //     $pattern = sprintf(
    // //         '/%s(.+?)%s/ims',
    // //         preg_quote($start, '/'), preg_quote($end, '/')
    // //     );
        
    // //     if (preg_match($pattern, $string, $matches)) {
    // //         list(, $match) = $matches;
    // //         return $match;
    // //     }
    // // }

    
    // private function getNamaAlias($string)
    // {      
    //     $start = 'Nama alias :';
    //     $end = ';'; 
        
    //     $pattern = sprintf(
    //         '/%s(.+?)%s/ims',
    //         preg_quote($start, '/'), preg_quote($end, '/')
    //     );
        
    //     if (preg_match($pattern, $string, $matches)) {
    //         list(, $match) = $matches;
    //         return $match;
    //     }
    // }
    
    // private function arrayToTextAlias($array){
    //     $x = 1;
    //     foreach($array as $a){ 
    //         $res = $this->getNamaAlias($a); 
    //         if(!$res)continue;
    //         echo $x.'. Nama Alias: '.$res.'<br>';  
    //         $x++;
    //     }
    // }

    
    // private function arrayToTextNama($array){
    //     $x = 1;
    //     foreach($array as $a){
    //         $res = $this->getNama($a); 
    //         if(!$res)continue;  
    //         echo $x.'. Nama : '.$res.'<br>'; 
    //         $x++;
    //     }
    // }

    // public function extractPDF(Request $request)
    // {     
    //     $path = public_path().'/invoice/book.pdf'; 
    //     $PDFParser = new Parser();
    //     $pdf = $PDFParser->parseFile($path);
    //     $pages  = $pdf->getPages();
    //     $totalPages = count($pages);
    //     $currentPage = 1;
    //     $text = ""; 
    //     $jml = 100;
    //     foreach ($pages as $page) { 
    //         $text .= $page->getText(); 
    //         $currentPage++;
    //     }

    //     $str = preg_replace('/(\v|\s)+/', ' ', $text);
    //     $add = str_replace('KEPOLISIAN NEGARA REPUBLIK INDONESIA MARKAS BESAR Jalan Trunojoyo 3, Kebayoran Baru, Jakarta 12110 “Pro Justitia ” DAFTAR TERDUGA TERORIS DAN ORGANISASI TERORIS', '', $str);
    //     $add = str_replace('I. INDIVIDU: ', '', $add);
    //     $add = str_replace('II. ENTITAS: ', '', $add);
    //     $variable = substr($add, 0, strpos($add, "III. KETERANGAN"));
    //     $start = 'Nomor:';
    //     $end = '1. Nama'; 
    //     $pattern = sprintf(
    //             '/%s(.+?)%s/ims',
    //             preg_quote($start, '/'), preg_quote($end, '/')
    //     ); 
    //     if (preg_match($pattern, $variable, $matches)) {
    //             list(, $match) = $matches;
    //             $concat = $start.$match;
    //             $nextString = str_replace($concat, "", $variable);   
    //             DataExtract::create(array('string'=>$nextString));
    //            // return $this->stringToArray($nextString,1);
    //         }
    //     else{
    //         return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File different format']]]);
    //     } 
       
    // }

    
    // public function extractName(){
    //     $cek =  DataExtract::findOrFail(1);

    //     $this->stringToArray($cek->string,1);
    // }



    // public function extractString(){
    //     $cek =  DataExtract::findOrFail(1);

    //     $this->stringToArray($cek->string,1);
    // }

    





    // private function stringToArray($string,$no)
    // {
    //     $jml= $no+1;
    //     for($no;$no<$jml;$no++){ 
    //         $start = ' '.$no.'.';
    //         $end = ' '.$jml.'.'; 
    //         $pattern = sprintf(
    //             '/%s(.+?)%s/ims',
    //             preg_quote($start, '/'), preg_quote($end, '/')
    //         );
    //         $rmv = $start.$end;
    //         if (preg_match($pattern, $string, $matches)) {
    //             list(, $match) = $matches;
    //             $concat = $start.$match;
    //             $nextString = str_replace($concat, "", $string); 
    //             $this->stringToArray($nextString,$jml); 
    //             $explode = explode(':', $match ); 
    //             $nama = $this->getNama($explode[1]);
    //             if(strlen($nama) < 1) continue;

    //             echo 'Nama : '.$nama;
    //             echo '<br>'; 
    //         }
    //     }
    // }

    // private function getNama($data)
    // {
    //     return substr($data, 0, strpos($data, "; N"));
    // }

    
    // private function getAlias($data)
    // {
    //     return substr($data, 0, strpos($data, "; K"));
    // }

    // private function getAlias2($data)
    // {
    //     return substr($data, 0, strpos($data, "; T"));
    // }  
    
    // public function extractArray($array)
    // {
    //     $inNama = ['Nama','N ama','Na ma','Nam a','N am a','N ama'];

    //     for($x=0;$x <= count($array);$x++){
    //         //if(in_array($array[0], $inNama)) continue;

    //         if($x = 1){ 
    //             echo 'Nama : '.str_replace('; Nama Alias','',$array[$x],0);
    //             echo '<br>';

    //         }
 
    //         // if($x = 2){ 
    //         //     echo 'Nama Alias : '.str_replace('; Kewarganegaraan','',$array[$x]);
    //         //     echo '<br>';
    //         // }

    //         // if($x = 3){ 
    //         //     echo 'Kewarganegaraan : '.str_replace('; Tempat','',$array[$x],0);
    //         //     echo '<br>';
    //         // }
    //     }
    // }
    
    // public function extractPDF2(Request $request)
    // {     
    //     $path = public_path().'/invoice/book.pdf'; 
    //     $PDFParser = new Parser();
    //     $pdf = $PDFParser->parseFile($path);
    //     $pages  = $pdf->getPages();
    //     $totalPages = count($pages);
    //     $currentPage = 1;
    //     $text = ""; 
    //     $jml = 100;
    //     foreach ($pages as $page) { 
    //         $text .= $page->getText(); 
    //         $currentPage++;
    //     }

    //     $str = preg_replace('/(\v|\s)+/', ' ', $text);
    //     $add = str_replace('KEPOLISIAN NEGARA REPUBLIK INDONESIA MARKAS BESAR Jalan Trunojoyo 3, Kebayoran Baru, Jakarta 12110 “Pro Justitia ” DAFTAR TERDUGA TERORIS DAN ORGANISASI TERORIS', '', $str);
    //     $add = str_replace('I. INDIVIDU: ', '', $add);
    //     $add = str_replace('II. ENTITAS: ', '', $add);
    //     $variable = substr($add, 0, strpos($add, "III. KETERANGAN"));
    //     $start = 'Nomor:';
    //     $end = '1. Nama'; 
    //     $pattern = sprintf(
    //             '/%s(.+?)%s/ims',
    //             preg_quote($start, '/'), preg_quote($end, '/')
    //     ); 
    //     if (preg_match($pattern, $variable, $matches)) {
    //             list(, $match) = $matches;
    //             $concat = $start.$match;
    //             $nextString = str_replace($concat, "", $variable);   
    //             //echo $nextString;
    //             $this->stringToArrayLagih($nextString,1);
    //         }
    //     else{
    //         return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File different format']]]);
    //     } 
    // }

    // private function stringToArrayLagih($string,$no)
    // {
    //     $jml= $no+1;
    //     for($no;$no<$jml;$no++){ 
    //         $start = ' '.$no.'.';
    //         $end = ' '.$jml.'.'; 
    //         $pattern = sprintf(
    //             '/%s(.+?)%s/ims',
    //             preg_quote($start, '/'), preg_quote($end, '/')
    //         );
    //         $rmv = $start.$end;
    //         if (preg_match($pattern, $string, $matches)) {
    //             list(, $match) = $matches;
    //             $concat = $start.$match;
    //             $nextString = str_replace($concat, "", $string); 
    //             $this->stringToArrayLagih($nextString,$jml); 
    //             $explode = explode(':', $match );  
    //             print_r($explode);
    //             echo '<br><br>';
    //         }
    //     }
    // }

    // private function getByNumber($string,$no)
    // {
    //     $jml= $no+1;
    //     for($no;$no<$jml;$no++){ 
    //         $start = ' '.$no.'.';
    //         $end = ' '.$jml.'.'; 
    //         $pattern = sprintf(
    //             '/%s(.+?)%s/ims',
    //             preg_quote($start, '/'), preg_quote($end, '/')
    //         );
    //         $rmv = $start.$end;
    //         if (preg_match($pattern, $string, $matches)) {
    //             list(, $match) = $matches;
    //             $concat = $start.$match;
    //             $nextString = str_replace($concat, "", $string); 
    //             $this->getByNumber($nextString,$jml);  
    //             // echo $match.'<br><br><br>'; 
    //             $explode = explode(':', $match );
    //             $this->extractArray($explode);
    //             echo '<br><br><br>'; 
    //         }
    //     }
    // }


    
    // public function extractNameFromString(){
    //     $cek =  DataExtract::findOrFail(1);

    //     $this->stringToArrayName($cek->string,1);
    // }

    // private function stringToArrayName($string,$no)
    // {
    //     $jml= $no+1;
    //     for($no;$no<$jml;$no++){ 
    //         $start = ' '.$no.'.';
    //         $end = ' '.$jml.'.'; 
    //         $pattern = sprintf(
    //             '/%s(.+?)%s/ims',
    //             preg_quote($start, '/'), preg_quote($end, '/')
    //         );
    //         $rmv = $start.$end;
    //         if (preg_match($pattern, $string, $matches)) {
    //             list(, $match) = $matches;
    //             $concat = $start.$match;
    //             $nextString = str_replace($concat, "", $string); 
    //             $this->stringToArrayName($nextString,$jml); 
    //             $explode = explode(':', $match ); 
    //             $nama = $this->extractNameFromParam($explode[1],"; N");
    //             if(strlen($nama) < 1) continue;

    //             echo 'Nama : '.$nama;
    //             echo '<br>'; 
    //         }
    //     }
    // } 
    
    // public function extractAliasFromString(){
    //     $cek =  DataExtract::findOrFail(1);

    //     $this->stringToArrayAlias($cek->string,1);
    // }

    // private function stringToArrayAlias($string,$no)
    // {
    //     $jml= $no+1;
    //     for($no;$no<$jml;$no++){ 
    //         $start = ' '.$no.'.';
    //         $end = ' '.$jml.'.'; 
    //         $pattern = sprintf(
    //             '/%s(.+?)%s/ims',
    //             preg_quote($start, '/'), preg_quote($end, '/')
    //         );
    //         $rmv = $start.$end;
    //         if (preg_match($pattern, $string, $matches)) {
    //             list(, $match) = $matches;
    //             $concat = $start.$match;
    //             $nextString = str_replace($concat, "", $string); 
    //             $this->stringToArrayAlias($nextString,$jml); 
    //             $explode = explode(':', $match ); 
    //             $Alias = $this->extractNameFromParam($explode[1],"; K");
    //             if(strlen($Alias) < 1){ 
    //                 $Alias2 = $this->extractNameFromParam($explode[1]."; T");
    //                 if(strlen($Alias) < 1){ 
    //                     echo 'Nama Alias : -';
    //                     echo '<br>'; 

    //                 }else{
    //                     echo 'Nama Alias : '.$Alias2;
    //                     echo '<br>'; 
    //                 }
    //             }else{ 
    //                 echo 'Nama Alias : '.$Alias;
    //                 echo '<br>'; 
    //             }

    //         }
    //     }
    // }

    // private function extractNameFromParam($data,$param)
    // {
    //     return substr($data, 0, strpos($data, $param));
    // }



    // public function extractPdfToArray(){
    //     $path = public_path().'/invoice/book.pdf'; 
    //     $PDFParser = new Parser();
    //     $pdf = $PDFParser->parseFile($path);
    //     $pages  = $pdf->getPages();
    //     $totalPages = count($pages);
    //     $currentPage = 1;
    //     $text = ""; 
    //     $jml = 100;
    //     foreach ($pages as $page) { 
    //         $text .= $page->getText(); 
    //         $currentPage++;
    //     }

    //     $str = preg_replace('/(\v|\s)+/', ' ', $text);
    //     $add = str_replace('KEPOLISIAN NEGARA REPUBLIK INDONESIA MARKAS BESAR Jalan Trunojoyo 3, Kebayoran Baru, Jakarta 12110 “Pro Justitia ” DAFTAR TERDUGA TERORIS DAN ORGANISASI TERORIS', '', $str);
    //     $add = str_replace('I. INDIVIDU: ', '', $add);
    //     $add = str_replace('II. ENTITAS: ', '', $add);
    //     $variable = substr($add, 0, strpos($add, "III. KETERANGAN"));
    //     $start = 'Nomor:';
    //     $end = '1. Nama'; 
    //     $pattern = sprintf(
    //             '/%s(.+?)%s/ims',
    //             preg_quote($start, '/'), preg_quote($end, '/')
    //     ); 
    //     if (preg_match($pattern, $variable, $matches)) {
    //             list(, $match) = $matches;
    //             $concat = $start.$match;
    //             $nextString = str_replace($concat, "", $variable);   
    //             //echo $nextString;
    //             $this->stringToArrayNya($nextString,1);
    //         }
    //     else{
    //         return response()->json(['status'=>422,'data'=>'','message'=>['file_name'=>['File different format']]]);
    //     } 
    // }

    // private function stringToArrayNya($string,$no){
    //     $jml= $no+1;
    //     for($no;$no<$jml;$no++){ 
    //         $start = ' '.$no.'.';
    //         $end = ' '.$jml.'.'; 
    //         $pattern = sprintf(
    //             '/%s(.+?)%s/ims',
    //             preg_quote($start, '/'), preg_quote($end, '/')
    //         );
    //         $rmv = $start.$end;
    //         if (preg_match($pattern, $string, $matches)) {
    //             list(, $match) = $matches;
    //             $concat = $start.$match;
    //             $nextString = str_replace($concat, "", $string); 
    //             $this->stringToArrayNya($nextString,$jml); 
    //             $explode = explode(':', $match );  
    //             echo json_encode($explode);
    //             echo '<br><br>';
    //         }
    //     }
    // }




}