<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable; 
use Smalot\PdfParser\Parser;
use PHPExcel; 
use PHPExcel_IOFactory;
 

use App\Models\UkkDttot; 
use App\Models\DataExtract; 
 

class DttotFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ukkDttot = null;

    public function __construct(UkkDttot $ukkDttot)
    {
        $this->ukkDttot = $ukkDttot;
    }

    public function handle()
    {    
       $this->updateDttot('processing','-');
       $this->extractPdfToArray();
    }

    private function updateDttot($status,$remarks=''){
        UkkDttot::where('id', $this->ukkDttot['id'])->update(['status' => $status, 'remarks' => $remarks]);
    }

    private function extractPdfToArray(){
        $cek = DataExtract::where('dokumen_dttot_id',$this->ukkDttot['id'])->first();
        if(!$cek)
        { 
            $path = public_path().'/dokumen-dttot/'.$this->ukkDttot['dokumen']; 
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
                    $this->stringToArrayNya($nextString,1); 
            }else{
                $this->updateDttot('error','File different format'); 
            } 
        }else{
            $this->updateDttot('error','double document data');
        }
    }

    private function stringToArrayNya($string,$no){
        ini_set('max_execution_time', 0); // 0 = Unlimited
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
                DataExtract::create(array('dokumen_dttot_id'=>$this->ukkDttot['id'],'string'=>json_encode($explode)));
                $this->stringToArrayNya($nextString,$jml);  
            }else{
                $this->updateDttot('complete','-');
            }
        }
    }


}