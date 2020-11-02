<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set("memory_limit","128M");
/* 
 *  ======================================= 
 *  Author     : Larry Akah
 *  License    : Protected 
 *  Email      : larryakah@gmail.com 
 *  =======================================
 */
use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf {

    function __construct($options = null)
    {
        $CI = & get_instance();
        log_message('Debug', 'PDF class is loaded.');
    }

    public function getPdf($data){
        $pdf = new \Mpdf\Mpdf(['tempDir' => '\resources\uploads\tmp']);
        $pdf->WriteHTML($data);
        return $pdf->Output('', 'S');
    }
    public function getPdfAsFile($data, $filename = 'mini-crm.pdf'){
        $pdf = new \Mpdf\Mpdf();
        ob_start();
        echo $data;
        $d = ob_get_contents();
        ob_end_clean();
        $pdf->WriteHTML($d);
        $pdf->Output($filename, 'D');
    }
}