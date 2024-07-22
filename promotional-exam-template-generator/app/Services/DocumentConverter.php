<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Aspose\Words\WordsApi;
use Aspose\Words\Model\Requests\ConvertDocumentRequest;

class DocumentConverter
{
    protected $wordsApi;

    public function __construct()
    {
        $this->wordsApi = new WordsApi(env('ASPOSE_API_CID'), env('ASPOSE_API_SEC'));
    }

    public function convertWordToHtml($documentPath)
    {
        $tempFilePath=null;
        try
        {
            $request = new ConvertDocumentRequest(
                $documentPath,
                'html',
                null,
                null,
                null,
                null
            );

            $htmlContent = $this->wordsApi->convertDocument($request);
            return $htmlContent;
        }
        catch (\Exception $e)
        {
            Log::error("Error converting document {$tempFilePath} to HTML: " . $e->getMessage());
            /*throw new \Exception("Error converting document {$tempFilePath} to HTML: " . $e->getMessage());*/
        }
        finally
        {
            // Ensure the temporary file is removed
            if ($tempFilePath && file_exists($tempFilePath)) {
                unlink($documentPath);
            }
        }
    }
}
