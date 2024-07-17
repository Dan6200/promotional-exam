<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

class TemplateController extends Controller
{
    public function index() {
        return view('upload');
    }

    public function upload(Request $request) {
        $request->validate([
            'csv' => 'required|mimes:csv,txt'
        ]);
        $csvPath = $request->file('csv')->store('uploads');
        return redirect()->route('generate')->with('csv-path',$csvPath);
    }

    public function generate() {
        $csvPath=session('csv-path');
        if (!$csvPath) {
            return redirect('/')->withErrors('No CSV file uploaded');
        }
        $templatePath = base_path('templates/reportCardTemplateJunior.docx');
        if (!file_exists($templatePath)) {
            return redirect('/')->withErrors('Template file not found.');
        }

        try {
            $csv = Reader::createFromPath(storage_path('app/' . $csvPath), 'r');
            $csv->setHeaderOffset(0);
            $records = (new Statement())->process($csv);


            $generatedDocuments = [];

            foreach ($records as $record) {
                Log::info ('Processing record: ', $record);

                // Clone the template processor for each record
                $tempDoc = new TemplateProcessor($templatePath);

                // Replace placeholders in the template
                foreach ($record as $field => $value) {
                    try {
                        $tempDoc->setValue($field, $value);
                    } catch (\Exception $e) {
                        Log::error("Error setting value for field $field: " . $e->getMessage());
                        return redirect('/')->withErrors(['error' => 'Error setting value for field ' . $field . ': ' . $e->getMessage()]);
                    }
                }


                // Save the generated document to a temporary file
                $tempWordPath=tempnam(sys_get_temp_dir(), 'word') . '.docx';
                $outputPath = storage_path('app/generated_' . $record['STAFF_NAME'] . '_' . $record['STAFF_ID'] . '.pdf');
                try {
                    $tempDoc->saveAs($tempWordPath);
                    $this->convertWordTemplateToPdf($tempWordPath, $outputPath);
                } catch (\Exception $e) {
                    Log::error('Error saving document: ' . $e->getMessage());
                    return redirect('/')->withErrors(['error' => 'Error saving document: ' . $e->getMessage()]);
                }
                $generatedDocuments[]=$outputPath;
            }
            return view('generated', ['documents' => $generatedDocuments]);
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return redirect('/')->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    private function convertWordTemplateToPdf($tempWordPath, $pdfFilePath) {
        try {
            $phpWord = IOFactory::load($tempWordPath);
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            ob_start();
            $htmlWriter->save("php://output");
            $htmlContent = ob_get_clean();

            // Convert HTML to PDF
            $dompdf = new Dompdf();
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf->setOptions($options);
            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            // Render the HTML as PDF
            $dompdf->render();
            // Output the generated PDF to a file
            file_put_contents($pdfFilePath, $dompdf->output());
        } catch (\Exception $e) {
            Log::error('Error Converting To Pdf: ' . $e->getMessage());
            return redirect('/')->withErrors(['error'=>'Error trying to convert HTML to PDF: ' . $e->getMessage()]);
        }
    }
}
