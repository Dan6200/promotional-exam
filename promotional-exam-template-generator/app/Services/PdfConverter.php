<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Log;

class PdfConverter
{
    public function convertHtmlToPdf($htmlContent)
    {
        $html='
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body {
                    margin: 2%;
                    padding: 0;
                    width: 100%;
                    height: 100%;
                    transform: scale(0.75); /* Adjust the scale value as needed */
                    transform-origin: 0 0;
                }
            </style>
        </head>
        ';
        try
        {
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled',true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html . $htmlContent);
            /*$dompdf->setPaper('A4', 'portrait');*/
            $dompdf->render();
            return $dompdf->output();
        }
        catch (\Exception $e)
        {
            Log::error('Error converting HTML to PDF: ' . $e->getMessage());
            throw new \Exception('Error converting HTML to PDF: ' . $e->getMessage());
        }
    }
}

