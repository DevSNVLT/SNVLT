<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private $domPdf;


    public function __construct()
    {
        $this->domPdf = new  DomPdf();

        $pdfOptions = new Options();

        $pdfOptions->set('defaultFont', 'arial');

        $this->domPdf->setOptions($pdfOptions);


    }

    public function showPdfFile($html, $titre){
        $entete = "<img src='/public/assets/images/logo_eaux_forets_ci.png' alt='logo' ";
        $this->domPdf->loadHtml($entete);
        $this->domPdf->loadHtml($html);
        $this->domPdf->getPaperSize();
        $this->domPdf->render();
        $this->domPdf->stream($titre.'.pdf', [
            'Attachment'=>true
        ]);
    }
    public function generateBinaryPdf($html){
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }


}