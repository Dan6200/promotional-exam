<?php

namespace App\Http\Controllers;

use App\Services\ExcelProcessor;
use App\Services\DocumentGenerator;
use App\Services\DocumentConverter;
use App\Services\PdfConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Mail\DocumentMail;

class TemplateController extends Controller
{
    protected $excelProcessor;
    protected $documentGenerator;
    protected $documentConverter;
    protected $pdfConverter;

    public function __construct(ExcelProcessor $excelProcessor, DocumentGenerator $documentGenerator, DocumentConverter $documentConverter, PdfConverter $pdfConverter)
    {
        $this->excelProcessor = $excelProcessor;
        $this->documentGenerator = $documentGenerator;
        $this->documentConverter = $documentConverter;
        $this->pdfConverter = $pdfConverter;
    }

    public function index()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel' => 'required|mimes:xlsx,xls',
            'template' => 'required|mimes:docx'
        ]);
        // Store files in the temporary directory
        $excelPath = $request->file('excel')->storeAs('', 'excel_' . time() . '.xlsx', ['disk' => 'temp']);
        $templatePath = $request->file('template')->storeAs('', 'template_' . time() . '.docx', ['disk' => 'temp']);

        return redirect()->route('generate')->with(['excel-path'=> $excelPath, 'template-path' => $templatePath]);
    }

    public function generate()
    {
        $excelPath=session('excel-path');
        $templatePath=session('template-path');
        if (!$excelPath||!$templatePath)
        {
            return redirect('/')->withErrors('Files not uploaded');
        }

        $excelFullPath=null;
        $templateFullPath=null;
        try
        {
            $excelFullPath = sys_get_temp_dir() . '/' . $excelPath;
            $templateFullPath = sys_get_temp_dir() . '/' . $templatePath;
            $sheets = $this->excelProcessor->process($excelFullPath);
            $zipFilePath = $this->sendDocumentsAsEmail($sheets, $templateFullPath);

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }
        catch (\Exception $e)
        {
            Log::error('General error: ' . $e->getMessage());
            return redirect('/')->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
        finally
        {
            @unlink($excelFullPath);
            @unlink($templateFullPath);
        }
    }

    public function download($fileName)
    {
        $filePath = sys_get_temp_dir() . "/$fileName";
        if (file_exists($filePath))
        {
            return response()->download($filePath)->deleteFileAfterSend(true);
        }
        else
        {
            return redirect('/')->withErrors('File not found');
        }
    }

    private function sendDocumentsAsEmail($file, $templatePath)
    {

        foreach ($file as $records)
        {
            foreach ($records as $record)
            {
                // Generate document from template
                $documentPath = $this->documentGenerator->generate($record, $templatePath);
                // Convert document content to html
                $htmlContent = $this->documentConverter->convertWordToHtml($documentPath);
                // Then convert htmlContent to Pdf
                $pdfContent = $this->pdfConverter->convertHtmlToPdf($htmlContent);
                // Name file according record
                $fileName = $this->sanitizeFileName($record['STAFF_NAME']) . '_' . $this->sanitizeFileName($record['STAFF_ID']) . '.pdf';
                $firstName = $record['STAFF_NAME'];
                // Send file as email
                Mail::to($record('EMAIL'))->send(new DocumentMail($firstName, $fileName,$pdfContent));
            }
        }
        else
        {
            throw new \Exception('Failed to send document as email');
        }
    }

    private function sanitizeFileName($fileName)
    {
        return preg_replace('/[^a-zA-Z0-9]/', '_', $fileName);
    }

}
